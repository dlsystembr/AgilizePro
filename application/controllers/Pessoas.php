<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pessoas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pessoas_model');
        $this->data['menuPessoas'] = 'pessoas';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vPessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar pessoas.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('pessoas/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->Pessoas_model->count('pessoas');
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url('index.php/pessoas')."?pesquisa={$pesquisa}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->Pessoas_model->get('pessoas', '*', $pesquisa, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'pessoas/pessoas';
        return $this->layout();
    }

    public function verificarCpfCnpj()
    {
        $cpfCnpj = $this->input->post('cpfcnpj');
        
        if (!$cpfCnpj) {
            echo json_encode(['exists' => false]);
            return;
        }
        
        // Remover formatação
        $cpfCnpj = preg_replace('/[^0-9]/', '', $cpfCnpj);
        
        // Buscar pessoa com este CPF/CNPJ
        $this->db->where('PES_CPFCNPJ', $cpfCnpj);
        $pessoa = $this->db->get('pessoas')->row();
        
        if ($pessoa) {
            echo json_encode([
                'exists' => true,
                'id' => $pessoa->PES_ID,
                'nome' => $pessoa->PES_NOME,
                'razao_social' => $pessoa->PES_RAZAO_SOCIAL
            ]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    public function listarVendedores()
    {
        // Buscar todas as pessoas que são vendedores ativos
        $this->db->select('p.PES_ID, p.PES_NOME, p.PES_RAZAO_SOCIAL');
        $this->db->from('pessoas p');
        $this->db->join('vendedores v', 'v.PES_ID = p.PES_ID', 'inner');
        $this->db->where('v.VEN_SITUACAO', 1);
        $this->db->where('p.PES_SITUACAO', 1);
        $this->db->order_by('p.PES_NOME', 'ASC');
        $vendedores = $this->db->get()->result();
        
        echo json_encode($vendedores);
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aPessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar pessoas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('PES_CPFCNPJ', 'CPF/CNPJ', 'required|trim');
        $this->form_validation->set_rules('PES_NOME', 'Nome', 'required|trim');
        $this->form_validation->set_rules('PES_CODIGO', 'Código', 'trim');
        $this->form_validation->set_rules('PES_FISICO_JURIDICO', 'Tipo (F/J)', 'required|in_list[F,J]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            // Gera código automaticamente se vazio
            $codigo = trim((string) $this->input->post('PES_CODIGO', true));
            if ($codigo === '') {
                $row = $this->db->query("SELECT MAX(CAST(PES_CODIGO AS UNSIGNED)) AS max_cod FROM pessoas WHERE PES_CODIGO REGEXP '^[0-9]+$'")->row();
                $next = isset($row->max_cod) && $row->max_cod !== null ? ((int) $row->max_cod) + 1 : 1;
                $codigo = (string) $next;
            }

            $data = [
                'PES_CPFCNPJ' => set_value('PES_CPFCNPJ'),
                'PES_NOME' => set_value('PES_NOME'),
                'PES_RAZAO_SOCIAL' => set_value('PES_RAZAO_SOCIAL'),
                'PES_CODIGO' => $codigo,
                'PES_FISICO_JURIDICO' => set_value('PES_FISICO_JURIDICO'),
                'PES_NASCIMENTO_ABERTURA' => set_value('PES_NASCIMENTO_ABERTURA') ?: null,
                'PES_NACIONALIDADES' => set_value('PES_NACIONALIDADES'),
                'PES_RG' => set_value('PES_RG'),
                'PES_ORGAO_EXPEDIDOR' => set_value('PES_ORGAO_EXPEDIDOR'),
                'PES_SEXO' => set_value('PES_SEXO'),
                'PES_ESTADO_CIVIL' => set_value('PES_ESTADO_CIVIL') ?: null,
                'PES_ESCOLARIDADE' => set_value('PES_ESCOLARIDADE') ?: null,
                'PES_PROFISSAO' => set_value('PES_PROFISSAO'),
                'PES_OBSERVACAO' => set_value('PES_OBSERVACAO'),
                'PES_SITUACAO' => $this->input->post('PES_SITUACAO') !== null ? (int) $this->input->post('PES_SITUACAO') : 1,
            ];

            if ($this->Pessoas_model->add('pessoas', $data)) {
                $pessoaId = $this->db->insert_id();

                // Salvar telefones, se houver
                $tipos = $this->input->post('TEL_TIPO');
                $ddds = $this->input->post('TEL_DDD');
                $numeros = $this->input->post('TEL_NUMERO');
                $obs = $this->input->post('TEL_OBSERVACAO');
                if (is_array($tipos) && is_array($ddds) && is_array($numeros)) {
                    $count = max(count($tipos), count($ddds), count($numeros));
                    for ($i = 0; $i < $count; $i++) {
                        $tipo = isset($tipos[$i]) ? $tipos[$i] : '';
                        $ddd = isset($ddds[$i]) ? preg_replace('/\D/', '', $ddds[$i]) : '';
                        $numero = isset($numeros[$i]) ? preg_replace('/\D/', '', $numeros[$i]) : '';
                        $obst = isset($obs[$i]) ? $obs[$i] : null;
                        if ($ddd !== '' && $numero !== '' && in_array($tipo, ['Celular','Comercial','Residencial','Whatsapp','Outros'])) {
                            $this->db->insert('telefones', [
                                'PES_ID' => $pessoaId,
                                'TEL_TIPO' => $tipo,
                                'TEL_DDD' => substr($ddd, 0, 3),
                                'TEL_NUMERO' => substr($numero, 0, 12),
                                'TEL_OBSERVACAO' => $obst,
                            ]);
                        }
                    }
                }

                // Salvar emails, se houver (novos campos EMAIL_*)
                $emailTipos = $this->input->post('EMAIL_TIPO');
                $emailNomes = $this->input->post('EMAIL_NOME');
                $emailEnderecos = $this->input->post('EMAIL_ENDERECO');
                if (is_array($emailEnderecos)) {
                    $count = count($emailEnderecos);
                    for ($i = 0; $i < $count; $i++) {
                        $tipo = isset($emailTipos[$i]) ? $emailTipos[$i] : 'Comercial';
                        $nome = isset($emailNomes[$i]) ? trim($emailNomes[$i]) : null;
                        $email = isset($emailEnderecos[$i]) ? trim($emailEnderecos[$i]) : '';
                        if ($email !== '') {
                            $this->db->insert('emails', [
                                'PES_ID' => $pessoaId,
                                'EML_TIPO' => $tipo,
                                'EML_EMAIL' => $email,
                                'EML_NOME' => $nome,
                            ]);
                        }
                    }
                }

                // Salvar endereços, se houver (novos campos END_TIPO, END_CEP, END_CIDADE, END_UF, END_BAIRRO)
                $tiposEnd = $this->input->post('END_TIPO');
                $ceps = $this->input->post('END_CEP');
                $logradouros = $this->input->post('END_LOGRADOURO');
                $numerosEnd = $this->input->post('END_NUMERO');
                $complementos = $this->input->post('END_COMPLEMENTO');
                $bairrosTexto = $this->input->post('END_BAIRRO');
                $cidadesTexto = $this->input->post('END_CIDADE');
                $ufs = $this->input->post('END_UF');
                
                if (is_array($logradouros)) {
                    $count = count($logradouros);
                    $insertedEnderecoIds = [];
                    for ($i = 0; $i < $count; $i++) {
                        $logradouro = isset($logradouros[$i]) ? trim($logradouros[$i]) : '';
                        if ($logradouro !== '') {
                            // Buscar/criar IDs de estado, município e bairro
                            $uf = isset($ufs[$i]) ? trim($ufs[$i]) : '';
                            $cidadeNome = isset($cidadesTexto[$i]) ? trim($cidadesTexto[$i]) : '';
                            $bairroNome = isset($bairrosTexto[$i]) ? trim($bairrosTexto[$i]) : '';
                            
                            $estId = null;
                            $munId = null;
                            $baiId = null;
                            
                            // Buscar estado pela UF
                            if ($uf !== '') {
                                $estado = $this->db->select('EST_ID')->where('EST_UF', $uf)->get('estados')->row();
                                if ($estado) {
                                    $estId = (int) $estado->EST_ID;
                                }
                            }
                            
                            // Buscar município pelo nome e estado
                            if ($cidadeNome !== '' && $estId) {
                                $municipio = $this->db->select('MUN_ID')->where(['MUN_NOME' => $cidadeNome, 'EST_ID' => $estId])->get('municipios')->row();
                                if ($municipio) {
                                    $munId = (int) $municipio->MUN_ID;
                                }
                            }
                            
                            // Buscar/criar bairro
                            if ($bairroNome !== '' && $munId) {
                                $bairro = $this->db->select('BAI_ID')->where(['BAI_NOME' => $bairroNome, 'MUN_ID' => $munId])->get('bairros')->row();
                                if ($bairro) {
                                    $baiId = (int) $bairro->BAI_ID;
                                } else {
                                    // Criar bairro se não existir
                                    $this->db->insert('bairros', ['MUN_ID' => $munId, 'BAI_NOME' => $bairroNome]);
                                    $baiId = (int) $this->db->insert_id();
                                }
                            }
                            
                            // Mapear tipo de endereço
                            $tipoEnd = isset($tiposEnd[$i]) ? $tiposEnd[$i] : 'Comercial';
                            $tipoEndBanco = 'Geral'; // Padrão
                            if ($tipoEnd == 'Cobrança') $tipoEndBanco = 'Cobranca';
                            else if ($tipoEnd == 'Entrega') $tipoEndBanco = 'Entrega';
                            else if ($tipoEnd == 'Faturamento') $tipoEndBanco = 'Faturamento';
                            
                            $dataEnd = [
                                'PES_ID' => $pessoaId,
                                'EST_ID' => $estId,
                                'MUN_ID' => $munId,
                                'BAI_ID' => $baiId,
                                'END_TIPO_ENDENRECO' => $tipoEndBanco,
                                'END_LOGRADOURO' => $logradouro,
                                'END_NUMERO' => isset($numerosEnd[$i]) ? $numerosEnd[$i] : null,
                                'END_COMPLEMENTO' => isset($complementos[$i]) ? $complementos[$i] : null,
                                'END_CEP' => isset($ceps[$i]) ? preg_replace('/\D/', '', $ceps[$i]) : null,
                                'END_SITUACAO' => 1,
                            ];
                            $this->db->insert('enderecos', $dataEnd);
                            $insertedEnderecoIds[$i] = (int) $this->db->insert_id();
                        } else {
                            $insertedEnderecoIds[$i] = null;
                        }
                    }
                }

                // Salvar documentos, se houver
                $docTipos = $this->input->post('DOC_TIPO_DOCUMENTO');
                $docNumeros = $this->input->post('DOC_NUMERO');
                $docOrgaos = $this->input->post('DOC_ORGAO_EXPEDIDOR');
                $docEndIdxs = $this->input->post('DOC_ENDE_IDX');
                $docNaturezas = $this->input->post('DOC_NATUREZA_CONTRIBUINTE');
                if (is_array($docTipos) || is_array($docNumeros) || is_array($docOrgaos)) {
                    $max = max(
                        is_array($docTipos) ? count($docTipos) : 0,
                        is_array($docNumeros) ? count($docNumeros) : 0,
                        is_array($docOrgaos) ? count($docOrgaos) : 0,
                        is_array($docEndIdxs) ? count($docEndIdxs) : 0,
                        is_array($docNaturezas) ? count($docNaturezas) : 0
                    );
                    for ($i = 0; $i < $max; $i++) {
                        $tipo = is_array($docTipos) && isset($docTipos[$i]) ? trim((string) $docTipos[$i]) : '';
                        $numero = is_array($docNumeros) && isset($docNumeros[$i]) ? trim((string) $docNumeros[$i]) : '';
                        $orgao = is_array($docOrgaos) && isset($docOrgaos[$i]) ? trim((string) $docOrgaos[$i]) : null;
                        $natureza = is_array($docNaturezas) && isset($docNaturezas[$i]) ? trim((string) $docNaturezas[$i]) : null;
                        if ($tipo !== '' && $numero !== '') {
                            $endeId = null;
                            if (is_array($docEndIdxs) && isset($docEndIdxs[$i]) && $docEndIdxs[$i] !== '') {
                                $idx = (int) $docEndIdxs[$i];
                                if (isset($insertedEnderecoIds) && array_key_exists($idx, $insertedEnderecoIds) && $insertedEnderecoIds[$idx]) {
                                    $endeId = (int) $insertedEnderecoIds[$idx];
                                }
                            }
                            $this->db->insert('documentos', [
                                'PES_ID' => $pessoaId,
                                'DOC_TIPO_DOCUMENTO' => mb_substr($tipo, 0, 60),
                                'ENDEID' => $endeId,
                                'DOC_ORGAO_EXPEDIDOR' => $orgao !== '' ? mb_substr($orgao, 0, 60) : null,
                                'DOC_NUMERO' => mb_substr($numero, 0, 60),
                                'DOC_NATUREZA_CONTRIBUINTE' => in_array($natureza, ['Contribuinte','Não Contribuinte']) ? $natureza : null,
                            ]);
                        }
                    }
                }

                // Salvar dados de Cliente (se marcado)
                $isCliente = (bool) $this->input->post('CLN_ENABLE');
                if ($isCliente) {
                    $cliente = [
                        'PES_ID' => $pessoaId,
                        'CLN_LIMITE_CREDITO' => $this->input->post('CLN_LIMITE_CREDITO') !== null ? str_replace([','], ['.'], $this->input->post('CLN_LIMITE_CREDITO')) : null,
                        'CLN_SITUACAO' => $this->input->post('CLN_SITUACAO') !== null ? (int)$this->input->post('CLN_SITUACAO') : 1,
                        'CLN_COMPRAR_APRAZO' => $this->input->post('CLN_COMPRAR_APRAZO') ? 1 : 0,
                        'CLN_BLOQUEIO_FINANCEIRO' => $this->input->post('CLN_BLOQUEIO_FINANCEIRO') ? 1 : 0,
                        'CLN_DIAS_CARENCIA' => $this->input->post('CLN_DIAS_CARENCIA') !== null ? (int)$this->input->post('CLN_DIAS_CARENCIA') : null,
                        'CLN_EMITIR_NFE' => $this->input->post('CLN_EMITIR_NFE') ? 1 : 0,
                        'CLN_DATA_CADASTRO' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('clientes', $cliente);
                    $clienteId = $this->db->insert_id();
                    
                    // Salvar vendedores permitidos para este cliente
                    $vendedoresPermitidosPesId = $this->input->post('CLV_VEN_PES_ID');
                    $vendedorPadraoPesId = $this->input->post('CLV_PADRAO');
                    
                    if (is_array($vendedoresPermitidosPesId)) {
                        foreach ($vendedoresPermitidosPesId as $vendedorPesId) {
                            if ($vendedorPesId) {
                                // Buscar VEN_ID pela PES_ID
                                $this->db->select('VEN_ID');
                                $this->db->where('PES_ID', $vendedorPesId);
                                $vendedor = $this->db->get('vendedores')->row();
                                
                                if ($vendedor) {
                                    $isPadrao = ($vendedorPadraoPesId && $vendedorPadraoPesId == $vendedorPesId) ? 1 : 0;
                                    
                                    $this->db->insert('clientes_vendedores', [
                                        'CLN_ID' => $clienteId,
                                        'VEN_ID' => $vendedor->VEN_ID,
                                        'CLV_PADRAO' => $isPadrao
                                    ]);
                                }
                            }
                        }
                    }
                }

                // Salvar dados de Vendedor (se marcado)
                $isVendedor = (bool) $this->input->post('VEN_ENABLE');
                if ($isVendedor) {
                    $vendedor = [
                        'PES_ID' => $pessoaId,
                        'VEN_PERCENTUAL_COMISSAO' => $this->input->post('VEN_PERCENTUAL_COMISSAO') !== null ? str_replace([','], ['.'], $this->input->post('VEN_PERCENTUAL_COMISSAO')) : null,
                        'VEN_TIPO_COMISSAO' => $this->input->post('VEN_TIPO_COMISSAO'),
                        'VEN_META_MENSAL' => $this->input->post('VEN_META_MENSAL') !== null ? str_replace([','], ['.'], $this->input->post('VEN_META_MENSAL')) : null,
                        'VEN_SITUACAO' => 1, // Sempre ativo ao criar
                    ];
                    $this->db->insert('vendedores', $vendedor);
                }

                $this->session->set_flashdata('success', 'Pessoa adicionada com sucesso!');
                log_info('Adicionou uma pessoa.');
                redirect(site_url('pessoas/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }
        // Carrega estados para o modal de endereço
        $this->data['estados'] = $this->db->order_by('EST_UF', 'ASC')->get('estados')->result();

        $this->data['view'] = 'pessoas/adicionarPessoa';
        return $this->layout();
    }

    // Retorna municípios por EST_ID (JSON)
    public function getMunicipios()
    {
        $estId = (int) $this->input->get('est_id');
        if (! $estId) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }
        $rows = $this->db->select('MUN_ID, MUN_NOME')->from('municipios')->where('EST_ID', $estId)->order_by('MUN_NOME', 'ASC')->get()->result();
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }

    // Retorna bairros por MUN_ID (JSON)
    public function getBairros()
    {
        $munId = (int) $this->input->get('mun_id');
        if (! $munId) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }
        $rows = $this->db->select('BAI_ID, BAI_NOME')->from('bairros')->where('MUN_ID', $munId)->order_by('BAI_NOME', 'ASC')->get()->result();
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }

    public function editar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Pessoa não encontrada.');
            redirect(base_url('index.php/pessoas'));
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'ePessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar pessoas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('PES_CPFCNPJ', 'CPF/CNPJ', 'required|trim');
        $this->form_validation->set_rules('PES_NOME', 'Nome', 'required|trim');
        $this->form_validation->set_rules('PES_CODIGO', 'Código', 'required|trim');
        $this->form_validation->set_rules('PES_FISICO_JURIDICO', 'Tipo (F/J)', 'required|in_list[F,J]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'PES_CPFCNPJ' => $this->input->post('PES_CPFCNPJ'),
                'PES_NOME' => $this->input->post('PES_NOME'),
                'PES_RAZAO_SOCIAL' => $this->input->post('PES_RAZAO_SOCIAL'),
                'PES_CODIGO' => $this->input->post('PES_CODIGO'),
                'PES_FISICO_JURIDICO' => $this->input->post('PES_FISICO_JURIDICO'),
                'PES_NASCIMENTO_ABERTURA' => $this->input->post('PES_NASCIMENTO_ABERTURA') ?: null,
                'PES_NACIONALIDADES' => $this->input->post('PES_NACIONALIDADES'),
                'PES_RG' => $this->input->post('PES_RG'),
                'PES_ORGAO_EXPEDIDOR' => $this->input->post('PES_ORGAO_EXPEDIDOR'),
                'PES_SEXO' => $this->input->post('PES_SEXO'),
                'PES_ESTADO_CIVIL' => $this->input->post('PES_ESTADO_CIVIL') ?: null,
                'PES_ESCOLARIDADE' => $this->input->post('PES_ESCOLARIDADE') ?: null,
                'PES_PROFISSAO' => $this->input->post('PES_PROFISSAO'),
                'PES_OBSERVACAO' => $this->input->post('PES_OBSERVACAO'),
                'PES_SITUACAO' => $this->input->post('PES_SITUACAO') !== null ? (int) $this->input->post('PES_SITUACAO') : 1,
            ];

            if ($this->Pessoas_model->edit('pessoas', $data, 'PES_ID', $id)) {
                $this->session->set_flashdata('success', 'Pessoa editada com sucesso!');
                log_info('Alterou uma pessoa. ID ' . $id);
                redirect(site_url('pessoas/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->Pessoas_model->getById($id);
        
        // Buscar telefones (se tabela existir)
        if ($this->db->table_exists('telefones')) {
            $this->data['telefones'] = $this->db->where('PES_ID', $id)->get('telefones')->result();
        } else {
            $this->data['telefones'] = [];
        }
        
        // Buscar emails (se tabela existir)
        if ($this->db->table_exists('emails')) {
            $this->data['emails'] = $this->db->where('PES_ID', $id)->get('emails')->result();
        } else {
            $this->data['emails'] = [];
        }
        
        // Buscar endereços (se tabela existir)
        if ($this->db->table_exists('enderecos')) {
            $this->db->select('e.*, est.EST_UF, mun.MUN_NOME, bai.BAI_NOME');
            $this->db->from('enderecos e');
            $this->db->join('estados est', 'est.EST_ID = e.EST_ID', 'left');
            $this->db->join('municipios mun', 'mun.MUN_ID = e.MUN_ID', 'left');
            $this->db->join('bairros bai', 'bai.BAI_ID = e.BAI_ID', 'left');
            $this->db->where('e.PES_ID', $id);
            $this->data['enderecos'] = $this->db->get()->result();
            
            // Debug temporário
            error_log('Endereços encontrados: ' . count($this->data['enderecos']));
            if (!empty($this->data['enderecos'])) {
                error_log('Primeiro endereço: ' . print_r($this->data['enderecos'][0], true));
            }
        } else {
            $this->data['enderecos'] = [];
            error_log('Tabela enderecos não existe');
        }
        
        // Buscar documentos (se tabela existir)
        if ($this->db->table_exists('documentos')) {
            $this->data['documentos'] = $this->db->where('PES_ID', $id)->get('documentos')->result();
        } else {
            $this->data['documentos'] = [];
        }
        
        // Buscar tipos de pessoa vinculados (se tabela existir)
        if ($this->db->table_exists('pessoa_tipos')) {
            // Tentar diferentes estruturas de colunas
            $query = $this->db->get_where('pessoa_tipos', ['PES_ID' => $id]);
            
            // Se não encontrou com PES_ID, tentar com pessoa_id ou pt_pessoa_id
            if ($query->num_rows() == 0) {
                $query = $this->db->get_where('pessoa_tipos', ['pessoa_id' => $id]);
            }
            if ($query->num_rows() == 0) {
                $query = $this->db->get_where('pessoa_tipos', ['pt_pessoa_id' => $id]);
            }
            
            $tiposVinculados = $query->result();
            
            error_log('Tipos vinculados encontrados: ' . count($tiposVinculados));
            if (!empty($tiposVinculados)) {
                error_log('Primeiro tipo: ' . print_r($tiposVinculados[0], true));
            }
            
            // Extrair IDs de tipos, tentando diferentes nomes de colunas
            $this->data['tipos_vinculados'] = [];
            foreach ($tiposVinculados as $vinculo) {
                if (isset($vinculo->TPP_ID)) {
                    $this->data['tipos_vinculados'][] = (object)['TPP_ID' => $vinculo->TPP_ID];
                } elseif (isset($vinculo->tipo_id)) {
                    $this->data['tipos_vinculados'][] = (object)['TPP_ID' => $vinculo->tipo_id];
                } elseif (isset($vinculo->pt_tipo_id)) {
                    $this->data['tipos_vinculados'][] = (object)['TPP_ID' => $vinculo->pt_tipo_id];
                } elseif (isset($vinculo->TP_ID)) {
                    $this->data['tipos_vinculados'][] = (object)['TPP_ID' => $vinculo->TP_ID];
                }
            }
            
            error_log('Tipos vinculados processados: ' . print_r($this->data['tipos_vinculados'], true));
        } else {
            $this->data['tipos_vinculados'] = [];
            error_log('Tabela pessoa_tipos não existe');
        }
        
        // Buscar dados de cliente (se tabela existir)
        if ($this->db->table_exists('clientes')) {
            $this->data['cliente'] = $this->db->where('PES_ID', $id)->get('clientes')->row();
            
            // Se for cliente, buscar vendedores permitidos
            if ($this->data['cliente'] && $this->db->table_exists('clientes_vendedores')) {
                $this->db->select('cv.*, v.PES_ID as VEN_PES_ID, p.PES_NOME as VEN_NOME');
                $this->db->from('clientes_vendedores cv');
                $this->db->join('vendedores v', 'v.VEN_ID = cv.VEN_ID');
                $this->db->join('pessoas p', 'p.PES_ID = v.PES_ID');
                $this->db->where('cv.CLN_ID', $this->data['cliente']->CLN_ID);
                $this->data['vendedores_permitidos'] = $this->db->get()->result();
            } else {
                $this->data['vendedores_permitidos'] = [];
            }
        } else {
            $this->data['cliente'] = null;
            $this->data['vendedores_permitidos'] = [];
        }
        
        // Buscar dados de vendedor (se tabela existir)
        if ($this->db->table_exists('vendedores')) {
            $this->data['vendedor'] = $this->db->where('PES_ID', $id)->get('vendedores')->row();
        } else {
            $this->data['vendedor'] = null;
        }
        
        $this->data['view'] = 'pessoas/editarPessoa';
        return $this->layout();
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dPessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir pessoas.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir pessoa.');
            redirect(site_url('pessoas/gerenciar/'));
        }

        $this->Pessoas_model->delete('pessoas', 'PES_ID', $id);
        log_info('Removeu uma pessoa. ID ' . $id);

            $this->session->set_flashdata('success', 'Pessoa excluída com sucesso!');
        redirect(site_url('pessoas/gerenciar/'));
    }
} 
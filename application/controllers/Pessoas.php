<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pessoas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pessoas_model');
        $this->load->model('Tipos_clientes_model');
        $this->data['menuPessoas'] = 'pessoas';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vPessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar pessoas.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('pessoas/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->Pessoas_model->count('pessoas');
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url('index.php/pessoas') . "?pesquisa={$pesquisa}";
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
        // Não remover formatação pois no banco está formatado
        // $cpfCnpj = preg_replace('/[^0-9]/', '', $cpfCnpj);

        // Buscar pessoa com este CPF/CNPJ do mesmo tenant
        $this->db->where('pes_cpfcnpj', $cpfCnpj);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $pessoa = $this->db->get('pessoas')->row();

        if ($pessoa) {
            echo json_encode([
                'exists' => true,
                'id' => $pessoa->pes_id,
                'nome' => $pessoa->pes_nome,
                'razao_social' => $pessoa->pes_razao_social
            ]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    /**
     * Proxy para busca de CNPJ na API pública (publica.cnpj.ws).
     * Evita CORS e timeout no navegador: a requisição é feita pelo servidor.
     * Uso: GET index.php/pessoas/buscarCnpjApi/24982773000189 ou ?cnpj=24982773000189
     */
    public function buscarCnpjApi()
    {
        header('Content-Type: application/json; charset=utf-8');
        $cnpj = $this->input->get('cnpj') ?: $this->uri->segment(3);
        $cnpj = preg_replace('/\D/', '', (string) $cnpj);
        if (strlen($cnpj) !== 14) {
            echo json_encode(['erro' => 'CNPJ inválido. Informe 14 dígitos.']);
            return;
        }
        $url = 'https://publica.cnpj.ws/cnpj/' . $cnpj;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            echo json_encode(['erro' => 'Erro ao conectar na API: ' . $err]);
            return;
        }
        if ($httpCode !== 200) {
            echo json_encode(['erro' => 'API retornou status ' . $httpCode, 'razao_social' => null]);
            return;
        }
        echo $response ?: json_encode(['erro' => 'Resposta vazia da API']);
    }

    public function listarVendedores()
    {
        // Buscar todas as pessoas que são vendedores ativos
        $this->db->select('p.pes_id, p.pes_nome, p.pes_razao_social');
        $this->db->from('pessoas p');
        $this->db->join('vendedores v', 'v.pes_id = p.pes_id', 'inner');
        $this->db->where('v.ven_situacao', 1);
        $this->db->where('p.pes_situacao', 1);
        $this->db->order_by('p.pes_nome', 'ASC');
        $vendedores = $this->db->get()->result();

        echo json_encode($vendedores);
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aPessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar pessoas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Validação customizada para CPF/CNPJ considerando ten_id
        $this->form_validation->set_rules('pes_cpfcnpj', 'CPF/CNPJ', 'required|trim|callback_check_cpfcnpj_unique');
        $this->form_validation->set_rules('pes_nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('pes_codigo', 'Código', 'trim');
        $this->form_validation->set_rules('pes_fisico_juridico', 'Tipo (F/J)', 'required|in_list[F,J]');
        $this->form_validation->set_rules('pes_regime_tributario', 'Regime Tributário', 'trim|callback_check_regime_tributario_cnpj');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            // Gera código automaticamente se vazio
            $codigo = trim((string) $this->input->post('pes_codigo', true));
            if ($codigo === '') {
                $row = $this->db->query("SELECT MAX(CAST(pes_codigo AS UNSIGNED)) AS max_cod FROM pessoas WHERE pes_codigo REGEXP '^[0-9]+$'")->row();
                $next = isset($row->max_cod) && $row->max_cod !== null ? ((int) $row->max_cod) + 1 : 1;
                $codigo = (string) $next;
            }

            $data = [
                'ten_id' => $this->session->userdata('ten_id'),
                'pes_cpfcnpj' => set_value('pes_cpfcnpj'),
                'pes_nome' => set_value('pes_nome'),
                'pes_razao_social' => set_value('pes_razao_social'),
                'pes_codigo' => $codigo,
                'pes_fisico_juridico' => set_value('pes_fisico_juridico'),
                'pes_regime_tributario' => $this->normalizarRegimeTributario($this->input->post('pes_regime_tributario')),
                'pes_nascimento_abertura' => set_value('pes_nascimento_abertura') ?: null,
                'pes_nacionalidades' => set_value('pes_nacionalidades'),
                'pes_rg' => set_value('pes_rg'),
                'pes_orgao_expedidor' => set_value('pes_orgao_expedidor'),
                'pes_sexo' => set_value('pes_sexo'),
                'pes_estado_civil' => set_value('pes_estado_civil') ?: null,
                'pes_escolaridade' => set_value('pes_escolaridade') ?: null,
                'pes_profissao' => set_value('pes_profissao'),
                'pes_observacao' => set_value('pes_observacao'),
                'pes_situacao' => $this->input->post('pes_situacao') !== null ? (int) $this->input->post('pes_situacao') : 1,
            ];

            // Validar: Inscrição Estadual exige endereço vinculado
            $docTiposPre = $this->input->post('doc_tipo_documento');
            $docEndIdxsPre = $this->input->post('DOC_ENDE_IDX');
            if (is_array($docTiposPre)) {
                foreach ($docTiposPre as $i => $tipoPre) {
                    $tipoPre = trim((string) $tipoPre);
                    if ($tipoPre !== '' && stripos($tipoPre, 'Inscrição Estadual') !== false) {
                        $idxPre = isset($docEndIdxsPre[$i]) ? trim((string) $docEndIdxsPre[$i]) : '';
                        if ($idxPre === '') {
                            $this->data['custom_error'] = '<div class="form_error"><p>Para documento tipo Inscrição Estadual é obrigatório vincular ao endereço.</p></div>';
                            break;
                        }
                    }
                }
            }
            // Validar: é obrigatório um endereço padrão quando houver endereços
            $logradourosPre = $this->input->post('end_logradouro');
            $temEnderecosPre = is_array($logradourosPre) && count(array_filter(array_map('trim', $logradourosPre))) > 0;
            $enderecoPadraoPre = trim((string) $this->input->post('endereco_padrao'));
            if (empty($this->data['custom_error']) && $temEnderecosPre && $enderecoPadraoPre === '') {
                $this->data['custom_error'] = '<div class="form_error"><p>É obrigatório definir um endereço padrão.</p></div>';
            }

            if (empty($this->data['custom_error'])) {
                $insertOk = $this->Pessoas_model->add('pessoas', $data);
                if ($insertOk) {
                    $pessoaId = is_numeric($insertOk) ? (int) $insertOk : (int) $this->db->insert_id();

                // Salvar telefones, se houver
                $tipos = $this->input->post('tel_tipo');
                $ddds = $this->input->post('tel_ddd');
                $numeros = $this->input->post('tel_numero');
                $obs = $this->input->post('tel_observacao');
                if (is_array($tipos) && is_array($ddds) && is_array($numeros)) {
                    $count = max(count($tipos), count($ddds), count($numeros));
                    for ($i = 0; $i < $count; $i++) {
                        $tipo = isset($tipos[$i]) ? $tipos[$i] : '';
                        $ddd = isset($ddds[$i]) ? preg_replace('/\D/', '', $ddds[$i]) : '';
                        $numero = isset($numeros[$i]) ? preg_replace('/\D/', '', $numeros[$i]) : '';
                        $obst = isset($obs[$i]) ? $obs[$i] : null;
                        if ($ddd !== '' && $numero !== '' && in_array($tipo, ['Celular', 'Comercial', 'Residencial', 'Whatsapp', 'Outros'])) {
                            $this->db->insert('telefones', [
                                'ten_id' => $this->session->userdata('ten_id'),
                                'pes_id' => $pessoaId,
                                'tel_tipo' => $tipo,
                                'tel_ddd' => substr($ddd, 0, 3),
                                'tel_numero' => substr($numero, 0, 12),
                                'tel_observacao' => $obst,
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
                                'ten_id' => $this->session->userdata('ten_id'),
                                'pes_id' => $pessoaId,
                                'eml_tipo' => $tipo,
                                'eml_email' => $email,
                                'eml_nome' => $nome,
                            ]);
                        }
                    }
                }

                // Salvar endereços, se houver (novos campos END_TIPO, end_cep, END_CIDADE, END_UF, END_BAIRRO)
                $tiposEnd = $this->input->post('END_TIPO');
                $ceps = $this->input->post('end_cep');
                $logradouros = $this->input->post('end_logradouro');
                $numerosEnd = $this->input->post('end_numero');
                $complementos = $this->input->post('end_complemento');
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
                                $estado = $this->db->select('est_id')->where('est_uf', $uf)->get('estados')->row();
                                if ($estado) {
                                    $estId = (int) $estado->est_id;
                                }
                            }

                            // Buscar município pelo nome e estado
                            if ($cidadeNome !== '' && $estId) {
                                $municipio = $this->db->select('mun_id')->where(['mun_nome' => $cidadeNome, 'est_id' => $estId])->get('municipios')->row();
                                if ($municipio) {
                                    $munId = (int) $municipio->mun_id;
                                }
                            }

                            // Buscar/criar bairro
                            if ($bairroNome !== '' && $munId) {
                                $bairro = $this->db->select('bai_id')->where(['bai_nome' => $bairroNome, 'mun_id' => $munId])->get('bairros')->row();
                                if ($bairro) {
                                    $baiId = (int) $bairro->bai_id;
                                } else {
                                    // Criar bairro se não existir
                                    $this->db->insert('bairros', ['mun_id' => $munId, 'bai_nome' => $bairroNome]);
                                    $baiId = (int) $this->db->insert_id();
                                }
                            }

                            // Mapear tipo de endereço
                            $tipoEnd = isset($tiposEnd[$i]) ? $tiposEnd[$i] : 'Comercial';
                            $tipoEndBanco = 'Geral'; // Padrão
                            if ($tipoEnd == 'Cobrança')
                                $tipoEndBanco = 'Cobranca';
                            else if ($tipoEnd == 'Entrega')
                                $tipoEndBanco = 'Entrega';
                            else if ($tipoEnd == 'Faturamento')
                                $tipoEndBanco = 'Faturamento';

                            $dataEnd = [
                                'ten_id' => $this->session->userdata('ten_id'),
                                'pes_id' => $pessoaId,
                                'est_id' => $estId,
                                'mun_id' => $munId,
                                'bai_id' => $baiId,
                                'end_tipo_endenreco' => $tipoEndBanco,
                                'end_logradouro' => $logradouro,
                                'end_numero' => isset($numerosEnd[$i]) ? $numerosEnd[$i] : null,
                                'end_complemento' => isset($complementos[$i]) ? $complementos[$i] : null,
                                'end_cep' => isset($ceps[$i]) ? preg_replace('/\D/', '', $ceps[$i]) : null,
                                'end_situacao' => 1,
                            ];
                            $this->db->insert('enderecos', $dataEnd);
                            $insertedEnderecoIds[$i] = (int) $this->db->insert_id();
                        } else {
                            $insertedEnderecoIds[$i] = null;
                        }
                    }
                    // Definir endereço padrão (obrigatório pelo menos um)
                    $enderecoPadrao = $this->input->post('endereco_padrao');
                    $endIdPadrao = null;
                    if (!empty($enderecoPadrao)) {
                        if (strpos($enderecoPadrao, 'novo_') === 0) {
                            $idx = (int) str_replace('novo_', '', $enderecoPadrao);
                            if (isset($insertedEnderecoIds[$idx]) && $insertedEnderecoIds[$idx]) {
                                $endIdPadrao = (int) $insertedEnderecoIds[$idx];
                            }
                        }
                    }
                    if (!$endIdPadrao && !empty($insertedEnderecoIds)) {
                        $endIdPadrao = (int) reset($insertedEnderecoIds);
                    }
                    if ($endIdPadrao) {
                        $this->db->where('pes_id', $pessoaId);
                        $this->db->update('enderecos', ['end_padrao' => 0]);
                        $this->db->where('end_id', $endIdPadrao)->where('pes_id', $pessoaId);
                        $this->db->update('enderecos', ['end_padrao' => 1]);
                    }
                }

                // Salvar documentos, se houver
                $docTipos = $this->input->post('doc_tipo_documento');
                $docNumeros = $this->input->post('doc_numero');
                $docOrgaos = $this->input->post('doc_orgao_expedidor');
                $docEndIdxs = $this->input->post('DOC_ENDE_IDX');
                $docNaturezas = $this->input->post('doc_natureza_contribuinte');
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
                                'ten_id' => $this->session->userdata('ten_id'),
                                'pes_id' => $pessoaId,
                                'doc_tipo_documento' => mb_substr($tipo, 0, 60),
                                'end_id' => $endeId,
                                'doc_orgao_expedidor' => $orgao !== '' ? mb_substr($orgao, 0, 60) : null,
                                'doc_numero' => mb_substr($numero, 0, 60),
                                'doc_natureza_contribuinte' => in_array($natureza, ['Contribuinte', 'Não Contribuinte']) ? $natureza : null,
                            ]);
                        }
                    }
                }

                // Salvar dados de Cliente (se marcado)
                $isCliente = (bool) $this->input->post('CLN_ENABLE');
                if ($isCliente) {
                    $cliente = [
                        'ten_id' => $this->session->userdata('ten_id'),
                        'pes_id' => $pessoaId,
                        'cln_limite_credito' => $this->input->post('cln_limite_credito') !== null ? str_replace([','], ['.'], $this->input->post('cln_limite_credito')) : null,
                        'cln_situacao' => $this->input->post('cln_situacao') !== null ? (int) $this->input->post('cln_situacao') : 1,
                        'cln_comprar_aprazo' => $this->input->post('cln_comprar_aprazo') ? 1 : 0,
                        'cln_bloqueio_financeiro' => $this->input->post('cln_bloqueio_financeiro') ? 1 : 0,
                        'cln_dias_carencia' => $this->input->post('cln_dias_carencia') !== null ? (int) $this->input->post('cln_dias_carencia') : null,
                        'cln_emitir_nfe' => $this->input->post('cln_emitir_nfe') ? 1 : 0,
                        'cln_cobrar_irrf' => $this->input->post('cln_cobrar_irrf') ? 1 : 0,
                        'cln_objetivo_comercial' => $this->input->post('cln_objetivo_comercial'),
                        'tpc_id' => $this->input->post('tpc_id') ?: null,
                        'cln_data_cadastro' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('clientes', $cliente);
                    $clienteId = $this->db->insert_id();

                    // Salvar vendedores permitidos para este cliente
                    $vendedoresPermitidosPesId = $this->input->post('CLV_VEN_PES_ID');
                    $vendedorPadraoPesId = $this->input->post('clv_padrao');

                    if (is_array($vendedoresPermitidosPesId)) {
                        foreach ($vendedoresPermitidosPesId as $vendedorPesId) {
                            if ($vendedorPesId) {
                                // Buscar ven_id pela pes_id
                                $this->db->select('ven_id');
                                $this->db->where('pes_id', $vendedorPesId);
                                $vendedor = $this->db->get('vendedores')->row();

                                if ($vendedor) {
                                    $isPadrao = ($vendedorPadraoPesId && $vendedorPadraoPesId == $vendedorPesId) ? 1 : 0;

                                    $this->db->insert('clientes_vendedores', [
                                        'ten_id' => $this->session->userdata('ten_id'),
                                        'cln_id' => $clienteId,
                                        'ven_id' => $vendedor->ven_id,
                                        'clv_padrao' => $isPadrao
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
                        'ten_id' => $this->session->userdata('ten_id'),
                        'pes_id' => $pessoaId,
                        'ven_percentual_comissao' => $this->input->post('ven_percentual_comissao') !== null ? str_replace([','], ['.'], $this->input->post('ven_percentual_comissao')) : null,
                        'ven_tipo_comissao' => $this->input->post('ven_tipo_comissao'),
                        'ven_meta_mensal' => $this->input->post('ven_meta_mensal') !== null ? str_replace([','], ['.'], $this->input->post('ven_meta_mensal')) : null,
                        'ven_situacao' => 1, // Sempre ativo ao criar
                    ];
                    $this->db->insert('vendedores', $vendedor);
                }

                // Salvar tipos de pessoa
                $tiposPessoa = $this->input->post('TIPOS_PESSOA');
                if (is_array($tiposPessoa) && $this->db->table_exists('pessoa_tipos')) {
                    foreach ($tiposPessoa as $tipoId) {
                        $this->db->insert('pessoa_tipos', [
                            'ten_id' => $this->session->userdata('ten_id'),
                            'pessoa_id' => $pessoaId,
                            'tipo_id' => $tipoId
                        ]);
                    }
                }

                // Cadastrar usuário do sistema quando tipo "Usuário" estiver marcado
                $tipoUsuario = $this->db->select('id')->from('tipos_pessoa')->where('nome', 'Usuário')->limit(1)->get()->row();
                $tipoUsuarioId = $tipoUsuario ? (int) $tipoUsuario->id : null;
                $usuEnable = (bool) $this->input->post('USU_ENABLE');
                $tiposPessoaPost = $this->input->post('TIPOS_PESSOA');
                $ehTipoUsuario = $tipoUsuarioId && is_array($tiposPessoaPost) && in_array((string) $tipoUsuarioId, $tiposPessoaPost, true);
                if ($usuEnable && $ehTipoUsuario && $this->db->table_exists('usuarios')) {
                    $usu_email = trim((string) $this->input->post('usu_email'));
                    $usu_senha = $this->input->post('usu_senha');
                    $usu_situacao = (int) $this->input->post('usu_situacao');
                    $gpu_id = $this->input->post('gpu_id') ? (int) $this->input->post('gpu_id') : null;
                    $ok = true;
                    if ($usu_email === '') {
                        $this->data['custom_error'] = '<div class="alert alert-danger">Para tipo Usuário, o e-mail (login) é obrigatório.</div>';
                        $ok = false;
                    } elseif (!filter_var($usu_email, FILTER_VALIDATE_EMAIL)) {
                        $this->data['custom_error'] = '<div class="alert alert-danger">E-mail do usuário inválido.</div>';
                        $ok = false;
                    } else {
                        $existe = $this->db->limit(1)->get_where('usuarios', ['usu_email' => $usu_email])->row();
                        if ($existe) {
                            $this->data['custom_error'] = '<div class="alert alert-danger">Este e-mail já está em uso por outro usuário.</div>';
                            $ok = false;
                        }
                    }
                    if ($ok && ($usu_senha === null || $usu_senha === '')) {
                        $this->data['custom_error'] = '<div class="alert alert-danger">Para cadastrar usuário, a senha é obrigatória.</div>';
                        $ok = false;
                    }
                    if ($ok) {
                        $gre_id = $this->session->userdata('ten_id');
                        if ($gre_id === null || $gre_id === '') {
                            $gre_id = null;
                        } else {
                            $gre_id = (int) $gre_id;
                        }
                        $pessoa = $this->db->select('pes_nome')->from('pessoas')->where('pes_id', $pessoaId)->get()->row();
                        $usu_nome = $pessoa ? $pessoa->pes_nome : 'Usuário';
                        $dataUsuario = [
                            'usu_nome' => $usu_nome,
                            'usu_email' => $usu_email,
                            'usu_senha' => password_hash($usu_senha, PASSWORD_DEFAULT),
                            'usu_situacao' => $usu_situacao,
                            'usu_data_cadastro' => date('Y-m-d H:i:s'),
                            'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                            'gre_id' => $gre_id,
                            'pes_id' => $pessoaId,
                        ];
                        if (!$this->db->insert('usuarios', $dataUsuario)) {
                            $this->data['custom_error'] = '<div class="alert alert-danger">Erro ao criar usuário do sistema.</div>';
                        } elseif ($gpu_id && $this->db->table_exists('grupo_usuario_empresa')) {
                            $usu_id = $this->db->insert_id();
                            $emp_id = (int) $this->session->userdata('emp_id');
                            if ($emp_id) {
                                $this->db->replace('grupo_usuario_empresa', [
                                    'usu_id' => $usu_id,
                                    'gpu_id' => $gpu_id,
                                    'emp_id' => $emp_id,
                                    'uge_data_cadastro' => date('Y-m-d H:i:s'),
                                    'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        }
                    }
                }

                if (empty($this->data['custom_error'])) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['result' => true, 'message' => 'Pessoa adicionada com sucesso!', 'redirect' => base_url('index.php/pessoas/visualizar/' . $pessoaId)]);
                        return;
                    }
                    $this->session->set_flashdata('success', 'Pessoa adicionada com sucesso!');
                    log_info('Adicionou uma pessoa.');
                    redirect(base_url('index.php/pessoas/visualizar/' . $pessoaId));
                }
            } else {
                $dbError = $this->db->error();
                log_message('error', 'Pessoas::adicionar insert pessoas falhou: ' . json_encode($dbError));
                $msg = 'Ocorreu um erro ao salvar a pessoa.';
                if (!empty($dbError['message'])) {
                    if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                        $msg .= ' Detalhe: ' . $dbError['message'];
                    } elseif (strpos($dbError['message'], 'Unknown column') !== false) {
                        $msg .= ' Pode ser coluna faltando no banco (ex.: pes_regime_tributario). Execute o script sql/add_pes_regime_tributario.sql se ainda não executou.';
                    }
                }
                $this->data['custom_error'] = '<div class="form_error"><p>' . htmlspecialchars($msg) . '</p></div>';
            }
            }
        }

        if ($this->input->is_ajax_request() && isset($this->data['custom_error']) && $this->data['custom_error']) {
            echo json_encode(['result' => false, 'message' => $this->data['custom_error']]);
            return;
        }
        // Carrega estados para o modal de endereço
        $this->data['estados'] = $this->db->order_by('est_uf', 'ASC')->get('estados')->result();

        $this->data['tipos_clientes'] = $this->Tipos_clientes_model->get('tipos_clientes', 'tpc_id, tpc_nome', '', 0, 0, false, 'object', 'tpc_nome', 'ASC');
        // Tipo "Usuário" e grupos de usuário para cadastro de usuário pelo cadastro de pessoas
        $tipoUsuario = $this->db->select('id')->from('tipos_pessoa')->where('nome', 'Usuário')->limit(1)->get()->row();
        $this->data['tipo_usuario_id'] = $tipoUsuario ? (int) $tipoUsuario->id : null;
        $emp_id = (int) $this->session->userdata('emp_id');
        $this->data['grupos'] = [];
        if ($emp_id && $this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $emp_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
        }
        $this->data['view'] = 'pessoas/adicionarPessoa';
        return $this->layout();
    }

    // Retorna municípios por est_id (JSON)
    public function getMunicipios()
    {
        $estId = (int) $this->input->get('est_id');
        if (!$estId) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }
        $rows = $this->db->select('mun_id, mun_nome')->from('municipios')->where('est_id', $estId)->order_by('mun_nome', 'ASC')->get()->result();
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }

    // Retorna bairros por mun_id (JSON)
    public function getBairros()
    {
        $munId = (int) $this->input->get('mun_id');
        if (!$munId) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }
        $rows = $this->db->select('bai_id, bai_nome')->from('bairros')->where('mun_id', $munId)->order_by('bai_nome', 'ASC')->get()->result();
        return $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }

    public function editar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Pessoa não encontrada.');
            redirect(base_url('index.php/pessoas'));
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'ePessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar pessoas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Validação customizada para CPF/CNPJ considerando ten_id (permitindo o próprio registro na edição)
        $this->form_validation->set_rules('pes_cpfcnpj', 'CPF/CNPJ', 'required|trim|callback_check_cpfcnpj_unique_edit[' . $id . ']');
        $this->form_validation->set_rules('pes_nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('pes_codigo', 'Código', 'required|trim');
        $this->form_validation->set_rules('pes_fisico_juridico', 'Tipo (F/J)', 'required|in_list[F,J]');
        $this->form_validation->set_rules('pes_regime_tributario', 'Regime Tributário', 'trim|callback_check_regime_tributario_cnpj');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'ten_id' => $this->session->userdata('ten_id'),
                'pes_cpfcnpj' => $this->input->post('pes_cpfcnpj'),
                'pes_nome' => $this->input->post('pes_nome'),
                'pes_razao_social' => $this->input->post('pes_razao_social'),
                'pes_codigo' => $this->input->post('pes_codigo'),
                'pes_fisico_juridico' => $this->input->post('pes_fisico_juridico'),
                'pes_regime_tributario' => $this->normalizarRegimeTributario($this->input->post('pes_regime_tributario')),
                'pes_nascimento_abertura' => $this->input->post('pes_nascimento_abertura') ?: null,
                'pes_nacionalidades' => $this->input->post('pes_nacionalidades'),
                'pes_rg' => $this->input->post('pes_rg'),
                'pes_orgao_expedidor' => $this->input->post('pes_orgao_expedidor'),
                'pes_sexo' => $this->input->post('pes_sexo'),
                'pes_estado_civil' => $this->input->post('pes_estado_civil') ?: null,
                'pes_escolaridade' => $this->input->post('pes_escolaridade') ?: null,
                'pes_profissao' => $this->input->post('pes_profissao'),
                'pes_observacao' => $this->input->post('pes_observacao'),
                'pes_situacao' => $this->input->post('pes_situacao') !== null ? (int) $this->input->post('pes_situacao') : 1,
            ];

            if ($this->Pessoas_model->edit('pessoas', $data, 'pes_id', $id)) {

                // Processar endereços se houver
                if ($this->db->table_exists('enderecos')) {
                    $enderecoIds = $this->input->post('end_id') ?: [];
                    $tiposEnd = $this->input->post('END_TIPO') ?: [];
                    $logradouros = $this->input->post('end_logradouro') ?: [];
                    $enderecoPadrao = trim((string) $this->input->post('endereco_padrao'));
                    $temEnderecos = is_array($tiposEnd) && count($tiposEnd) > 0 && is_array($logradouros) && count(array_filter($logradouros)) > 0;
                    if ($temEnderecos && $enderecoPadrao === '') {
                        $this->data['custom_error'] = '<div class="form_error"><p>É obrigatório definir um endereço padrão.</p></div>';
                    }
                    if (empty($this->data['custom_error'])) {
                        // Primeiro, desmarcar todos os endereços como não padrão
                        $this->db->where('pes_id', $id);
                        $this->db->update('enderecos', ['end_padrao' => 0]);

                        $ceps = $this->input->post('end_cep') ?: [];
                        $numerosEnd = $this->input->post('end_numero') ?: [];
                        $complementos = $this->input->post('end_complemento') ?: [];
                        $bairrosTexto = $this->input->post('END_BAIRRO') ?: [];
                        $cidadesTexto = $this->input->post('END_CIDADE') ?: [];
                        $ufs = $this->input->post('END_UF') ?: [];

                        $enderecoIdsByIndex = [];
                        if (is_array($tiposEnd) && count($tiposEnd) > 0) {
                        foreach ($tiposEnd as $i => $tipoEnd) {
                            if (!empty($logradouros[$i])) {
                                // Verificar se é um endereço existente ou novo
                                $enderecoId = isset($enderecoIds[$i]) ? $enderecoIds[$i] : null;

                                // Mapear tipos
                                $tipoEndBanco = $tipoEnd;
                                if ($tipoEnd == 'Comercial')
                                    $tipoEndBanco = 'Geral';
                                if ($tipoEnd == 'Cobrança')
                                    $tipoEndBanco = 'Cobranca';
                                if ($tipoEnd == 'Entrega')
                                    $tipoEndBanco = 'Entrega';

                                // Buscar IDs de estado, município e bairro
                                $estId = null;
                                $munId = null;
                                $baiId = null;

                                // Buscar estado
                                if (!empty($ufs) && isset($ufs[$i]) && !empty($ufs[$i])) {
                                    $estado = $this->db->get_where('estados', ['est_uf' => $ufs[$i]])->row();
                                    $estId = $estado ? $estado->est_id : null;
                                }

                                // Buscar município - IMPORTANTE: mun_id não pode ser NULL devido à constraint
                                if (!empty($cidadesTexto) && isset($cidadesTexto[$i]) && !empty($cidadesTexto[$i])) {
                                    $this->db->select('mun_id');
                                    $this->db->from('municipios');
                                    $this->db->where('mun_nome', $cidadesTexto[$i]);

                                    // Se temos estado, filtrar por ele também
                                    if ($estId) {
                                        $this->db->where('est_id', $estId);
                                    }

                                    $municipio = $this->db->get()->row();
                                    $munId = $municipio ? $municipio->mun_id : null;

                                    // Se não encontrou e temos estado, tentar buscar qualquer município do estado
                                    if (!$munId && $estId) {
                                        $this->db->select('mun_id');
                                        $this->db->from('municipios');
                                        $this->db->where('est_id', $estId);
                                        $this->db->limit(1);
                                        $municipio_fallback = $this->db->get()->row();
                                        $munId = $municipio_fallback ? $municipio_fallback->mun_id : null;
                                    }

                                    // Se ainda não encontrou, buscar o primeiro município disponível (fallback)
                                    if (!$munId) {
                                        $this->db->select('mun_id');
                                        $this->db->from('municipios');
                                        $this->db->limit(1);
                                        $municipio_fallback = $this->db->get()->row();
                                        $munId = $municipio_fallback ? $municipio_fallback->mun_id : null;
                                    }
                                }

                                // Buscar bairro (opcional - pode ser NULL)
                                if (!empty($bairrosTexto[$i]) && $munId) {
                                    $bairro = $this->db->get_where('bairros', [
                                        'bai_nome' => $bairrosTexto[$i],
                                        'mun_id' => $munId
                                    ])->row();
                                    $baiId = $bairro ? $bairro->bai_id : null;
                                }

                                // Validar se temos os dados essenciais para o endereço
                                if (!$munId) {
                                    // Pular este endereço se não conseguir determinar o município
                                    error_log("Pulando endereço {$i} - município não encontrado para cidade: {$cidadesTexto[$i]}, UF: {$ufs[$i]}");
                                    continue;
                                }

                                $enderecoData = [
                                    'ten_id' => $this->session->userdata('ten_id'),
                                    'pes_id' => $id,
                                    'est_id' => $estId,
                                    'mun_id' => $munId, // Este campo é obrigatório devido à constraint
                                    'bai_id' => $baiId,
                                    'end_tipo_endenreco' => $tipoEndBanco,
                                    'end_logradouro' => $logradouros[$i],
                                    'end_numero' => isset($numerosEnd[$i]) ? $numerosEnd[$i] : null,
                                    'end_complemento' => isset($complementos[$i]) ? $complementos[$i] : null,
                                    'end_cep' => isset($ceps[$i]) ? preg_replace('/\D/', '', $ceps[$i]) : null,
                                    'end_situacao' => 1,
                                    'end_padrao' => 0 // Será definido abaixo
                                ];

                                if ($enderecoId) {
                                    // Atualizar endereço existente
                                    $this->db->where('end_id', $enderecoId);
                                    $this->db->update('enderecos', $enderecoData);
                                } else {
                                    // Inserir novo endereço
                                    $this->db->insert('enderecos', $enderecoData);
                                    $enderecoId = $this->db->insert_id();
                                }
                                $enderecoIdsByIndex[$i] = $enderecoId;
                            }
                        }

                        // Marcar endereço padrão
                        if (!empty($enderecoPadrao)) {
                            if (strpos($enderecoPadrao, 'novo_') === 0) {
                                $idxNovo = (int) str_replace('novo_', '', $enderecoPadrao);
                                if (isset($enderecoIdsByIndex[$idxNovo]) && $enderecoIdsByIndex[$idxNovo]) {
                                    $this->db->where('end_id', $enderecoIdsByIndex[$idxNovo]);
                                    $this->db->where('pes_id', $id);
                                    $this->db->update('enderecos', ['end_padrao' => 1]);
                                }
                            } else {
                                $this->db->where('end_id', $enderecoPadrao);
                                $this->db->where('pes_id', $id);
                                $this->db->update('enderecos', ['end_padrao' => 1]);
                            }
                        }
                    }
                    }
                }

                // Processar documentos (deletar antigos e inserir novos)
                if ($this->db->table_exists('documentos')) {
                    // Remover documentos antigos
                    $this->db->where('pes_id', $id);
                    $this->db->delete('documentos');

                    // Inserir novos documentos
                    $docTipos = $this->input->post('doc_tipo_documento');
                    $docNumeros = $this->input->post('doc_numero');
                    $docOrgaos = $this->input->post('doc_orgao_expedidor');
                    $docEndIdxs = $this->input->post('DOC_ENDE_IDX');
                    $docNaturezas = $this->input->post('doc_natureza_contribuinte');
                    
                    if (is_array($docTipos) || is_array($docNumeros) || is_array($docOrgaos)) {
                        $max = max(
                            is_array($docTipos) ? count($docTipos) : 0,
                            is_array($docNumeros) ? count($docNumeros) : 0,
                            is_array($docOrgaos) ? count($docOrgaos) : 0,
                            is_array($docEndIdxs) ? count($docEndIdxs) : 0,
                            is_array($docNaturezas) ? count($docNaturezas) : 0
                        );
                        
                        // Buscar IDs dos endereços inseridos/atualizados para vincular documentos
                        $enderecoIdsMap = [];
                        if (is_array($enderecoIds)) {
                            foreach ($enderecoIds as $idx => $endId) {
                                if ($endId) {
                                    $enderecoIdsMap[$idx] = $endId;
                                }
                            }
                        }
                        
                        for ($i = 0; $i < $max; $i++) {
                            $tipo = is_array($docTipos) && isset($docTipos[$i]) ? trim((string) $docTipos[$i]) : '';
                            $numero = is_array($docNumeros) && isset($docNumeros[$i]) ? trim((string) $docNumeros[$i]) : '';
                            $orgao = is_array($docOrgaos) && isset($docOrgaos[$i]) ? trim((string) $docOrgaos[$i]) : null;
                            $natureza = is_array($docNaturezas) && isset($docNaturezas[$i]) ? trim((string) $docNaturezas[$i]) : null;
                            
                            if ($tipo !== '' && $numero !== '') {
                                $endeId = null;
                                if (is_array($docEndIdxs) && isset($docEndIdxs[$i]) && $docEndIdxs[$i] !== '') {
                                    $idx = (int) $docEndIdxs[$i];
                                    // Verificar se é um índice de endereço novo ou existente
                                    if (isset($enderecoIdsMap[$idx])) {
                                        $endeId = (int) $enderecoIdsMap[$idx];
                                    } else {
                                        // Tentar buscar pelo índice se for um endereço existente
                                        if (isset($enderecoIds[$idx]) && $enderecoIds[$idx]) {
                                            $endeId = (int) $enderecoIds[$idx];
                                        }
                                    }
                                }
                                if (stripos($tipo, 'Inscrição Estadual') !== false && !$endeId) {
                                    $this->data['custom_error'] = '<div class="form_error"><p>Para documento tipo Inscrição Estadual é obrigatório vincular ao endereço.</p></div>';
                                    break;
                                }
                                $this->db->insert('documentos', [
                                    'ten_id' => $this->session->userdata('ten_id'),
                                    'pes_id' => $id,
                                    'doc_tipo_documento' => mb_substr($tipo, 0, 60),
                                    'end_id' => $endeId,
                                    'doc_orgao_expedidor' => $orgao !== '' ? mb_substr($orgao, 0, 60) : null,
                                    'doc_numero' => mb_substr($numero, 0, 60),
                                    'doc_natureza_contribuinte' => in_array($natureza, ['Contribuinte', 'Não Contribuinte']) ? $natureza : null,
                                ]);
                            }
                        }
                    }
                }

                // Salvar dados de Cliente (se marcado)
                $isCliente = (bool) $this->input->post('CLN_ENABLE');
                if ($this->db->table_exists('clientes')) {
                    if ($isCliente) {
                        $clienteData = [
                            'ten_id' => $this->session->userdata('ten_id'),
                            'pes_id' => $id,
                            'cln_limite_credito' => $this->input->post('cln_limite_credito') !== null ? str_replace([','], ['.'], $this->input->post('cln_limite_credito')) : null,
                            'cln_situacao' => $this->input->post('cln_situacao') !== null ? (int) $this->input->post('cln_situacao') : 1,
                            'cln_comprar_aprazo' => $this->input->post('cln_comprar_aprazo') ? 1 : 0,
                            'cln_bloqueio_financeiro' => $this->input->post('cln_bloqueio_financeiro') ? 1 : 0,
                            'cln_dias_carencia' => $this->input->post('cln_dias_carencia') !== null ? (int) $this->input->post('cln_dias_carencia') : null,
                            'cln_emitir_nfe' => $this->input->post('cln_emitir_nfe') ? 1 : 0,
                            'cln_cobrar_irrf' => $this->input->post('cln_cobrar_irrf') ? 1 : 0,
                            'cln_objetivo_comercial' => $this->input->post('cln_objetivo_comercial'),
                            'tpc_id' => $this->input->post('tpc_id') ?: null,
                        ];

                        // Verificar se já existe registro de cliente
                        $existente = $this->db->get_where('clientes', ['pes_id' => $id])->row();
                        if ($existente) {
                            $this->db->where('pes_id', $id);
                            $this->db->update('clientes', $clienteData);
                            $clienteId = $existente->cln_id;
                        } else {
                            $clienteData['cln_data_cadastro'] = date('Y-m-d H:i:s');
                            $this->db->insert('clientes', $clienteData);
                            $clienteId = $this->db->insert_id();
                        }

                        // Atualizar vendedores permitidos
                        if ($this->db->table_exists('clientes_vendedores')) {
                            $this->db->where('cln_id', $clienteId);
                            $this->db->delete('clientes_vendedores');

                            $vendedoresPermitidosPesId = $this->input->post('CLV_VEN_PES_ID');
                            $vendedorPadraoPesId = $this->input->post('clv_padrao');

                            if (is_array($vendedoresPermitidosPesId)) {
                                foreach ($vendedoresPermitidosPesId as $vendedorPesId) {
                                    if ($vendedorPesId) {
                                        $this->db->select('ven_id');
                                        $this->db->where('pes_id', $vendedorPesId);
                                        $vendedor = $this->db->get('vendedores')->row();

                                        if ($vendedor) {
                                            $isPadrao = ($vendedorPadraoPesId && $vendedorPadraoPesId == $vendedorPesId) ? 1 : 0;
                                            $this->db->insert('clientes_vendedores', [
                                                'ten_id' => $this->session->userdata('ten_id'),
                                                'cln_id' => $clienteId,
                                                'ven_id' => $vendedor->ven_id,
                                                'clv_padrao' => $isPadrao
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        // Se desmarcou cliente, opcionalmente remover ou inativar. 
                        // Aqui manteremos os dados mas o checkbox controla o acesso.
                    }
                }

                // Salvar dados de Vendedor (se marcado)
                $isVendedor = (bool) $this->input->post('VEN_ENABLE');
                if ($this->db->table_exists('vendedores')) {
                    if ($isVendedor) {
                        $vendedorData = [
                            'ten_id' => $this->session->userdata('ten_id'),
                            'pes_id' => $id,
                            'ven_percentual_comissao' => $this->input->post('ven_percentual_comissao') !== null ? str_replace([','], ['.'], $this->input->post('ven_percentual_comissao')) : null,
                            'ven_tipo_comissao' => $this->input->post('ven_tipo_comissao'),
                            'ven_meta_mensal' => $this->input->post('ven_meta_mensal') !== null ? str_replace([','], ['.'], $this->input->post('ven_meta_mensal')) : null,
                        ];

                        $existente = $this->db->get_where('vendedores', ['pes_id' => $id])->row();
                        if ($existente) {
                            $this->db->where('pes_id', $id);
                            $this->db->update('vendedores', $vendedorData);
                        } else {
                            $vendedorData['ven_situacao'] = 1;
                            $this->db->insert('vendedores', $vendedorData);
                        }
                    }
                }

                // Salvar tipos de pessoa
                $tiposPessoa = $this->input->post('TIPOS_PESSOA');
                if (is_array($tiposPessoa) && $this->db->table_exists('pessoa_tipos')) {
                    // Remover tipos antigos
                    $this->db->where('pessoa_id', $id);
                    $this->db->delete('pessoa_tipos');

                    foreach ($tiposPessoa as $tipoId) {
                        $this->db->insert('pessoa_tipos', [
                            'ten_id' => $this->session->userdata('ten_id'),
                            'pessoa_id' => $id,
                            'tipo_id' => $tipoId
                        ]);
                    }
                }

                // Criar ou atualizar usuário do sistema quando tipo "Usuário" estiver marcado
                $tipoUsuario = $this->db->select('id')->from('tipos_pessoa')->where('nome', 'Usuário')->limit(1)->get()->row();
                $tipoUsuarioId = $tipoUsuario ? (int) $tipoUsuario->id : null;
                $usuEnable = (bool) $this->input->post('USU_ENABLE');
                $tiposPessoaPost = $this->input->post('TIPOS_PESSOA');
                $ehTipoUsuario = $tipoUsuarioId && is_array($tiposPessoaPost) && in_array((string) $tipoUsuarioId, $tiposPessoaPost, true);
                if ($usuEnable && $ehTipoUsuario && $this->db->table_exists('usuarios')) {
                    $usu_email = trim((string) $this->input->post('usu_email'));
                    $usu_senha = $this->input->post('usu_senha');
                    $usu_situacao = (int) $this->input->post('usu_situacao');
                    $gpu_id = $this->input->post('gpu_id') ? (int) $this->input->post('gpu_id') : null;
                    $usuarioExistente = $this->db->where('pes_id', $id)->get('usuarios')->row();
                    $ok = true;
                    if ($usu_email === '') {
                        $this->data['custom_error'] = '<div class="alert alert-danger">Para tipo Usuário, o e-mail (login) é obrigatório.</div>';
                        $ok = false;
                    } elseif (!filter_var($usu_email, FILTER_VALIDATE_EMAIL)) {
                        $this->data['custom_error'] = '<div class="alert alert-danger">E-mail do usuário inválido.</div>';
                        $ok = false;
                    } else {
                        $this->db->where('usu_email', $usu_email);
                        if ($usuarioExistente) {
                            $this->db->where('usu_id !=', $usuarioExistente->usu_id);
                        }
                        $existe = $this->db->limit(1)->get('usuarios')->row();
                        if ($existe) {
                            $this->data['custom_error'] = '<div class="alert alert-danger">Este e-mail já está em uso por outro usuário.</div>';
                            $ok = false;
                        }
                    }
                    if ($ok && !$usuarioExistente && ($usu_senha === null || $usu_senha === '')) {
                        $this->data['custom_error'] = '<div class="alert alert-danger">Para cadastrar novo usuário, a senha é obrigatória.</div>';
                        $ok = false;
                    }
                    if ($ok) {
                        $pessoa = $this->db->select('pes_nome')->from('pessoas')->where('pes_id', $id)->get()->row();
                        $usu_nome = $pessoa ? $pessoa->pes_nome : 'Usuário';
                        $gre_id = $this->session->userdata('ten_id');
                        $gre_id = ($gre_id === null || $gre_id === '') ? null : (int) $gre_id;
                        $emp_id = (int) $this->session->userdata('emp_id');
                        if ($usuarioExistente) {
                            $dataUsuario = [
                                'usu_nome' => $usu_nome,
                                'usu_email' => $usu_email,
                                'usu_situacao' => $usu_situacao,
                                'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                                'gre_id' => $gre_id,
                            ];
                            if ($usu_senha !== null && $usu_senha !== '') {
                                $dataUsuario['usu_senha'] = password_hash($usu_senha, PASSWORD_DEFAULT);
                            }
                            $this->db->where('usu_id', $usuarioExistente->usu_id);
                            $this->db->update('usuarios', $dataUsuario);
                            if ($gpu_id && $emp_id && $this->db->table_exists('grupo_usuario_empresa')) {
                                $uge = $this->db->get_where('grupo_usuario_empresa', [
                                    'usu_id' => $usuarioExistente->usu_id,
                                    'emp_id' => $emp_id,
                                ])->row();
                                if ($uge) {
                                    $this->db->where('uge_id', $uge->uge_id)->update('grupo_usuario_empresa', [
                                        'gpu_id' => $gpu_id,
                                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                                    ]);
                                } else {
                                    $this->db->insert('grupo_usuario_empresa', [
                                        'usu_id' => $usuarioExistente->usu_id,
                                        'gpu_id' => $gpu_id,
                                        'emp_id' => $emp_id,
                                        'uge_data_cadastro' => date('Y-m-d H:i:s'),
                                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                                    ]);
                                }
                            }
                        } else {
                            $dataUsuario = [
                                'usu_nome' => $usu_nome,
                                'usu_email' => $usu_email,
                                'usu_senha' => password_hash($usu_senha, PASSWORD_DEFAULT),
                                'usu_situacao' => $usu_situacao,
                                'usu_data_cadastro' => date('Y-m-d H:i:s'),
                                'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                                'gre_id' => $gre_id,
                                'pes_id' => $id,
                            ];
                            $this->db->insert('usuarios', $dataUsuario);
                            if ($this->db->affected_rows() && $gpu_id && $emp_id && $this->db->table_exists('grupo_usuario_empresa')) {
                                $usu_id = $this->db->insert_id();
                                $this->db->replace('grupo_usuario_empresa', [
                                    'usu_id' => $usu_id,
                                    'gpu_id' => $gpu_id,
                                    'emp_id' => $emp_id,
                                    'uge_data_cadastro' => date('Y-m-d H:i:s'),
                                    'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        }
                    }
                }

                if (empty($this->data['custom_error'])) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['result' => true, 'message' => 'Pessoa editada com sucesso!', 'redirect' => base_url('index.php/pessoas/visualizar/' . $id)]);
                        return;
                    }
                    $this->session->set_flashdata('success', 'Pessoa editada com sucesso!');
                    log_info('Alterou uma pessoa. ID ' . $id);
                    redirect(base_url('index.php/pessoas/visualizar/' . $id));
                }
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        if ($this->input->is_ajax_request() && isset($this->data['custom_error']) && $this->data['custom_error']) {
            echo json_encode(['result' => false, 'message' => $this->data['custom_error']]);
            return;
        }

        $this->data['result'] = $this->Pessoas_model->getById($id);

        // Buscar telefones (se tabela existir)
        if ($this->db->table_exists('telefones')) {
            $this->data['telefones'] = $this->db->where('pes_id', $id)->get('telefones')->result();
        } else {
            $this->data['telefones'] = [];
        }

        // Buscar emails (se tabela existir)
        if ($this->db->table_exists('emails')) {
            $this->data['emails'] = $this->db->where('pes_id', $id)->get('emails')->result();
        } else {
            $this->data['emails'] = [];
        }

        // Buscar endereços (se tabela existir)
        if ($this->db->table_exists('enderecos')) {
            $this->db->select('e.*, est.est_uf, mun.mun_nome, bai.bai_nome');
            $this->db->from('enderecos e');
            $this->db->join('estados est', 'est.est_id = e.est_id', 'left');
            $this->db->join('municipios mun', 'mun.mun_id = e.mun_id', 'left');
            $this->db->join('bairros bai', 'bai.bai_id = e.bai_id', 'left');
            $this->db->where('e.pes_id', $id);
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
            $this->data['documentos'] = $this->db->where('pes_id', $id)->get('documentos')->result();
        } else {
            $this->data['documentos'] = [];
        }

        // Buscar tipos de pessoa vinculados (se tabela existir)
        if ($this->db->table_exists('pessoa_tipos')) {
            // Tentar diferentes estruturas de colunas
            $query = $this->db->get_where('pessoa_tipos', ['pes_id' => $id]);

            // Se não encontrou com pes_id, tentar com pessoa_id ou pt_pessoa_id
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
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->TPP_ID];
                } elseif (isset($vinculo->tipo_id)) {
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->tipo_id];
                } elseif (isset($vinculo->pt_tipo_id)) {
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->pt_tipo_id];
                } elseif (isset($vinculo->TP_ID)) {
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->TP_ID];
                }
            }

            error_log('Tipos vinculados processados: ' . print_r($this->data['tipos_vinculados'], true));
        } else {
            $this->data['tipos_vinculados'] = [];
            error_log('Tabela pessoa_tipos não existe');
        }

        // Buscar dados de cliente (se tabela existir)
        if ($this->db->table_exists('clientes')) {
            $this->data['cliente'] = $this->db->where('pes_id', $id)->get('clientes')->row();

            // Se for cliente, buscar vendedores permitidos
            if ($this->data['cliente'] && $this->db->table_exists('clientes_vendedores')) {
                $this->db->select('cv.*, v.pes_id as VEN_PES_ID, p.pes_nome as VEN_NOME');
                $this->db->from('clientes_vendedores cv');
                $this->db->join('vendedores v', 'v.ven_id = cv.ven_id');
                $this->db->join('pessoas p', 'p.pes_id = v.pes_id');
                $this->db->where('cv.cln_id', $this->data['cliente']->cln_id);
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
            $this->data['vendedor'] = $this->db->where('pes_id', $id)->get('vendedores')->row();
        } else {
            $this->data['vendedor'] = null;
        }

        // Tipo "Usuário" e usuário vinculado (se existir) para edição
        $tipoUsuario = $this->db->select('id')->from('tipos_pessoa')->where('nome', 'Usuário')->limit(1)->get()->row();
        $this->data['tipo_usuario_id'] = $tipoUsuario ? (int) $tipoUsuario->id : null;
        if ($this->db->table_exists('usuarios')) {
            $this->data['usuario'] = $this->db->where('pes_id', $id)->get('usuarios')->row();
        } else {
            $this->data['usuario'] = null;
        }
        $emp_id = (int) $this->session->userdata('emp_id');
        $this->data['grupos'] = [];
        $this->data['gpu_id_atual'] = null;
        if ($emp_id && $this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $emp_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
            if (!empty($this->data['usuario']) && $this->db->table_exists('grupo_usuario_empresa')) {
                $uge = $this->db->get_where('grupo_usuario_empresa', [
                    'usu_id' => $this->data['usuario']->usu_id,
                    'emp_id' => $emp_id,
                ])->row();
                if ($uge) {
                    $this->data['gpu_id_atual'] = (int) $uge->gpu_id;
                }
            }
        }

        $this->data['tipos_clientes'] = $this->Tipos_clientes_model->get('tipos_clientes', 'tpc_id, tpc_nome', '', 0, 0, false, 'object', 'tpc_nome', 'ASC');
        $this->data['view'] = 'pessoas/editarPessoa';
        return $this->layout();
    }

    public function visualizar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Pessoa não encontrada.');
            redirect(base_url('index.php/pessoas'));
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vPessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar pessoas.');
            redirect(base_url());
        }

        $this->data['result'] = $this->Pessoas_model->getById($id);

        if ($this->db->table_exists('telefones')) {
            $this->data['telefones'] = $this->db->where('pes_id', $id)->get('telefones')->result();
        } else {
            $this->data['telefones'] = [];
        }

        if ($this->db->table_exists('emails')) {
            $this->data['emails'] = $this->db->where('pes_id', $id)->get('emails')->result();
        } else {
            $this->data['emails'] = [];
        }

        if ($this->db->table_exists('enderecos')) {
            $this->db->select('e.*, est.est_uf, mun.mun_nome, bai.bai_nome');
            $this->db->from('enderecos e');
            $this->db->join('estados est', 'est.est_id = e.est_id', 'left');
            $this->db->join('municipios mun', 'mun.mun_id = e.mun_id', 'left');
            $this->db->join('bairros bai', 'bai.bai_id = e.bai_id', 'left');
            $this->db->where('e.pes_id', $id);
            $this->data['enderecos'] = $this->db->get()->result();
        } else {
            $this->data['enderecos'] = [];
        }

        if ($this->db->table_exists('documentos')) {
            $documentos = $this->db->where('pes_id', $id)->get('documentos')->result();
            foreach ($documentos as $doc) {
                $doc->DOC_ENDE_IDX = null;
                $endeId = isset($doc->ENDEID) ? $doc->ENDEID : (isset($doc->end_id) ? $doc->end_id : null);
                if ($endeId) {
                    foreach ($this->data['enderecos'] as $idx => $ende) {
                        if ($ende->end_id == $endeId) {
                            $doc->DOC_ENDE_IDX = $idx;
                            break;
                        }
                    }
                }
            }
            $this->data['documentos'] = $documentos;
        } else {
            $this->data['documentos'] = [];
        }

        if ($this->db->table_exists('pessoa_tipos')) {
            $query = $this->db->get_where('pessoa_tipos', ['pes_id' => $id]);
            if ($query->num_rows() == 0) {
                $query = $this->db->get_where('pessoa_tipos', ['pessoa_id' => $id]);
            }
            if ($query->num_rows() == 0) {
                $query = $this->db->get_where('pessoa_tipos', ['pt_pessoa_id' => $id]);
            }
            $tiposVinculados = $query->result();
            $this->data['tipos_vinculados'] = [];
            foreach ($tiposVinculados as $vinculo) {
                if (isset($vinculo->TPP_ID)) {
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->TPP_ID];
                } elseif (isset($vinculo->tipo_id)) {
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->tipo_id];
                } elseif (isset($vinculo->pt_tipo_id)) {
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->pt_tipo_id];
                } elseif (isset($vinculo->TP_ID)) {
                    $this->data['tipos_vinculados'][] = (object) ['TPP_ID' => $vinculo->TP_ID];
                }
            }
        } else {
            $this->data['tipos_vinculados'] = [];
        }

        if ($this->db->table_exists('clientes')) {
            $this->db->select('c.*, tc.tpc_nome');
            $this->db->from('clientes c');
            $this->db->join('TIPOS_CLIENTES tc', 'tc.tpc_id = c.tpc_id', 'left');
            $this->db->where('c.pes_id', $id);
            $this->data['cliente'] = $this->db->get()->row();

            if ($this->data['cliente'] && $this->db->table_exists('clientes_vendedores')) {
                $this->db->select('cv.*, v.pes_id as VEN_PES_ID, p.pes_nome as VEN_NOME');
                $this->db->from('clientes_vendedores cv');
                $this->db->join('vendedores v', 'v.ven_id = cv.ven_id');
                $this->db->join('pessoas p', 'p.pes_id = v.pes_id');
                $this->db->where('cv.cln_id', $this->data['cliente']->cln_id);
                $this->data['vendedores_permitidos'] = $this->db->get()->result();
            } else {
                $this->data['vendedores_permitidos'] = [];
            }
        } else {
            $this->data['cliente'] = null;
            $this->data['vendedores_permitidos'] = [];
        }

        if ($this->db->table_exists('vendedores')) {
            $this->data['vendedor'] = $this->db->where('pes_id', $id)->get('vendedores')->row();
        } else {
            $this->data['vendedor'] = null;
        }

        if ($this->db->table_exists('usuarios')) {
            $this->data['usuario'] = $this->db->where('pes_id', $id)->get('usuarios')->row();
        } else {
            $this->data['usuario'] = null;
        }

        $this->data['view'] = 'pessoas/visualizarPessoa';
        return $this->layout();
    }

    /**
     * Validação customizada para verificar se CPF/CNPJ já existe no mesmo tenant (para adicionar)
     */
    public function check_cpfcnpj_unique($cpfcnpj)
    {
        if (empty($cpfcnpj)) {
            return true; // Se vazio, outra validação já vai tratar
        }

        $ten_id = $this->session->userdata('ten_id');
        
        // Verificar se já existe CPF/CNPJ para o mesmo tenant
        $this->db->where('pes_cpfcnpj', $cpfcnpj);
        $this->db->where('ten_id', $ten_id);
        $query = $this->db->get('pessoas');

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_cpfcnpj_unique', 'Este CPF/CNPJ já está cadastrado.');
            return false;
        }

        return true;
    }

    /**
     * Validação customizada para verificar se CPF/CNPJ já existe no mesmo tenant (para editar)
     * Permite que o próprio registro mantenha o mesmo CPF/CNPJ
     */
    public function check_cpfcnpj_unique_edit($cpfcnpj, $id)
    {
        if (empty($cpfcnpj)) {
            return true; // Se vazio, outra validação já vai tratar
        }

        $ten_id = $this->session->userdata('ten_id');
        
        // Verificar se já existe CPF/CNPJ para o mesmo tenant, excluindo o próprio registro
        $this->db->where('pes_cpfcnpj', $cpfcnpj);
        $this->db->where('ten_id', $ten_id);
        $this->db->where('pes_id !=', $id);
        $query = $this->db->get('pessoas');

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_cpfcnpj_unique_edit', 'Este CPF/CNPJ já está cadastrado para outra pessoa.');
            return false;
        }

        return true;
    }

    /**
     * Regime Tributário: obrigatório quando for CNPJ (14 dígitos), opcional para CPF.
     * Valores aceitos: MEI, Simples Nacional, Regime Normal.
     */
    public function check_regime_tributario_cnpj($regime)
    {
        $cpfcnpj = preg_replace('/\D/', '', (string) $this->input->post('pes_cpfcnpj'));
        if (strlen($cpfcnpj) === 11) {
            return true;
        }
        if (strlen($cpfcnpj) === 14) {
            $regime = trim((string) $regime);
            if ($regime === '') {
                $this->form_validation->set_message('check_regime_tributario_cnpj', 'Para CNPJ, o Regime Tributário é obrigatório.');
                return false;
            }
            $aceitos = ['MEI', 'Simples Nacional', 'Regime Normal'];
            if (!in_array($regime, $aceitos, true)) {
                $this->form_validation->set_message('check_regime_tributario_cnpj', 'Regime Tributário deve ser MEI, Simples Nacional ou Regime Normal.');
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * Retorna o valor a gravar em pes_regime_tributario (NULL se vazio ou inválido).
     */
    private function normalizarRegimeTributario($valor)
    {
        $v = trim((string) $valor);
        if ($v === '') {
            return null;
        }
        $aceitos = ['MEI', 'Simples Nacional', 'Regime Normal'];
        return in_array($v, $aceitos, true) ? $v : null;
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dPessoa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir pessoas.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir pessoa.');
            redirect(site_url('pessoas/gerenciar/'));
        }

        $this->Pessoas_model->delete('pessoas', 'pes_id', $id);
        log_info('Removeu uma pessoa. ID ' . $id);

        $this->session->set_flashdata('success', 'Pessoa excluída com sucesso!');
        redirect(site_url('pessoas/gerenciar/'));
    }
}
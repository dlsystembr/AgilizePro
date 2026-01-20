<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Nfecom extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('Nfecom_model');
        $this->load->model('Nfe_model');
        $this->load->model('Clientes_model');
        $this->load->model('ConfiguracoesFiscais_model');
        $this->load->model('OperacaoComercial_model');
        $this->load->library('FiscalClassificationService');
        $this->data['menuNfecom'] = 'NFECom';

        // Fix for OpenSSL 3 legacy certificates
        if (file_exists('C:/xampp/php/extras/ssl/openssl.cnf')) {
            putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');
            putenv('OPENSSL_MODULES=C:/xampp/php/extras/ssl');
        }
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFECom.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $where_array = [];

        $pesquisa = $this->input->get('pesquisa');
        $status = $this->input->get('status');
        $de = $this->input->get('data');
        $ate = $this->input->get('data2');

        if ($pesquisa) {
            $where_array['pesquisa'] = $pesquisa;
        }
        if ($status) {
            $where_array['status'] = $status;
        }
        if ($de) {
            $where_array['de'] = $de;
        }
        if ($ate) {
            $where_array['ate'] = $ate;
        }

        $this->data['configuration']['base_url'] = site_url('nfecom/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->Nfecom_model->count('nfecom_capa');

        if (count($where_array) > 0) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
            $this->data['configuration']['first_url'] = base_url("index.php/nfecom/gerenciar") . "?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->Nfecom_model->get('nfecom_capa', '*', $where_array, $this->data['configuration']['per_page'], $this->uri->segment(3));

        // Carregar dados auxiliares para o modal
        $this->db->select("c.CLN_ID as idClientes,
                          CASE
                            WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME
                            ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME)
                          END as nomeCliente");
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->order_by("CASE WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME) END ASC");
        $this->db->limit(1000);
        $query_clientes = $this->db->get();
        $this->data['clientes'] = $query_clientes ? $query_clientes->result() : [];

        // Serviços ficam na tabela produtos - pro_tipo = 2 significa serviço
        // Usar query direta para descobrir a coluna primária
        $primary_key_query = $this->db->query("SHOW KEYS FROM produtos WHERE Key_name = 'PRIMARY'");
        $primary_key = 'idProdutos'; // fallback padrão

        if ($primary_key_query->num_rows() > 0) {
            $key_info = $primary_key_query->row();
            $primary_key = $key_info->Column_name;
        } else {
            // Se não encontrou chave primária, tentar colunas comuns
            $possible_keys = ['idProdutos', 'id_produtos', 'id_produto', 'produtos_id', 'produto_id'];
            foreach ($possible_keys as $key) {
                if ($this->db->field_exists($key, 'produtos')) {
                    $primary_key = $key;
                    break;
                }
            }
        }

        // Agora buscar serviços com a coluna correta
        $this->db->select("$primary_key as idServicos, PRO_DESCRICAO as nome");
        $this->db->from('produtos');
        $this->db->where('pro_tipo', 2);
        $this->db->order_by('PRO_DESCRICAO', 'asc');
        $query_servicos = $this->db->get();
        $this->data['servicos'] = $query_servicos ? $query_servicos->result() : [];

        $this->data['view'] = 'nfecom/nfecom';

        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar NFECom.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        
        // Limpar flashdata de erro se não for um POST (evitar exibir erro ao carregar página)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->session->set_flashdata('error', '');
        }

        // Regras de validação
        $this->form_validation->set_rules('clientes_id', 'Cliente', 'trim|required');
        $this->form_validation->set_rules('enderecoClienteSelect', 'Endereço do Cliente', 'trim|required');
        $this->form_validation->set_rules('observacoes', 'Observações', 'trim|required');
        $this->form_validation->set_rules('numeroContrato', 'Número do Contrato', 'trim|required');
        $this->form_validation->set_rules('dataContratoIni', 'Data Início Contrato', 'trim|required');
        // Data de emissão é opcional (será gerada automaticamente se não fornecida)
        $this->form_validation->set_rules('dataEmissao', 'Data Emissão', 'trim');
        $this->form_validation->set_rules('comissaoAgencia', 'Comissão Agência', 'trim|numeric');
        $this->form_validation->set_rules('dataVencimento', 'Data Vencimento', 'trim|required');
        $this->form_validation->set_rules('dataPeriodoIni', 'Data Período Início', 'trim|required');
        $this->form_validation->set_rules('dataPeriodoFim', 'Data Período Fim', 'trim|required');
        $this->form_validation->set_rules('tpAssinante', 'Tipo de Assinante', 'trim|required');
        $this->form_validation->set_rules('tpServUtil', 'Tipo de Serviço', 'trim|required');
        $this->form_validation->set_rules('opc_id', 'Operação Comercial', 'trim|required');

        // Carregar alguns clientes iniciais para melhor UX (mais recentes primeiro)
        $this->db->select("c.CLN_ID as id,
                          CASE
                            WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME
                            ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME)
                          END as text,
                          p.PES_CPFCNPJ as cpf_cnpj");
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->order_by("CASE WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME) END ASC"); // Ordem alfabética para melhor UX
        $this->db->limit(50); // Limitar a 50 para não sobrecarregar
        $query_clientes = $this->db->get();
        $this->data['clientes_iniciais'] = $query_clientes ? $query_clientes->result() : [];

        // Serviços ficam na tabela produtos - pro_tipo = 2 significa serviço
        // Descobrir dinamicamente a coluna primária
        $primary_key_query = $this->db->query("SHOW KEYS FROM produtos WHERE Key_name = 'PRIMARY'");
        $produtos_primary_key = 'idProdutos'; // fallback

        if ($primary_key_query->num_rows() > 0) {
            $key_info = $primary_key_query->row();
            $produtos_primary_key = $key_info->Column_name;
        }

        $this->db->select("$produtos_primary_key as idServicos, PRO_DESCRICAO as nome");
        $this->db->from('produtos');
        $this->db->where('pro_tipo', 2);
        $this->db->order_by('PRO_DESCRICAO', 'asc');
        $query_servicos = $this->db->get();
        $this->data['servicos'] = $query_servicos ? $query_servicos->result() : [];

        // Carregar Operações Comerciais
        $this->data['operacoes'] = $this->OperacaoComercial_model->getAll();

        // Só validar se for um POST (submissão do formulário)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->form_validation->run('nfecom') == false) {
                $error_messages = validation_errors();
                if ($error_messages) {
                    $this->data['custom_error'] = $error_messages;
                    $this->session->set_flashdata('error', $error_messages);
                    log_message('debug', 'Erros de validação NFCom: ' . $error_messages);
                } else {
                    // Se não há erros de validação mas o run retornou false, pode ser problema com os dados
                    $post_data = $this->input->post();
                    log_message('debug', 'Validação falhou sem mensagens. Dados POST: ' . json_encode($post_data));
                    $this->data['custom_error'] = 'Erro na validação do formulário. Verifique se todos os campos obrigatórios foram preenchidos.';
                    $this->session->set_flashdata('error', 'Erro na validação do formulário. Verifique se todos os campos obrigatórios foram preenchidos.');
                }
            } else {
                // Validação passou, processar dados
                $data = $this->input->post();
                log_message('debug', 'Validação passou. Processando dados...');

                // Definir série padrão (não controlada na tela)
                $data['serie'] = isset($data['serie']) ? $data['serie'] : '1';

                // Processar data de emissão
                // Processar data de emissão (automática)
                if (!empty($data['dataEmissao'])) {
                try {
                    $dataEmissao = explode('/', $data['dataEmissao']);
                    $data['dataEmissao'] = $dataEmissao[2] . '-' . $dataEmissao[1] . '-' . $dataEmissao[0];
                } catch (Exception $e) {
                    $data['dataEmissao'] = date('Y-m-d');
                }
                } else {
                    $data['dataEmissao'] = date('Y-m-d');
                }

                // Processar datas do contrato
                if ($data['dataContratoIni']) {
                try {
                    $dataContrato = explode('/', $data['dataContratoIni']);
                    $data['dataContratoIni'] = $dataContrato[2] . '-' . $dataContrato[1] . '-' . $dataContrato[0];
                } catch (Exception $e) {
                    $data['dataContratoIni'] = date('Y-m-d');
                }
                }

                // Processar datas de vencimento
                if ($data['dataVencimento']) {
                try {
                    $dataVenc = explode('/', $data['dataVencimento']);
                    $data['dataVencimento'] = $dataVenc[2] . '-' . $dataVenc[1] . '-' . $dataVenc[0];
                } catch (Exception $e) {
                    $data['dataVencimento'] = date('Y-m-d', strtotime('+30 days'));
                }
                }

                // Processar período de uso
                if ($data['dataPeriodoIni']) {
                try {
                    $dataPerIni = explode('/', $data['dataPeriodoIni']);
                    $data['dataPeriodoIni'] = $dataPerIni[2] . '-' . $dataPerIni[1] . '-' . $dataPerIni[0];
                } catch (Exception $e) {
                    $data['dataPeriodoIni'] = date('Y-m-d');
                }
                }

                if ($data['dataPeriodoFim']) {
                try {
                    $dataPerFim = explode('/', $data['dataPeriodoFim']);
                    $data['dataPeriodoFim'] = $dataPerFim[2] . '-' . $dataPerFim[1] . '-' . $dataPerFim[0];
                } catch (Exception $e) {
                    $data['dataPeriodoFim'] = date('Y-m-d', strtotime('+30 days'));
                }
                }

                // Processar data fim de contrato (opcional)
                if (!empty($data['dataContratoFim']) && strpos($data['dataContratoFim'], '/') !== false) {
                try {
                    $dataContFim = explode('/', $data['dataContratoFim']);
                    $data['dataContratoFim'] = $dataContFim[2] . '-' . $dataContFim[1] . '-' . $dataContFim[0];
                } catch (Exception $e) {
                    $data['dataContratoFim'] = null;
                }
                }

                // Processar múltiplos serviços
                $servicos = isset($data['servicos']) ? $data['servicos'] : [];
                log_message('debug', 'Serviços recebidos: ' . json_encode($servicos));
                
                // Validar se os serviços têm dados válidos (se houver serviços)
                $servicosValidos = 0;
                if (!empty($servicos) && is_array($servicos)) {
                    foreach ($servicos as $index => $servico) {
                        log_message('debug', "Validando serviço $index: " . json_encode($servico));
                        if (!empty($servico['id']) && !empty($servico['quantidade']) && !empty($servico['valorUnitario'])) {
                            $servicosValidos++;
                        }
                    }
                }
                
                log_message('debug', "Total de serviços válidos: $servicosValidos");
                
                $totalValorBruto = 0;
                $nomesServicos = [];

                foreach ($servicos as $servico) {
                if (!empty($servico['id']) && !empty($servico['quantidade']) && !empty($servico['valorUnitario'])) {
                    // Converter valores recebidos (podem vir como string)
                    $quantidade = is_numeric($servico['quantidade']) ? floatval($servico['quantidade']) : 0;
                    $valorUnitario = is_numeric($servico['valorUnitario']) ? floatval($servico['valorUnitario']) : 0;
                    $valorDesconto = isset($servico['valorDesconto']) && is_numeric($servico['valorDesconto']) ? floatval($servico['valorDesconto']) : (isset($servico['v_desc']) && is_numeric($servico['v_desc']) ? floatval($servico['v_desc']) : 0);
                    $valorOutros = isset($servico['valorOutros']) && is_numeric($servico['valorOutros']) ? floatval($servico['valorOutros']) : (isset($servico['v_outro']) && is_numeric($servico['v_outro']) ? floatval($servico['v_outro']) : 0);
                    
                    log_message('debug', "Serviço processado - Quantidade: $quantidade, Valor Unitário: $valorUnitario, Desconto: $valorDesconto, Outros: $valorOutros");

                    // Valor Item = Quantidade × Valor Unitário
                    $valorItem = $quantidade * $valorUnitario;

                    // Valor Produto = Valor Item - Desconto + Outros
                    $valorProduto = $valorItem - $valorDesconto + $valorOutros;

                    $totalValorBruto += $valorProduto;

                    // Buscar nome do serviço
                    $this->db->select('PRO_DESCRICAO as descricao');
                    $this->db->from('produtos');
                    $this->db->where($produtos_primary_key, $servico['id']);
                    $servico_query = $this->db->get();
                    $servico_info = $servico_query->row();
                    if ($servico_info) {
                        $nomesServicos[] = $servico_info->descricao . ' (Qtd: ' . $quantidade . ')';
                    }
                }
                }

                // Se não há serviços válidos, usar o valor bruto do formulário
                if ($totalValorBruto == 0) {
                    $totalValorBruto = floatval($data['valorBruto']);
                    $nomesServicos[] = 'Serviços diversos';
                }

                // Calcular valores usando o total dos serviços
                $valorBruto = $totalValorBruto;
                $comissaoAgencia = floatval($data['comissaoAgencia']);
                $valorLiquido = $valorBruto - $comissaoAgencia;

                // Cálculos tributários (valores fixos baseados no XML de exemplo)
                // PIS e COFINS são apenas informativos, não alteram o valor da nota
                $pis = $valorLiquido * 0.0065; // 0.65%
                $cofins = $valorLiquido * 0.03; // 3.0%
                $irrf = 0.00; // IRRF zerado por enquanto
                $valorNF = $valorLiquido; // Valor da nota = valor líquido (sem descontar PIS/COFINS)

                $nomeServico = implode('; ', $nomesServicos);

                // Buscar dados completos do cliente incluindo endereço selecionado
                $enderecoId = $data['enderecoClienteSelect'] ?? null;
                $this->db->select('p.PES_CPFCNPJ, p.PES_NOME, p.PES_RAZAO_SOCIAL, p.PES_FISICO_JURIDICO, e.END_LOGRADOURO as logradouro, e.END_NUMERO as numero, e.END_COMPLEMENTO as complemento, e.END_CEP as cep, b.BAI_NOME as bairro, m.MUN_NOME as municipio_nome, m.MUN_IBGE, es.EST_UF as estado_uf');
                $this->db->from('clientes c');
                $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID');
                $this->db->join('enderecos e', 'e.PES_ID = p.PES_ID', 'left');
                $this->db->join('bairros b', 'b.BAI_ID = e.BAI_ID', 'left');
                $this->db->join('municipios m', 'm.MUN_ID = e.MUN_ID', 'left');
                $this->db->join('estados es', 'es.EST_ID = e.EST_ID', 'left');
                $this->db->where('c.CLN_ID', $data['clientes_id']);
                if (!empty($enderecoId)) {
                    $this->db->where('e.END_ID', $enderecoId);
                } else {
                    $this->db->where('e.END_PADRAO', 1);
                }
                $this->db->limit(1);
                $cliente_query = $this->db->get();
                $cliente = $cliente_query->row();

                if ($cliente) {
                    $data['nomeCliente'] = $cliente->PES_FISICO_JURIDICO == 'F' ? $cliente->PES_NOME : ($cliente->PES_RAZAO_SOCIAL ?: $cliente->PES_NOME);
                    $data['cnpjCliente'] = $cliente->PES_CPFCNPJ ?? '';
                    $data['logradouroCliente'] = $cliente->logradouro ?? '';
                    $data['numeroCliente'] = $cliente->numero ?? '';
                    $data['bairroCliente'] = $cliente->bairro ?? '';
                    $data['municipioCliente'] = $cliente->municipio_nome ?? '';
                    $data['codMunCliente'] = $cliente->MUN_IBGE ?? '';
                    $data['cepCliente'] = $cliente->cep ?? '';
                    $data['ufCliente'] = $cliente->estado_uf ?? '';
                }


                // Carregar dados do emitente da tabela empresas
                $emit = $this->Nfe_model->getEmit();

                if (!$emit) {
                    $this->session->set_flashdata('error', 'Nenhuma empresa emitente configurada. Por favor, cadastre uma empresa.');
                    redirect(site_url('nfecom/adicionar'));
                }

                // ... busca de cliente ...

                // Dados da NFCom
                $cnpjSemMascara = preg_replace('/\D/', '', $data['cnpjCliente'] ?? '');
                $configFiscal = $this->getConfiguracaoNfcom();
                $codigoUf = $this->getCodigoUf($emit['enderEmit']['UF'] ?? '');
                $nfecomData = [
                'NFC_CUF' => $codigoUf ?: ($emit['enderEmit']['UF'] ?? ''),
                'NFC_TIPO_AMBIENTE' => $configFiscal ? $configFiscal->CFG_AMBIENTE : $this->data['configuration']['ambiente'],
                'NFC_MOD' => '62',
                'NFC_SERIE' => $configFiscal ? $configFiscal->CFG_SERIE : $data['serie'],
                'NFC_NNF' => 0,
                'NFC_CNF' => str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'NFC_DHEMI' => $data['dataEmissao'] . ' ' . date('H:i:s'),
                'NFC_TP_EMIS' => 1,
                'NFC_N_SITE_AUTORIZ' => 0,
                'NFC_C_MUN_FG' => $emit['enderEmit']['cMun'],
                'NFC_FIN_NFCOM' => 0,
                'NFC_TP_FAT' => 0,
                'NFC_VER_PROC' => '1.0.0',
                'NFC_CNPJ_EMIT' => $emit['CNPJ'],
                'NFC_IE_EMIT' => $emit['IE'],
                'NFC_CRT_EMIT' => $emit['CRT'],
                'NFC_X_NOME_EMIT' => $emit['xNome'],
                'NFC_X_FANT_EMIT' => $emit['xNome'], // Assumindo nome fantasia = nome
                'NFC_X_LGR_EMIT' => $emit['enderEmit']['xLgr'],
                'NFC_NRO_EMIT' => $emit['enderEmit']['nro'],
                'NFC_X_CPL_EMIT' => $emit['enderEmit']['xCpl'],
                'NFC_X_BAIRRO_EMIT' => $emit['enderEmit']['xBairro'],
                'NFC_C_MUN_EMIT' => $emit['enderEmit']['cMun'],
                'NFC_X_MUN_EMIT' => $emit['enderEmit']['xMun'],
                'NFC_CEP_EMIT' => $emit['enderEmit']['CEP'],
                'NFC_UF_EMIT' => $emit['enderEmit']['UF'],
                'NFC_FONE_EMIT' => $emit['enderEmit']['fone'],
                'NFC_X_NOME_DEST' => $data['nomeCliente'],
                'NFC_CNPJ_DEST' => $cnpjSemMascara,
                'NFC_IND_IE_DEST' => 9,
                'NFC_X_LGR_DEST' => $data['logradouroCliente'],
                'NFC_NRO_DEST' => $data['numeroCliente'],
                'NFC_X_BAIRRO_DEST' => $data['bairroCliente'],
                'NFC_C_MUN_DEST' => $data['codMunCliente'],
                'NFC_X_MUN_DEST' => $data['municipioCliente'],
                'NFC_CEP_DEST' => $data['cepCliente'],
                'NFC_UF_DEST' => $data['ufCliente'],
                'NFC_I_COD_ASSINANTE' => !empty($data['iCodAssinante']) ? $data['iCodAssinante'] : $cnpjSemMascara,
                'NFC_TP_ASSINANTE' => $data['tpAssinante'],
                'NFC_TP_SERV_UTIL' => $data['tpServUtil'],
                'NFC_N_CONTRATO' => $data['numeroContrato'],
                'NFC_D_CONTRATO_INI' => $data['dataContratoIni'],
                'NFC_D_CONTRATO_FIM' => !empty($data['dataContratoFim']) ? $data['dataContratoFim'] : null,
                'NFC_V_PROD' => $valorLiquido,
                'NFC_V_COFINS' => $cofins,
                'NFC_V_PIS' => $pis,
                'NFC_V_FUST' => 0.00,
                'NFC_V_FUNTEL' => 0.00,
                'NFC_V_RET_PIS' => 0.00,
                'NFC_V_RET_COFINS' => 0.00,
                'NFC_V_RET_CSLL' => 0.00,
                'NFC_V_IRRF' => $irrf,
                'NFC_V_RET_TRIB_TOT' => $irrf,
                'NFC_V_DESC' => 0.00,
                'NFC_V_OUTRO' => 0.00,
                'NFC_V_NF' => $valorNF,
                'NFC_COMPET_FAT' => date('Ym', strtotime($data['dataEmissao'])),
                'NFC_D_VENC_FAT' => $data['dataVencimento'],
                'NFC_D_PER_USO_INI' => $data['dataPeriodoIni'],
                'NFC_D_PER_USO_FIM' => $data['dataPeriodoFim'],
                'NFC_COD_BARRAS' => '1',
                'NFC_STATUS' => 1, // Salvo
                'CLN_ID' => $data['clientes_id'],
                'OPC_ID' => $this->input->post('opc_id'),
                'NFC_CHAVE_PIX' => $this->input->post('nfc_chave_pix'),
                'NFC_LINHA_DIGITAVEL' => $this->input->post('nfc_linha_digitavel')
                ];

                // Coletar mensagens fiscais dos itens antes de construir informações complementares
                $mensagensFiscais = [];
                foreach ($servicos as $servico) {
                    if (!empty($servico['clf_id'])) {
                        // Buscar mensagem fiscal da classificação
                        $this->db->select('CLF_MENSAGEM');
                        $this->db->from('classificacao_fiscal');
                        $this->db->where('CLF_ID', $servico['clf_id']);
                        $clfQuery = $this->db->get();
                        if ($clfQuery->num_rows() > 0) {
                            $clf = $clfQuery->row();
                            if (!empty($clf->CLF_MENSAGEM)) {
                                // Evitar duplicatas
                                if (!in_array($clf->CLF_MENSAGEM, $mensagensFiscais)) {
                                    $mensagensFiscais[] = $clf->CLF_MENSAGEM;
                                }
                            }
                        }
                    }
                }

                // Construir informações complementares com mensagens fiscais
                $nfecomData['NFC_INF_CPL'] = $this->buildInfoComplementar($data, $valorBruto, $comissaoAgencia, $valorLiquido, $mensagensFiscais);

                // Calcular CDV e Chave
                $nfecomData['NFC_CDV'] = $this->calculateDV($nfecomData);
                $nfecomData['NFC_CH_NFCOM'] = $this->generateChave($nfecomData);

                // Salvar NFCom
                $idNfecom = $this->Nfecom_model->add('nfecom_capa', $nfecomData);

            if ($idNfecom) {
                // Salvar múltiplos itens (um para cada serviço)
                $itemNumero = 1;

                foreach ($servicos as $servico) {
                    if (!empty($servico['id']) && !empty($servico['quantidade']) && !empty($servico['valorUnitario'])) {
                        $quantidade = floatval($servico['quantidade']);
                        $valorUnitario = floatval($servico['valorUnitario']);
                        $valorDesconto = floatval($servico['v_desc'] ?? 0);
                        $valorOutros = floatval($servico['v_outro'] ?? 0);
                        $cfop = $servico['cfop'] ?? '5307';
                        $unidade = $servico['u_med'] ?? 'UN';
                        $cClass = $servico['c_class'] ?? '0600402';
                        $cstIcms = $servico['cst_icms'] ?? '00';
                        $clfId = $servico['clf_id'] ?? null; // ID da classificação fiscal encontrada

                        // Valor Item = Quantidade × Valor Unitário
                        $valorItem = $quantidade * $valorUnitario;

                        // Valor Produto = Valor Item - Desconto + Outros
                        $valorProduto = $valorItem - $valorDesconto + $valorOutros;

                        // Buscar nome do serviço
                        if (!empty($servico['nome'])) {
                            $nomeServicoItem = $servico['nome'];
                        } else {
                            $this->db->select('PRO_DESCRICAO as descricao');
                            $this->db->from('produtos');
                            $this->db->where($produtos_primary_key, $servico['id']);
                            $servico_query = $this->db->get();
                            $servico_info = $servico_query->row();
                            $nomeServicoItem = $servico_info ? $servico_info->descricao : 'Serviço não encontrado';
                        }

                        // Calcular tributos proporcionais para este item
                        $proporcao = ($valorBruto > 0) ? ($valorProduto / $valorBruto) : 0;
                        $pisItem = $pis * $proporcao;
                        $cofinsItem = $cofins * $proporcao;
                        $irrfItem = $irrf * $proporcao;

                        $itemData = [
                            'NFC_ID' => $idNfecom,
                            'NFI_N_ITEM' => $itemNumero,
                            'NFI_C_PROD' => $servico['id'],
                            'NFI_X_PROD' => $nomeServicoItem,
                            'NFI_C_CLASS' => $cClass,
                            'NFI_CFOP' => $cfop,
                            'NFI_U_MED' => $unidade,
                            'NFI_Q_FATURADA' => $quantidade,
                            'NFI_V_ITEM' => $valorItem,
                            'NFI_V_DESC' => $valorDesconto,
                            'NFI_V_OUTRO' => $valorOutros,
                            'NFI_V_PROD' => $valorProduto,
                            'NFI_CST_ICMS' => $cstIcms,
                            'NFI_CST_PIS' => '01',
                            'NFI_V_BC_PIS' => $valorProduto,
                            'NFI_P_PIS' => 0.65,
                            'NFI_V_PIS' => $pisItem,
                            'NFI_CST_COFINS' => '01',
                            'NFI_V_BC_COFINS' => $valorProduto,
                            'NFI_P_COFINS' => 3.00,
                            'NFI_V_COFINS' => $cofinsItem,
                            'NFI_V_BC_FUST' => 0.00,
                            'NFI_P_FUST' => 0.00,
                            'NFI_V_FUST' => 0.00,
                            'NFI_V_BC_FUNTEL' => 0.00,
                            'NFI_P_FUNTEL' => 0.00,
                            'NFI_V_FUNTEL' => 0.00,
                            'NFI_V_BC_IRRF' => $valorProduto,
                            'NFI_V_IRRF' => $irrfItem,
                            'NFI_DATA_CADASTRO' => date('Y-m-d H:i:s'),
                            'NFI_DATA_ATUALIZACAO' => date('Y-m-d H:i:s')
                        ];

                        // Adicionar CLF_ID se existir o campo na tabela e se tiver valor
                        if ($clfId) {
                            // Verificar se o campo existe antes de adicionar
                            $fields = $this->db->list_fields('nfecom_itens');
                            if (in_array('CLF_ID', $fields) || in_array('clf_id', $fields)) {
                                $itemData['CLF_ID'] = $clfId;
                                log_message('info', 'Item NFCom #' . $itemNumero . ' - CLF_ID adicionado: ' . $clfId);
                            } else {
                                log_message('info', 'Campo CLF_ID não existe na tabela nfecom_itens. Não foi possível salvar o ID da classificação fiscal.');
                            }
                        } else {
                            log_message('info', 'Item NFCom #' . $itemNumero . ' - CLF_ID não fornecido no serviço');
                        }

                        $this->Nfecom_model->add('nfecom_itens', $itemData);
                        log_message('info', 'Item NFCom #' . $itemNumero . ' salvo com sucesso. NFI_ID: ' . $this->db->insert_id());
                        $itemNumero++;
                    }
                }

                // Se não há serviços válidos, criar um item genérico
                if ($itemNumero == 1) {
                    $itemData = [
                        'NFC_ID' => $idNfecom,
                        'NFI_N_ITEM' => 1,
                        'NFI_C_PROD' => '1',
                        'NFI_X_PROD' => 'Serviços diversos',
                        'NFI_C_CLASS' => '0600402',
                        'NFI_CFOP' => '5307',
                        'NFI_U_MED' => '4',
                        'NFI_Q_FATURADA' => 1.0000,
                        'NFI_V_ITEM' => $valorLiquido,
                        'NFI_V_DESC' => 0.00,
                        'NFI_V_OUTRO' => 0.00,
                        'NFI_V_PROD' => $valorLiquido,
                        'NFI_CST_ICMS' => '41',
                        'NFI_CST_PIS' => '01',
                        'NFI_V_BC_PIS' => $valorLiquido,
                        'NFI_P_PIS' => 0.65,
                        'NFI_V_PIS' => $pis,
                        'NFI_CST_COFINS' => '01',
                        'NFI_V_BC_COFINS' => $valorLiquido,
                        'NFI_P_COFINS' => 3.00,
                        'NFI_V_COFINS' => $cofins,
                        'NFI_V_BC_FUST' => 0.00,
                        'NFI_P_FUST' => 0.00,
                        'NFI_V_FUST' => 0.00,
                        'NFI_V_BC_FUNTEL' => 0.00,
                        'NFI_P_FUNTEL' => 0.00,
                        'NFI_V_FUNTEL' => 0.00,
                        'NFI_V_BC_IRRF' => $valorLiquido,
                        'NFI_V_IRRF' => $irrf,
                        'NFI_DATA_CADASTRO' => date('Y-m-d H:i:s'),
                        'NFI_DATA_ATUALIZACAO' => date('Y-m-d H:i:s')
                    ];

                    $this->Nfecom_model->add('nfecom_itens', $itemData);
                }

                $this->session->set_flashdata('success', 'NFECom adicionada com sucesso!');
                redirect(site_url('nfecom'));
            } else {
                $this->data['custom_error'] = true;
                $this->session->set_flashdata('error', 'Erro ao salvar NFECom!');
            }
            } // Fechamento do else da validação (linha 199)
        } // Fechamento do if ($_SERVER['REQUEST_METHOD'] === 'POST') (linha 185)

        $this->data['view'] = 'nfecom/adicionarNfecom';
        return $this->layout();
    }

    public function visualizar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFECom.');
            redirect(base_url());
        }

        $this->data['result'] = $this->Nfecom_model->getByIdWithOperation($this->uri->segment(3));
        $this->data['itens'] = $this->Nfecom_model->getItens($this->uri->segment(3));

        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        $this->data['view'] = 'nfecom/visualizar';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar NFECom.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // Só permite editar NFCom rejeitadas
        if ($nfecom->NFC_STATUS != 4) {
            $this->session->set_flashdata('error', 'Apenas NFCom rejeitadas podem ser editadas.');
            redirect(site_url('nfecom/visualizar/' . $id));
        }

        $this->db->trans_begin();

        try {
            // Carregar Operações Comerciais para a tela de edição
            $this->data['operacoes'] = $this->OperacaoComercial_model->getAll();

            // Atualizar dados básicos da NFCom (NÃO alterar o número da NFE)
            $dados = [
                'NFC_SERIE' => $this->input->post('nfc_serie'),
                'NFC_N_CONTRATO' => $this->input->post('nfc_n_contrato'),
                'NFC_D_CONTRATO_INI' => $this->input->post('nfc_d_contrato_ini'),
                'NFC_COMPET_FAT' => $this->input->post('nfc_compet_fat'),
                'NFC_D_VENC_FAT' => $this->input->post('nfc_d_venc_fat'),
                'NFC_D_PER_USO_INI' => $this->input->post('nfc_d_per_uso_ini'),
                'NFC_D_PER_USO_FIM' => $this->input->post('nfc_d_per_uso_fim'),
                'NFC_INF_CPL' => $this->input->post('nfc_inf_cpl'),
                'NFC_I_COD_ASSINANTE' => $this->input->post('nfc_i_cod_assinante'),
                'NFC_TP_ASSINANTE' => $this->input->post('nfc_tp_assinante'),
                'NFC_TP_SERV_UTIL' => $this->input->post('nfc_tp_serv_util'),
                'NFC_D_CONTRATO_FIM' => !empty($this->input->post('nfc_d_contrato_fim')) ? $this->input->post('nfc_d_contrato_fim') : null,
                'NFC_CHAVE_PIX' => $this->input->post('nfc_chave_pix'),
                'NFC_LINHA_DIGITAVEL' => $this->input->post('nfc_linha_digitavel'),
                'OPC_ID' => $this->input->post('opc_id'),
            ];

            // Atualizar dados do destinatário se fornecidos
            $destinatario = [
                'nfc_x_nome_dest' => $this->input->post('nfc_x_nome_dest'),
                'nfc_cnpj_dest' => $this->input->post('nfc_cnpj_dest'),
                'nfc_x_lgr_dest' => $this->input->post('nfc_x_lgr_dest'),
                'nfc_nro_dest' => $this->input->post('nfc_nro_dest'),
                'nfc_x_bairro_dest' => $this->input->post('nfc_x_bairro_dest'),
                'nfc_x_mun_dest' => $this->input->post('nfc_x_mun_dest'),
                'nfc_uf_dest' => $this->input->post('nfc_uf_dest'),
                'nfc_cep_dest' => $this->input->post('nfc_cep_dest'),
            ];

            // Mesclar dados do destinatário apenas se houver valores
            foreach ($destinatario as $key => $value) {
                if ($value !== null && $value !== '') {
                    $dados[$key] = $value;
                }
            }

            // Atualizar NFCom
            if (!$this->Nfecom_model->edit('nfecom_capa', $dados, 'nfc_id', $id)) {
                throw new Exception('Erro ao atualizar dados da NFCom.');
            }

            // Processar itens se fornecidos
            $itens = $this->input->post('itens');
            if ($itens && is_array($itens)) {
                // Remover itens existentes
                $this->db->where('nfc_id', $id);
                $this->db->delete('nfecom_itens');

                // Inserir novos itens
                foreach ($itens as $item) {
                    if (!empty($item['c_prod']) || !empty($item['x_prod'])) {
                        $itemData = [
                            'nfc_id' => $id,
                            'nfi_n_item' => $item['n_item'] ?? 1,
                            'nfi_c_prod' => $item['c_prod'] ?? '',
                            'nfi_x_prod' => $item['x_prod'] ?? '',
                            'nfi_c_class' => $item['c_class'] ?? '',
                            'nfi_cfop' => $item['cfop'] ?? '5301',
                            'nfi_u_med' => $item['u_med'] ?? '',
                            'nfi_q_faturada' => str_replace(',', '.', $item['q_faturada'] ?? '1.0000'),
                            'nfi_v_item' => str_replace(',', '.', $item['v_item'] ?? '0.00'),
                            'nfi_v_desc' => str_replace(',', '.', $item['v_desc'] ?? '0.00'),
                            'nfi_v_outro' => str_replace(',', '.', $item['v_outro'] ?? '0.00'),
                            'nfi_cst_icms' => $item['cst_icms'] ?? '',
                        ];

                        // Calcular valor total
                        $itemData['nfi_v_prod'] = $itemData['nfi_q_faturada'] * $itemData['nfi_v_item'];

                        $this->db->insert('nfecom_itens', $itemData);
                        if ($this->db->affected_rows() == 0) {
                            throw new Exception('Erro ao inserir item da NFCom.');
                        }
                    }
                }
            }

            // Limpar dados de rejeição anterior para reenvio
            if ($this->input->post('reenviar') == '1') {
                $limparDados = [
                    'nfc_x_motivo' => null,
                    'nfc_c_stat' => null,
                    'nfc_n_prot' => null,
                    'nfc_dh_recbto' => null,
                    'nfc_xml' => null,
                    'nfc_status' => 0, // Volta para rascunho
                ];
                $this->Nfecom_model->edit('nfecom_capa', $limparDados, 'nfc_id', $id);
            }

            $this->db->trans_commit();

            $this->session->set_flashdata('success', 'NFCom atualizada com sucesso.');

            // Se foi solicitado reenvio automático
            if ($this->input->post('reenviar') == '1') {
                // Redireciona para o método gerarXml que fará o reenvio
                redirect(site_url('nfecom/gerarXml/' . $id . '?reenviar=1'));
            } else {
                redirect(site_url('nfecom/visualizar/' . $id));
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Erro ao atualizar NFCom: ' . $e->getMessage());
            redirect(site_url('nfecom/visualizar/' . $id));
        }
    }

    public function gerarXml()
    {
        // Verificar se é uma requisição AJAX
        $isAjax = $this->input->is_ajax_request() || $this->input->post('ajax') === 'true';

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'Você não tem permissão para emitir NFECom.'];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFECom.');
                redirect(base_url());
            }
        }

        $id = $this->input->post('id') ?? $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'NFECom não encontrada.'];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('error', 'NFECom não encontrada.');
                redirect(site_url('nfecom'));
            }
        }

        // Se ainda não foi autorizada, fazer a autorização automática
        // Inclui reemissões (status 4)
        if ($nfecom->NFC_STATUS < 3 || $nfecom->NFC_STATUS == 4) {
            $isReemissao = ($nfecom->NFC_STATUS == 4);
            log_message('info', 'NFCom ' . ($isReemissao ? 'Reemissão' : 'Emissão') . ' iniciada - ID: ' . $id . ', Status: ' . $nfecom->NFC_STATUS);
            try {
                // Atualizar dados fiscais e gerar XML se necessário
                $configFiscal = $this->getConfiguracaoNfcom();
                if ($configFiscal) {
                    $ufEmit = $nfecom->NFC_UF_EMIT ?? 'GO';
                    $codigoUf = $this->get_cUF($ufEmit);

                    // Para NFCom rejeitadas (reemissão), manter o número, chave e data originais
                    // Para NFCom novas, apenas pegar número atual (incremento será feito no final do processo)
                    if ($nfecom->NFC_STATUS < 2) {
                        $numeroNota = $configFiscal->CFG_NUMERO_ATUAL;
                        
                        $atualizacao = [
                            'NFC_TIPO_AMBIENTE' => $configFiscal->CFG_AMBIENTE,
                            'NFC_SERIE' => $configFiscal->CFG_SERIE,
                            'NFC_NNF' => $numeroNota,
                            'NFC_CUF' => $codigoUf,
                            'NFC_DHEMI' => date('Y-m-d H:i:s'), // Usar data atual na emissão
                        ];

                        // Calcular nova chave para NFCom nova
                        $chaveData = [
                            'NFC_CUF' => $atualizacao['NFC_CUF'],
                            'NFC_DHEMI' => $atualizacao['NFC_DHEMI'],
                            'NFC_CNPJ_EMIT' => $nfecom->NFC_CNPJ_EMIT,
                            'NFC_MOD' => $nfecom->NFC_MOD,
                            'NFC_SERIE' => $atualizacao['NFC_SERIE'],
                            'NFC_NNF' => $atualizacao['NFC_NNF'],
                            'NFC_TP_EMIS' => $nfecom->NFC_TP_EMIS,
                            'NFC_CNF' => $nfecom->NFC_CNF,
                            'NFC_N_SITE_AUTORIZ' => 0,
                        ];

                        $atualizacao['NFC_CDV'] = $this->calculateDV($chaveData);
                        $chaveData['NFC_CDV'] = $atualizacao['NFC_CDV'];
                        $atualizacao['NFC_CH_NFCOM'] = $this->generateChave($chaveData);
                    } else {
                        // NFCom rejeitada (reemissão) - manter número, chave, série e data originais
                        $atualizacao = [
                            'NFC_TIPO_AMBIENTE' => $configFiscal->CFG_AMBIENTE,
                            // Manter série original (não atualizar)
                            // Manter número original (não atualizar)
                            // Manter chave original (não atualizar)
                            // Manter data de emissão original (não atualizar)
                            // Limpar apenas os campos de rejeição
                            'NFC_X_MOTIVO' => null,
                            'NFC_C_STAT' => null,
                            'NFC_N_PROT' => null,
                            'NFC_DH_RECBTO' => null,
                            'NFC_XML' => null,
                        ];
                        // Não recalcular chave, manter a original
                    }

                    $this->Nfecom_model->edit('nfecom_capa', $atualizacao, 'NFC_ID', $id);
                    $nfecom = $this->Nfecom_model->getById($id);
                }

                // Chamar o método de autorização
                $this->autorizar($id, false);

                // Recarregar dados após autorização
                $nfecom = $this->Nfecom_model->getById($id);

            } catch (Exception $e) {
                if ($isAjax) {
                    $response = ['success' => false, 'message' => 'Erro na autorização automática: ' . $e->getMessage()];
                    echo json_encode($response);
                    return;
                } else {
                    $this->session->set_flashdata('error', 'Erro na autorização automática: ' . $e->getMessage());
                    redirect(site_url('nfecom'));
                }
            }
        }

        // Preparar dados para o modal
        $statusDescricao = $this->getStatusDescricao($nfecom->NFC_STATUS);

        // Para notas rejeitadas, incluir o cStat da SEFAZ
        if ($nfecom->NFC_STATUS == 4 && !empty($nfecom->NFC_C_STAT)) {
            $statusDescricao = 'Rejeitada (cStat: ' . $nfecom->NFC_C_STAT . ')';
        }

        $modalData = [
            'numero_nfcom' => $nfecom->NFC_NNF,
            'chave_nfcom' => $nfecom->NFC_CH_NFCOM,
            'status' => $statusDescricao,
            'cstat' => $nfecom->NFC_C_STAT ?? '',
            'motivo' => $nfecom->NFC_X_MOTIVO ?? 'NFCom processada com sucesso',
            'protocolo' => $nfecom->NFC_N_PROT ?? '',
            'id' => $id
        ];

        // Verificar se tem retorno detalhado
        if (!empty($nfecom->NFC_XML)) {
            $modalData['retorno'] = 'XML autorizado gerado com sucesso.';
        }

        if ($isAjax) {
            $response = ['success' => true, 'modal' => $modalData];
            echo json_encode($response);
        } else {
            $this->session->set_flashdata('nfecom_modal', $modalData);
            redirect(site_url('nfecom'));
        }
    }

    public function gerarXmlPreEmissao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar XML de NFECom.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        try {
            // Se a nota ainda não tem número, usar o número da configuração fiscal ou um placeholder
            if (empty($nfecom->NFC_NNF) || $nfecom->NFC_NNF == 0) {
                $configFiscal = $this->getConfiguracaoNfcom();
                $numeroNota = $configFiscal ? $configFiscal->CFG_NUMERO_ATUAL : '000000000';
            } else {
                $numeroNota = $nfecom->NFC_NNF;
            }

            // Carregar biblioteca para gerar XML
            $this->load->library('NFComMake');

            // Preparar dados completos para o XML
            $dados = $this->prepararDadosEnvio($id);

            // Gerar XML (sem assinar, apenas pré-emissão)
            $nfcomMake = new NFComMake();
            $xml = $nfcomMake->build($dados);

            // Configurar headers para download
            $filename = 'NFCom_' . str_pad($numeroNota, 9, '0', STR_PAD_LEFT) . '_' . date('YmdHis') . '.xml';
            
            header('Content-Type: application/xml; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($xml));
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');

            // Output do XML
            echo $xml;
            exit;

        } catch (Exception $e) {
            log_message('error', 'Erro ao gerar XML pré-emissão: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $this->session->set_flashdata('error', 'Erro ao gerar XML: ' . $e->getMessage());
            redirect(site_url('nfecom/visualizar/' . $id));
        }
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir NFECom.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // Verificar se pode excluir (não autorizada nem cancelada)
        if ($nfecom->NFC_STATUS == 3) {
            $this->session->set_flashdata('error', 'Não é possível excluir NFCom autorizada.');
            redirect(site_url('nfecom'));
        }
        if ($nfecom->NFC_STATUS == 7) {
            $this->session->set_flashdata('error', 'Não é possível excluir NFCom cancelada.');
            redirect(site_url('nfecom'));
        }

        // Excluir itens primeiro
        $this->Nfecom_model->delete('nfecom_itens', 'NFC_ID', $id);

        // Excluir protocolos
        $this->Nfecom_model->delete('protocolos', 'NFC_ID', $id);

        // Excluir NFCom
        if ($this->Nfecom_model->delete('nfecom_capa', 'NFC_ID', $id)) {
            $this->session->set_flashdata('success', 'NFECom excluída com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir NFECom.');
        }

        redirect(site_url('nfecom'));
    }

    private function getStatusDescricao($status)
    {
        return match($status) {
            0 => 'Rascunho',
            1 => 'Salvo',
            2 => 'Enviado',
            3 => 'Autorizado',
            4 => 'Rejeitada',
            5 => 'Autorizada',
            7 => 'Cancelada',
            default => 'Desconhecido'
        };
    }

    public function consultar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para consultar NFECom.');
            redirect(base_url());
        }

        // Verificar se é requisição AJAX
        $isAjax = $this->input->post('ajax') === 'true';

        if ($isAjax) {
            $id = $this->input->post('id');
        } else {
            $id = $this->uri->segment(3);
        }

        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'NFECom não encontrada.'];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('error', 'NFECom não encontrada.');
                redirect(site_url('nfecom'));
            }
        }

        // Verificar se a NFCom já foi enviada para SEFAZ
        if ($nfecom->NFC_STATUS < 2) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'NFCom ainda não foi enviada para SEFAZ. Primeiro envie para autorização.'];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('error', 'NFCom ainda não foi enviada para SEFAZ. Primeiro envie para autorização.');
                redirect(site_url('nfecom'));
            }
        }

        // 0. Atualizar dados fiscais e gerar XML se rascunho ou rejeitado
        if ($nfecom->NFC_STATUS < 2 || $nfecom->NFC_STATUS == 4) {
            $configFiscal = $this->getConfiguracaoNfcom();
            if ($configFiscal) {
                $ufEmit = $nfecom->NFC_UF_EMIT ?? 'GO';
                $codigoUf = $this->get_cUF($ufEmit);

                // Para NFCom rejeitadas (reemissão), manter o número atual
                // Para NFCom novas, apenas pegar número atual (incremento será feito apenas na autorização)
                if ($nfecom->NFC_STATUS < 2) {
                    $numeroNota = $configFiscal->CFG_NUMERO_ATUAL;
                } else {
                    // NFCom rejeitada mantém o número atual
                    $numeroNota = $nfecom->NFC_NNF;
                }

                $atualizacao = [
                    'NFC_TIPO_AMBIENTE' => $configFiscal->CFG_AMBIENTE,
                    'NFC_SERIE' => $configFiscal->CFG_SERIE,
                    'NFC_NNF' => $numeroNota,
                    'NFC_CUF' => $codigoUf,
                ];

                // Para reemissão (NFCom rejeitada), limpar o motivo antigo
                if ($nfecom->NFC_STATUS == 4) {
                    $atualizacao['NFC_X_MOTIVO'] = null;
                    $atualizacao['NFC_C_STAT'] = null;
                    $atualizacao['NFC_N_PROT'] = null;
                    $atualizacao['NFC_DH_RECBTO'] = null;
                    $atualizacao['NFC_XML'] = null;
                }

                $chaveData = [
                    'NFC_CUF' => $atualizacao['NFC_CUF'],
                    'NFC_DHEMI' => $nfecom->NFC_DHEMI,
                    'NFC_CNPJ_EMIT' => $nfecom->NFC_CNPJ_EMIT,
                    'NFC_MOD' => $nfecom->NFC_MOD,
                    'NFC_SERIE' => $atualizacao['NFC_SERIE'],
                    'NFC_NNF' => $atualizacao['NFC_NNF'],
                    'NFC_TP_EMIS' => $nfecom->NFC_TP_EMIS,
                    'NFC_CNF' => $nfecom->NFC_CNF,
                ];

                // nSiteAutoriz é necessário para a chave e DV
                $nSiteAutoriz = 0; // Default
                $chaveData['NFC_N_SITE_AUTORIZ'] = $nSiteAutoriz;

                $atualizacao['NFC_CDV'] = $this->calculateDV($chaveData);
                $chaveData['NFC_CDV'] = $atualizacao['NFC_CDV'];
                $atualizacao['NFC_CH_NFCOM'] = $this->generateChave($chaveData);

                $this->Nfecom_model->edit('nfecom_capa', $atualizacao, 'NFC_ID', $id);
                $nfecom = $this->Nfecom_model->getById($id);
            }
        }

        // Validar certificado configurado para NFCOM
        $configFiscal = $this->getConfiguracaoNfcom();
        if (!$configFiscal || empty($configFiscal->CER_ARQUIVO) || empty($configFiscal->CER_SENHA)) {
            $this->session->set_flashdata('error', 'Nenhum certificado válido configurado para NFCOM.');
            redirect(site_url('nfecom/visualizar/' . $id));
        }

        try {
            $this->load->library('NFComService');
            $nfcomService = new NFComService([
                'ambiente' => $configFiscal->CFG_AMBIENTE,
                'disable_cert_validation' => true
            ]);
            $nfcomService->setCertificate($configFiscal->CER_ARQUIVO, $configFiscal->CER_SENHA);

            // Consulta Real na SEFAZ
            $retorno = $nfcomService->consult($nfecom->NFC_CH_NFCOM, $configFiscal->CFG_AMBIENTE);

            if (isset($retorno['error'])) {
                throw new Exception($retorno['error']);
            }

            $statusTexto = 'Rejeitado / Outros';
            $cStat = $retorno['cStat'];
            $xMotivo = $retorno['xMotivo'];

            if ($cStat == '100') { // Autorizado
                $statusTexto = 'Autorizado';
                $protocolo = $retorno['protocolo']['nProt'];
                $dhRecbto = $retorno['protocolo']['dhRecbto'];

                $dadosAtu = [
                    'NFC_STATUS' => 3, // Autorizado
                    'NFC_C_STAT' => $cStat,
                    'NFC_X_MOTIVO' => $xMotivo,
                    'NFC_N_PROT' => $protocolo,
                    'NFC_DH_RECBTO' => $dhRecbto
                ];

                $this->Nfecom_model->updateStatus($id, $dadosAtu);
                $this->registrarProtocolo($id, $protocolo, 'AUTORIZACAO', $xMotivo, $dhRecbto);

                $this->session->set_flashdata('success', 'NFCom consultada e Autorizada! Status: ' . $xMotivo);
            } elseif ($cStat == '101') { // Cancelamento homologado
                $statusTexto = 'Cancelada';
                $protocolo = $retorno['protocolo']['nProt'] ?? '';
                $dhRecbto = $retorno['protocolo']['dhRecbto'] ?? '';

                $dadosAtu = [
                    'NFC_STATUS' => 7, // Cancelada
                    'NFC_C_STAT' => $cStat,
                    'NFC_X_MOTIVO' => $xMotivo,
                    'NFC_N_PROT_CANC' => $protocolo
                ];

                $this->Nfecom_model->updateStatus($id, $dadosAtu);
                if ($protocolo) {
                    $this->registrarProtocolo($id, $protocolo, 'CANCELAMENTO', $xMotivo, $dhRecbto);
                }

                $this->session->set_flashdata('info', 'NFCom consultada e identificada como Cancelada! Status: ' . $xMotivo);
            } else {
                // Outros status ou rejeição
                $dadosAtu = [
                    'NFC_C_STAT' => $cStat,
                    'NFC_X_MOTIVO' => $xMotivo
                ];
                $this->Nfecom_model->updateStatus($id, $dadosAtu);
                $this->session->set_flashdata('info', 'Status da NFCom: ' . $cStat . ' - ' . $xMotivo);
            }

            // Para exibir no modal
            $retornoSefaz = json_encode([
                'cStat' => $cStat,
                'xMotivo' => $xMotivo,
                'nProt' => $retorno['protocolo']['nProt'] ?? '',
                'dhRecbto' => $retorno['protocolo']['dhRecbto'] ?? '',
                'xml' => $retorno['xml'] ?? ''
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            $modalData = [
                'numero_nfcom' => $nfecom->NFC_NNF,
                'chave_nfcom' => $nfecom->NFC_CH_NFCOM,
                'status' => $statusTexto,
                'cstat' => $cStat,
                'motivo' => $xMotivo,
                'protocolo' => $retorno['protocolo']['nProt'] ?? '',
                'retorno' => $retornoSefaz
            ];

            if ($isAjax) {
                $response = ['success' => true, 'modal' => $modalData];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('nfecom_modal', $modalData);
            }

        } catch (Exception $e) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'Erro ao consultar SEFAZ: ' . $e->getMessage()];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('error', 'Erro ao consultar SEFAZ: ' . $e->getMessage());
            }
        }

        if (!$isAjax) {
            redirect(site_url('nfecom'));
        }
    }

    public function danfe()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar DANFE.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);
        $itens = $this->Nfecom_model->getItens($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // Carregar a classe NFComPreview
        require_once APPPATH . 'libraries/NFComPreview.php';

        // Buscar dados da empresa emitente
        $emit = $this->Nfe_model->getEmit();

        if (!$emit) {
            $this->session->set_flashdata('error', 'Nenhuma empresa emitente configurada.');
            redirect(site_url('nfecom'));
        }

        // Configuração para a classe NFComPreview
        $config = [
            'empresa' => [
                'razao_social' => $emit['xNome'],
                'cnpj' => $emit['CNPJ'],
                'ie' => $emit['IE'],
                'logo' => !empty($emit['url_logo']) ? str_replace('/', DIRECTORY_SEPARATOR, FCPATH . $emit['url_logo']) : null,
                'endereco' => [
                    'logradouro' => $emit['enderEmit']['xLgr'],
                    'numero' => $emit['enderEmit']['nro'],
                    'complemento' => $emit['enderEmit']['xCpl'] ?? '',
                    'bairro' => $emit['enderEmit']['xBairro'],
                    'municipio' => $emit['enderEmit']['xMun'],
                    'uf' => $emit['enderEmit']['UF'],
                    'cep' => $emit['enderEmit']['CEP']
                ]
            ],
            'debug_logo' => [
                'url_logo_db' => $emit['url_logo'] ?? 'NULL',
                'caminho_completo' => !empty($emit['url_logo']) ? FCPATH . $emit['url_logo'] : 'NULL',
                'arquivo_existe' => !empty($emit['url_logo']) && file_exists(FCPATH . $emit['url_logo']) ? 'SIM' : 'NÃO'
            ],
            'serie' => $nfecom->NFC_SERIE,
            'numero_inicial' => $nfecom->NFC_NNF,
            'nSiteAutoriz' => $nfecom->NFC_N_SITE_AUTORIZ,
            'classe' => '0101011',
            'diretorios' => [
                'temp' => FCPATH . 'assets/temp'
            ]
        ];

        // Preparar dados do destinatário
        $destinatario = [
            'nome' => $nfecom->NFC_X_NOME_DEST,
            'cnpj' => $nfecom->NFC_CNPJ_DEST ?? '',
            'cpf' => '',
            'ie' => '',
            'endereco' => [
                'logradouro' => $nfecom->NFC_X_LGR_DEST ?? '',
                'numero' => $nfecom->NFC_NRO_DEST ?? '',
                'bairro' => $nfecom->NFC_X_BAIRRO_DEST ?? '',
                'municipio' => $nfecom->NFC_X_MUN_DEST ?? '',
                'uf' => $nfecom->NFC_UF_DEST ?? '',
                'cep' => $nfecom->NFC_CEP_DEST ?? '',
                'telefone' => ''
            ]
        ];

        // Preparar dados do assinante
        $assinante = [
            'iCodAssinante' => $nfecom->NFC_I_COD_ASSINANTE ?? '',
            'numero_contrato' => $nfecom->NFC_N_CONTRATO ?? '',
            'identificador_debito' => ''
        ];

        // Preparar dados de faturamento
        $faturamento = [
            'competencia' => $nfecom->NFC_COMPET_FAT ?? date('Y-m'),
            'periodo_inicio' => date('d/m/Y', strtotime($nfecom->NFC_D_PER_USO_INI)),
            'periodo_fim' => date('d/m/Y', strtotime($nfecom->NFC_D_PER_USO_FIM)),
            'vencimento' => date('d/m/Y', strtotime($nfecom->NFC_D_VENC_FAT)),
            'linha_digitavel' => $nfecom->NFC_COD_BARRAS ?? '',
            'cod_barras' => $nfecom->NFC_COD_BARRAS ?? ''
        ];

        // Preparar itens
        $itensFormatados = [];
        foreach ($itens as $item) {
            $itensFormatados[] = [
                'descricao' => $item->NFI_X_PROD,
                'cclass' => $item->NFI_C_CLASS,
                'unidade' => $item->NFI_U_MED,
                'quantidade' => $item->NFI_Q_FATURADA,
                'valor_unitario' => $item->NFI_V_ITEM / $item->NFI_Q_FATURADA,
                'valor_total' => $item->NFI_V_PROD,
                'desconto' => $item->NFI_V_DESC,
                'outros' => $item->NFI_V_OUTRO,
                'base_calculo' => $item->NFI_V_BC_PIS,
                'aliquota_icms' => 0,
                'valor_icms' => 0,
                'pis' => [
                    'valor' => $item->NFI_V_PIS
                ],
                'cofins' => [
                    'valor' => $item->NFI_V_COFINS
                ]
            ];
        }

        // Preparar totais
        $totais = [
            'valor_total' => $nfecom->NFC_V_NF,
            'valor_base_calculo' => $nfecom->NFC_V_PROD,
            'valor_produtos' => $nfecom->NFC_V_PROD,
            'valor_icms' => 0,
            'valor_isento' => 0,
            'valor_outros' => $nfecom->NFC_V_OUTRO,
            'valor_pis' => $nfecom->NFC_V_PIS,
            'valor_cofins' => $nfecom->NFC_V_COFINS,
            'valor_fust' => $nfecom->NFC_V_FUST,
            'valor_funtel' => $nfecom->NFC_V_FUNTEL
        ];

        // Preparar dados completos
        $dados = [
            'numero' => $nfecom->NFC_NNF,
            'chave' => $nfecom->NFC_CH_NFCOM,
            'status' => $nfecom->NFC_STATUS,
            'protocolo' => $nfecom->NFC_N_PROT,
            'data_autorizacao' => $nfecom->NFC_DH_RECBTO ? date('d/m/Y H:i:s', strtotime($nfecom->NFC_DH_RECBTO)) : '',
            'destinatario' => $destinatario,
            'assinante' => $assinante,
            'faturamento' => $faturamento,
            'itens' => $itensFormatados,
            'totais' => $totais,
            'informacoes_adicionais' => $nfecom->NFC_INF_CPL ?? ''
        ];

        try {
            // Gerar PDF
            $nfcomPreview = new \App\NFComPreview($config);
            $resultado = $nfcomPreview->gerarPdf($dados);

            // Verificar se o PDF foi gerado
            if (empty($resultado['pdf'])) {
                throw new Exception('PDF vazio gerado');
            }

            // Limpar qualquer output anterior
            if (ob_get_length()) {
                ob_clean();
            }

            // Enviar PDF para o navegador
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="danfe_nfcom_' . $nfecom->NFC_NNF . '.pdf"');
            header('Content-Length: ' . strlen($resultado['pdf']));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $resultado['pdf'];
            exit;
        } catch (Exception $e) {
            // Log do erro
            log_message('error', 'Erro ao gerar DANFE NFCom: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());

            // Exibir erro detalhado em desenvolvimento
            if (ENVIRONMENT === 'development') {
                echo '<h1>Erro ao gerar DANFE</h1>';
                echo '<p><strong>Mensagem:</strong> ' . $e->getMessage() . '</p>';
                echo '<p><strong>Arquivo:</strong> ' . $e->getFile() . ':' . $e->getLine() . '</p>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
                exit;
            }

            $this->session->set_flashdata('error', 'Erro ao gerar DANFE: ' . $e->getMessage());
            redirect(site_url('nfecom/visualizar/' . $id));
        }
    }

    public function baixarDanfe()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para baixar DANFE.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);
        $itens = $this->Nfecom_model->getItens($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // Carregar a classe NFComPreview
        require_once APPPATH . 'libraries/NFComPreview.php';

        // Buscar dados da empresa emitente
        $emit = $this->Nfe_model->getEmit();

        if (!$emit) {
            $this->session->set_flashdata('error', 'Nenhuma empresa emitente configurada.');
            redirect(site_url('nfecom'));
        }

        // Configuração para a classe NFComPreview
        $config = [
            'empresa' => [
                'razao_social' => $emit['xNome'],
                'cnpj' => $emit['CNPJ'],
                'ie' => $emit['IE'],
                'endereco' => $emit['enderEmit']['xLgr'] . ', ' . $emit['enderEmit']['nro'] .
                             (!empty($emit['enderEmit']['xCpl']) ? ' - ' . $emit['enderEmit']['xCpl'] : '') .
                             ' - ' . $emit['enderEmit']['xBairro'] . ' - ' . $emit['enderEmit']['xMun'] .
                             '/' . $emit['enderEmit']['UF'] . ' - CEP: ' . $emit['enderEmit']['CEP'],
                'telefone' => $emit['enderEmit']['fone'] ?? '',
                'email' => '',
                'logo' => FCPATH . 'assets/uploads/logomarca.png'
            ],
            'nfcom' => [
                'numero' => $nfecom->NFC_NNF,
                'serie' => $nfecom->NFC_SERIE,
                'data_emissao' => date('d/m/Y H:i:s', strtotime($nfecom->NFC_DHEMI)),
                'chave_acesso' => $nfecom->NFC_CH_NFCOM,
                'protocolo' => $nfecom->NFC_N_PROT,
                'data_autorizacao' => $nfecom->NFC_DH_RECBTO ? date('d/m/Y H:i:s', strtotime($nfecom->NFC_DH_RECBTO)) : ''
            ]
        ];

        // Preparar dados dos itens
        $produtos = [];
        foreach ($itens as $item) {
            $produtos[] = [
                'codigo' => $item->NFI_C_PROD,
                'descricao' => $item->NFI_X_PROD,
                'ncm' => '',
                'cfop' => $item->NFI_CFOP,
                'unidade' => $item->NFI_U_MED,
                'quantidade' => $item->NFI_Q_FATURADA,
                'valor_unitario' => $item->NFI_V_ITEM,
                'valor_total' => $item->NFI_V_PROD,
                'icms' => [
                    'cst' => $item->NFI_CST_ICMS,
                    'aliquota' => 0,
                    'valor' => 0
                ],
                'pis' => [
                    'cst' => $item->NFI_CST_PIS,
                    'aliquota' => $item->NFI_P_PIS,
                    'valor' => $item->NFI_V_PIS
                ],
                'cofins' => [
                    'cst' => $item->NFI_CST_COFINS,
                    'aliquota' => $item->NFI_P_COFINS,
                    'valor' => $item->NFI_V_COFINS
                ]
            ];
        }

        // Preparar totais
        $totais = [
            'valor_total' => $nfecom->NFC_V_NF,
            'valor_base_calculo' => $nfecom->NFC_V_PROD,
            'valor_produtos' => $nfecom->NFC_V_PROD,
            'valor_icms' => 0,
            'valor_isento' => 0,
            'valor_outros' => $nfecom->NFC_V_OUTRO,
            'valor_pis' => $nfecom->NFC_V_PIS,
            'valor_cofins' => $nfecom->NFC_V_COFINS,
            'valor_fust' => $nfecom->NFC_V_FUST,
            'valor_funtel' => $nfecom->NFC_V_FUNTEL
        ];

        // Preparar dados completos
        $dados = [
            'numero' => $nfecom->NFC_NNF,
            'chave' => $nfecom->NFC_CH_NFCOM,
            'destinatario' => [
                'nome' => $nfecom->NFC_X_NOME_DEST,
                'cnpj' => $nfecom->NFC_CNPJ_DEST,
                'endereco' => $nfecom->NFC_X_LOGRADOURO_DEST . ', ' . $nfecom->NFC_N_DEST .
                             (!empty($nfecom->NFC_X_COMPLEMENTO_DEST) ? ' - ' . $nfecom->NFC_X_COMPLEMENTO_DEST : '') .
                             ' - ' . $nfecom->NFC_X_BAIRRO_DEST . ' - ' . $nfecom->NFC_X_MUNICIPIO_DEST .
                             '/' . $nfecom->NFC_UF_DEST . ' - CEP: ' . $nfecom->NFC_CEP_DEST
            ],
            'assinante' => [
                'codigo' => $nfecom->NFC_CNPJ_DEST,
                'tipo' => 3, // Pessoa Jurídica
                'servico' => 6, // Telecomunicações
                'contrato' => $nfecom->NFC_N_CONTRATO ?? ''
            ],
            'faturamento' => [
                'competencia' => date('m/Y', strtotime($nfecom->NFC_COMPET_FAT)),
                'vencimento' => date('d/m/Y', strtotime($nfecom->NFC_D_VENC_FAT)),
                'periodo_inicio' => date('d/m/Y', strtotime($nfecom->NFC_D_PER_USO_INI)),
                'periodo_fim' => date('d/m/Y', strtotime($nfecom->NFC_D_PER_USO_FIM)),
                'cod_barras' => $nfecom->NFC_COD_BARRAS ?? '1'
            ],
            'itens' => $produtos,
            'totais' => $totais,
            'informacoes_adicionais' => $nfecom->NFC_INF_CPL ?? ''
        ];

        try {
            // Gerar PDF
            $nfcomPreview = new \App\NFComPreview($config);
            $resultado = $nfcomPreview->gerarPdf($dados);

            // Verificar se o PDF foi gerado
            if (empty($resultado['pdf'])) {
                throw new Exception('PDF vazio gerado');
            }

            // Limpar qualquer output anterior
            if (ob_get_length()) {
                ob_clean();
            }

            // Download do PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="danfe_nfcom_' . $nfecom->NFC_NNF . '.pdf"');
            header('Content-Length: ' . strlen($resultado['pdf']));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $resultado['pdf'];
            exit;

        } catch (Exception $e) {
            // Log do erro
            log_message('error', 'Erro ao baixar DANFE NFCom: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());

            $this->session->set_flashdata('error', 'Erro ao baixar DANFE: ' . $e->getMessage());
            redirect(site_url('nfecom'));
        }
    }

    public function reemitir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para reemitir NFECom.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // Verificar se a NFCom está rejeitada
        if ($nfecom->NFC_STATUS != 4) {
            $this->session->set_flashdata('error', 'Apenas NFCom rejeitadas podem ser reemitidas.');
            redirect(site_url('nfecom/visualizar/' . $id));
        }

        try {
            // Resetar apenas o status, mantendo a mesma chave de acesso
            $dadosAtualizacao = [
                'NFC_STATUS' => 1, // Voltar para status "Salvo"
                'NFC_XML' => null, // Limpar XML antigo
                'NFC_C_STAT' => null,
                'NFC_X_MOTIVO' => null,
                'NFC_N_PROT' => null,
                'NFC_DH_RECBTO' => null
            ];

            $this->Nfecom_model->edit('nfecom_capa', $dadosAtualizacao, 'NFC_ID', $id);

            $this->session->set_flashdata('success', 'NFCom preparada para reemissão com a mesma chave: ' . $nfecom->NFC_CH_NFCOM);

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Erro ao preparar reemissão: ' . $e->getMessage());
            log_message('error', 'Erro na reemissão NFCom: ' . $e->getMessage());
        }

        redirect(site_url('nfecom/visualizar/' . $id));
    }

    public function autorizar($id = null, $redirect = true)
    {
        // Verificar se é uma requisição AJAX
        $isAjax = $this->input->is_ajax_request() || $this->input->post('ajax') === 'true';
        
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'Você não tem permissão para autorizar NFECom.'];
                echo json_encode($response);
                return false;
            }
            $this->session->set_flashdata('error', 'Você não tem permissão para autorizar NFECom.');
            if ($redirect) redirect(base_url());
            return false;
        }

        $id = $id ?? $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'NFECom não encontrada.'];
                echo json_encode($response);
                return false;
            }
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // 0. Atualizar dados fiscais e gerar XML se rascunho ou rejeitado
        if ($nfecom->NFC_STATUS < 2 || $nfecom->NFC_STATUS == 4) {
            $configFiscal = $this->getConfiguracaoNfcom();
            $emitente = $this->Nfe_model->getEmit(); // Buscar dados atualizados do emitente

            if ($configFiscal && $emitente) {
                // Calcular cUF a partir da UF do emitente, não do destinatário
                $ufEmit = $emitente['enderEmit']['UF'] ?? 'GO';
                $codigoUf = $this->get_cUF($ufEmit);

                // Para NFCom rejeitadas (reemissão), manter o número, chave, série e data originais
                // Para NFCom novas, apenas pegar número atual (incremento será feito no final do processo)
                $isReemissao = ($nfecom->NFC_STATUS == 4);
                
                if ($nfecom->NFC_STATUS < 2) {
                    $numeroNota = $configFiscal->CFG_NUMERO_ATUAL;
                } else {
                    // NFCom rejeitada mantém o número atual
                    $numeroNota = $nfecom->NFC_NNF;
                }

                // Buscar dados atualizados do cliente/destinatário
                $this->db->select('c.*, p.*, e.END_LOGRADOURO, e.END_NUMERO, e.END_COMPLEMENTO, e.END_CEP, b.BAI_NOME, m.MUN_NOME, m.MUN_IBGE, es.EST_UF, d.DOC_NUMERO as PES_IE, d.DOC_NATUREZA_CONTRIBUINTE');
                $this->db->from('clientes c');
                $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID');
                $this->db->join('enderecos e', 'e.PES_ID = p.PES_ID AND e.END_PADRAO = 1', 'left');
                $this->db->join('bairros b', 'b.BAI_ID = e.BAI_ID', 'left');
                $this->db->join('municipios m', 'm.MUN_ID = e.MUN_ID', 'left');
                $this->db->join('estados es', 'es.EST_ID = e.EST_ID', 'left');
                $this->db->join('documentos d', "d.PES_ID = p.PES_ID AND d.DOC_TIPO_DOCUMENTO = 'Inscrição Estadual'", 'left');
                if (!empty($nfecom->CLN_ID)) {
                    $this->db->where('c.CLN_ID', $nfecom->CLN_ID);
                } else {
                    $cnpjLimpo = preg_replace('/\D/', '', $nfecom->NFC_CNPJ_DEST);
                    $this->db->where('p.PES_CPFCNPJ', $cnpjLimpo);
                }
                $cliente = $this->db->get()->row();

                $atualizacao = [
                    'NFC_TIPO_AMBIENTE' => $configFiscal->CFG_AMBIENTE,
                    // Sincronizar dados do emitente do cadastro de empresas
                    'NFC_CNPJ_EMIT' => $emitente['CNPJ'],
                    'NFC_IE_EMIT' => $emitente['IE'],
                    'NFC_CRT_EMIT' => $emitente['CRT'],
                    'NFC_X_NOME_EMIT' => $emitente['xNome'],
                    'NFC_X_FANT_EMIT' => $emitente['xFant'],
                    'NFC_X_LGR_EMIT' => $emitente['enderEmit']['xLgr'],
                    'NFC_NRO_EMIT' => $emitente['enderEmit']['nro'],
                    'NFC_X_CPL_EMIT' => $emitente['enderEmit']['xCpl'],
                    'NFC_X_BAIRRO_EMIT' => $emitente['enderEmit']['xBairro'],
                    'NFC_C_MUN_EMIT' => $emitente['enderEmit']['cMun'],
                    'NFC_X_MUN_EMIT' => $emitente['enderEmit']['xMun'],
                    'NFC_CEP_EMIT' => $emitente['enderEmit']['CEP'],
                    'NFC_UF_EMIT' => $emitente['enderEmit']['UF'],
                    'NFC_FONE_EMIT' => $emitente['enderEmit']['fone'],
                ];
                
                // Para reemissão, manter série, número, chave e data originais
                if (!$isReemissao) {
                    $atualizacao['NFC_SERIE'] = $configFiscal->CFG_SERIE;
                    $atualizacao['NFC_NNF'] = $numeroNota;
                    $atualizacao['NFC_CUF'] = $codigoUf;
                }
                // Se for reemissão, não atualizar série, número, CUF (mantém originais)

                // Sincronizar dados do destinatário do cadastro de clientes
                if ($cliente) {
                    $atualizacao['NFC_X_NOME_DEST'] = $cliente->PES_NOME;
                    $atualizacao['NFC_CNPJ_DEST'] = preg_replace('/\D/', '', $cliente->PES_CPFCNPJ);
                    $atualizacao['NFC_IND_IE_DEST'] = ($cliente->DOC_NATUREZA_CONTRIBUINTE == 'Contribuinte') ? '1' : '9';
                    $atualizacao['NFC_X_LGR_DEST'] = $cliente->END_LOGRADOURO;
                    $atualizacao['NFC_NRO_DEST'] = $cliente->END_NUMERO;
                    $atualizacao['NFC_X_CPL_DEST'] = $cliente->END_COMPLEMENTO;
                    $atualizacao['NFC_X_BAIRRO_DEST'] = $cliente->BAI_NOME;
                    $atualizacao['NFC_C_MUN_DEST'] = $cliente->MUN_IBGE;
                    $atualizacao['NFC_X_MUN_DEST'] = $cliente->MUN_NOME;
                    $atualizacao['NFC_CEP_DEST'] = preg_replace('/\D/', '', $cliente->END_CEP);
                    $atualizacao['NFC_UF_DEST'] = $cliente->EST_UF;
                }

                // Para reemissão (NFCom rejeitada), limpar o motivo antigo e manter chave original
                if ($isReemissao) {
                    $atualizacao['NFC_X_MOTIVO'] = null;
                    $atualizacao['NFC_C_STAT'] = null;
                    $atualizacao['NFC_N_PROT'] = null;
                    $atualizacao['NFC_DH_RECBTO'] = null;
                    $atualizacao['NFC_XML'] = null;
                    // Não recalcular chave, manter a original
                } else {
                    // Para NFCom nova, calcular nova chave
                    $chaveData = [
                        'NFC_CUF' => $atualizacao['NFC_CUF'],
                        'NFC_DHEMI' => $nfecom->NFC_DHEMI ?? date('Y-m-d H:i:s'),
                        'NFC_CNPJ_EMIT' => $atualizacao['NFC_CNPJ_EMIT'],
                        'NFC_MOD' => $nfecom->NFC_MOD,
                        'NFC_SERIE' => $atualizacao['NFC_SERIE'],
                        'NFC_NNF' => $atualizacao['NFC_NNF'],
                        'NFC_TP_EMIS' => $nfecom->NFC_TP_EMIS,
                        'NFC_CNF' => $nfecom->NFC_CNF,
                    ];

                    // nSiteAutoriz é necessário para a chave e DV
                    $nSiteAutoriz = 0; // Default
                    $chaveData['NFC_N_SITE_AUTORIZ'] = $nSiteAutoriz;

                    $atualizacao['NFC_CDV'] = $this->calculateDV($chaveData);
                    $chaveData['NFC_CDV'] = $atualizacao['NFC_CDV'];
                    $atualizacao['NFC_CH_NFCOM'] = $this->generateChave($chaveData);
                }

                $this->Nfecom_model->edit('nfecom_capa', $atualizacao, 'NFC_ID', $id);
                $nfecom = $this->Nfecom_model->getById($id); // Recarregar a NFCom com os dados atualizados
            }
        }

        // Verificar se é reemissão
        $isReemissao = ($nfecom->NFC_STATUS == 4);
        
        // Validar certificado configurado para NFCOM
        $configFiscal = $this->getConfiguracaoNfcom();
        if (!$configFiscal || empty($configFiscal->CER_ARQUIVO) || empty($configFiscal->CER_SENHA)) {
            if ($isAjax) {
                $response = ['success' => false, 'message' => 'Nenhum certificado válido configurado para NFCOM.'];
                echo json_encode($response);
                return false;
            }
            $this->session->set_flashdata('error', 'Nenhum certificado válido configurado para NFCOM.');
            redirect(site_url('nfecom/visualizar/' . $id));
        }

        // Integração Real com SEFAZ
        try {
            $this->load->library('NFComMake');
            $this->load->library('NFComService');

            // 1. Preparar dados completos para o XML
            try {
                $dados = $this->prepararDadosEnvio($id);
            } catch (Exception $e) {
                log_message('error', 'Erro ao preparar dados de envio NFCom: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                throw new Exception('Erro ao preparar dados da NFCom: ' . $e->getMessage());
            }

            // 2. Gerar XML
            try {
                $nfcomMake = new NFComMake();
                $xml = $nfcomMake->build($dados);
            } catch (Exception $e) {
                log_message('error', 'Erro ao gerar XML NFCom: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                throw new Exception('Erro ao gerar XML da NFCom: ' . $e->getMessage());
            }

            // Debug do XML gerado
            file_put_contents('debug_nfcom_generated.xml', $xml);

            // 3. Configurar Serviço (Certificado e Ambiente)
            try {
                $nfcomService = new NFComService([
                    'ambiente' => $dados['ide']['tpAmb'],
                    'disable_cert_validation' => true
                ]);
                $nfcomService->setCertificate($configFiscal->CER_ARQUIVO, $configFiscal->CER_SENHA);
            } catch (Exception $e) {
                log_message('error', 'Erro ao configurar serviço NFCom: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                throw new Exception('Erro ao configurar serviço NFCom: ' . $e->getMessage());
            }

            // 4. Assinar XML
            try {
                $xmlSigned = $nfcomService->sign($xml);
            } catch (Exception $e) {
                log_message('error', 'Erro ao assinar XML NFCom: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                throw new Exception('Erro ao assinar XML da NFCom: ' . $e->getMessage());
            }

            // 5. Enviar para SEFAZ
            try {
                $retorno = $nfcomService->send($xmlSigned);
            } catch (Exception $e) {
                log_message('error', 'Erro ao enviar NFCom para SEFAZ: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                throw new Exception('Erro ao enviar NFCom para SEFAZ: ' . $e->getMessage());
            }

            // 6. Processar Retorno
            if (isset($retorno['error'])) {
                throw new Exception($retorno['error']);
            }

            // Determinar status baseado no código de retorno da SEFAZ
            $cStat = $retorno['cStat'] ?? '999';

            if ($cStat == '100') {
                // Autorizado
                $protocolo = $retorno['protocolo']['nProt'];
                $dhRecbto = $retorno['protocolo']['dhRecbto'];
                $motivo = $retorno['xMotivo'];
                $chaveAcesso = $retorno['protocolo']['chNFCom'];

                $dadosAtu = [
                    'NFC_STATUS' => 3, // Autorizado
                    'NFC_TIPO_AMBIENTE' => $dados['ide']['tpAmb'],
                    'NFC_CH_NFCOM' => $chaveAcesso,
                    'NFC_C_STAT' => $cStat,
                    'NFC_X_MOTIVO' => $motivo,
                    'NFC_N_PROT' => $protocolo,
                    'NFC_DH_RECBTO' => $dhRecbto,
                    'NFC_XML' => $xmlSigned // Salva o assinado
                ];

                $this->Nfecom_model->updateStatus($id, $dadosAtu);
                $this->registrarProtocolo($id, $protocolo, 'AUTORIZACAO', $motivo, $dhRecbto);

                log_info('NFCom Autorizada Real (ID: ' . $id . ', Chave: ' . $chaveAcesso . ')');
                log_message('info', 'NFCom ' . ($isReemissao ? 'Reemissão' : 'Emissão') . ' concluída com sucesso - ID: ' . $id);
                $this->session->set_flashdata('success', 'NFCom ' . ($isReemissao ? 'reemitida' : 'autorizada') . ' com sucesso no SEFAZ! Chave: ' . $chaveAcesso);
            } elseif (in_array($cStat, ['110', '205', '301', '302', '303'])) {
                // Erro de validação/rejeição
                $motivo = $retorno['xMotivo'] ?? 'Erro de validação';
                $dadosAtu = [
                    'NFC_STATUS' => 4, // Rejeitada
                    'NFC_C_STAT' => $cStat,
                    'NFC_X_MOTIVO' => $motivo
                ];
                $this->Nfecom_model->updateStatus($id, $dadosAtu);

                // Registrar protocolo com o motivo da rejeição
                $this->registrarProtocolo($id, 'REJ-' . $cStat, 'REJEICAO', $motivo);

                log_message('error', 'NFCom com Erro de Validação (ID: ' . $id . '): ' . $cStat . ' - ' . $motivo);
                $this->session->set_flashdata('error', 'NFCom com Erro de Validação: ' . $cStat . ' - ' . $motivo);
            } else {
                // Outros tipos de rejeição
                $motivo = $retorno['xMotivo'] ?? 'Erro desconhecido';
                $dadosAtu = [
                    'NFC_STATUS' => 4, // Rejeitada
                    'NFC_C_STAT' => $cStat,
                    'NFC_X_MOTIVO' => $motivo
                ];
                $this->Nfecom_model->updateStatus($id, $dadosAtu);

                // Registrar protocolo com o motivo da rejeição
                $this->registrarProtocolo($id, 'REJ-' . $cStat, 'REJEICAO', $motivo);

                log_message('error', 'NFCom Rejeitada (ID: ' . $id . '): ' . $cStat . ' - ' . $motivo);
                $this->session->set_flashdata('error', 'NFCom Rejeitada pelo SEFAZ: ' . $cStat . ' - ' . $motivo);
            }

        } catch (Exception $e) {
            log_message('error', 'Erro na autorização NFCom: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());

            // Mensagem mais detalhada para o usuário
            $mensagemErro = 'Erro ao autorizar: ' . $e->getMessage();
            if (strpos($e->getMessage(), 'Resposta vazia') !== false) {
                $mensagemErro .= '. Verifique: 1) Certificado digital válido e configurado, 2) Conexão com internet, 3) Serviço SEFAZ disponível.';
            }
            
            // Se for requisição AJAX, retornar JSON com erro
            if ($isAjax) {
                $response = ['success' => false, 'message' => $mensagemErro];
                echo json_encode($response);
                return;
            }

            $this->session->set_flashdata('error', $mensagemErro);
            if ($redirect) {
                redirect(site_url('nfecom/visualizar/' . $id));
            }
            return false;
        }

        // Incrementar sequência apenas uma vez no final do processo para NFCom novas
        if ($nfecom->NFC_STATUS < 2) {
            $this->incrementarSequenciaNfcom();
        }

        if ($redirect) {
            redirect(site_url('nfecom/visualizar/' . $id));
        }
        return true;
    }

    public function cancelar()
    {
        // Verificar se é uma requisição AJAX
        $isAjax = $this->input->is_ajax_request() || $this->input->post('ajax') === 'true';
        
        // Configurar headers para JSON
        if ($isAjax) {
            $this->output->set_content_type('application/json');
        }

        try {
            // Verificar permissão
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
                throw new Exception('Você não tem permissão para cancelar NFCom.');
            }

            // Verificar método POST
            if ($this->input->method() !== 'post') {
                throw new Exception('Método de requisição inválido. Use POST.');
            }

            // Obter dados do POST
            $nfecom_id = $this->input->post('nfecom_id');
            $justificativa = $this->input->post('justificativa');
            
            // Limpar e validar justificativa
            if (is_string($justificativa)) {
                $justificativa = trim($justificativa);
            } else {
                $justificativa = '';
            }

            // Validações básicas
            if (empty($nfecom_id)) {
                throw new Exception('ID da NFCom não informado.');
            }

            if (empty($justificativa)) {
                throw new Exception('Justificativa não informada.');
            }

            // Validar comprimento mínimo (15 caracteres após trim)
            $justificativaLimpa = trim($justificativa);
            if (mb_strlen($justificativaLimpa, 'UTF-8') < 15) {
                throw new Exception('A justificativa deve ter no mínimo 15 caracteres.');
            }
            
            // Usar justificativa limpa
            $justificativa = $justificativaLimpa;

            // Buscar NFCom
            $nfecom = $this->Nfecom_model->getById($nfecom_id);
            if (!$nfecom) {
                throw new Exception('NFCom não encontrada.');
            }

            // Verificar se está autorizada (status 3 ou 5)
            if ($nfecom->NFC_STATUS != 3 && $nfecom->NFC_STATUS != 5) {
                throw new Exception('Apenas NFCom autorizadas podem ser canceladas. Status atual: ' . $nfecom->NFC_STATUS);
            }

            // Verificar se já está cancelada
            if ($nfecom->NFC_STATUS == 7) {
                throw new Exception('Esta NFCom já está cancelada.');
            }

            // Verificar se tem chave de acesso
            if (empty($nfecom->NFC_CH_NFCOM)) {
                throw new Exception('Chave de acesso da NFCom não encontrada.');
            }

            // Verificar se tem protocolo
            if (empty($nfecom->NFC_N_PROT)) {
                throw new Exception('Protocolo da NFCom não encontrado. É necessário ter um protocolo de autorização para cancelar.');
            }

            // Validar certificado configurado
            $configFiscal = $this->getConfiguracaoNfcom();
            if (!$configFiscal || empty($configFiscal->CER_ARQUIVO) || empty($configFiscal->CER_SENHA)) {
                throw new Exception('Nenhum certificado válido configurado para NFCOM.');
            }

            // Carregar bibliotecas
            $this->load->library('NFComService');

            // Configurar serviço
            $nfcomService = new NFComService([
                'ambiente' => $nfecom->NFC_TIPO_AMBIENTE ?? 2,
                'disable_cert_validation' => true
            ]);
            $nfcomService->setCertificate($configFiscal->CER_ARQUIVO, $configFiscal->CER_SENHA);

            // Gerar evento de cancelamento
            $eventoCancelamento = $this->gerarEventoCancelamento($nfecom, $justificativa);
            
            // Assinar evento usando método específico para eventos (infEvento)
            $eventoAssinado = $this->assinarEventoNFCom($configFiscal, $eventoCancelamento);
            
            // Enviar evento para SEFAZ
            $retornoCancelamento = $nfcomService->sendEvent($eventoAssinado, $nfecom->NFC_TIPO_AMBIENTE ?? 2);
            
            // Processar retorno
            if (isset($retornoCancelamento['error'])) {
                throw new Exception('Erro ao cancelar na SEFAZ: ' . $retornoCancelamento['error']);
            }
            
            // Verificar status do cancelamento (cStat = 135 significa cancelamento autorizado)
            $cStat = $retornoCancelamento['cStat'] ?? '999';
            
            if ($cStat == '135') {
                // Cancelamento autorizado pela SEFAZ
                $protocoloCancelamento = $retornoCancelamento['nProt'] ?? '';
                $motivo = $retornoCancelamento['xMotivo'] ?? 'Cancelamento autorizado';
                
                // Atualizar status da NFCom
                $dadosAtualizacao = [
                    'NFC_STATUS' => 7, // Cancelada
                    'NFC_X_MOTIVO' => 'Cancelada: ' . $justificativa,
                    'NFC_C_STAT' => $cStat,
                    'NFC_N_PROT_CANC' => $protocoloCancelamento
                ];

                $this->Nfecom_model->edit('nfecom_capa', $dadosAtualizacao, 'NFC_ID', $nfecom_id);

                // Registrar protocolo de cancelamento
                $this->registrarProtocolo($nfecom_id, $protocoloCancelamento, 'CANCELAMENTO', $motivo);

                log_message('info', 'NFCom cancelada com sucesso na SEFAZ - ID: ' . $nfecom_id . ', Chave: ' . $nfecom->NFC_CH_NFCOM . ', Protocolo: ' . $protocoloCancelamento);
            } else {
                // Cancelamento rejeitado pela SEFAZ
                $motivo = $retornoCancelamento['xMotivo'] ?? 'Erro desconhecido';
                throw new Exception('Cancelamento rejeitado pela SEFAZ: ' . $cStat . ' - ' . $motivo);
            }

            if ($isAjax) {
                $response = [
                    'success' => true,
                    'message' => 'NFCom cancelada com sucesso!'
                ];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('success', 'NFCom cancelada com sucesso!');
                redirect(site_url('nfecom/visualizar/' . $nfecom_id));
            }

        } catch (Exception $e) {
            log_message('error', 'Erro ao cancelar NFCom: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());

            if ($isAjax) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
                echo json_encode($response);
                return;
            } else {
                $this->session->set_flashdata('error', 'Erro ao cancelar NFCom: ' . $e->getMessage());
                redirect(site_url('nfecom'));
            }
        }
    }

    private function gerarEventoCancelamento($nfecom, $justificativa)
    {
        // Gerar XML do evento de cancelamento conforme schema evCancNFCom_v1.00.xsd
        // O Id deve seguir o padrão: "ID" + tpEvento (6 dígitos) + chave NFCom (44 dígitos) + nSeqEvento (3 dígitos)
        $tpEvento = '110111'; // Código do evento de cancelamento
        $chaveNFCom = preg_replace('/\D/', '', $nfecom->NFC_CH_NFCOM); // Remover caracteres não numéricos
        $nSeqEvento = '001'; // Primeiro evento de cancelamento
        $idEvento = 'ID' . $tpEvento . $chaveNFCom . $nSeqEvento;
        
        // Data/hora do evento em formato TDateTimeUTC (horário de Brasília - UTC-3)
        // Formato esperado: YYYY-MM-DDTHH:MM:SS-03:00 (não pode usar Z, deve ser -03:00 ou +03:00)
        // O padrão do schema aceita: -00:00 até -11:00 ou +12:00
        $timezone = new DateTimeZone('America/Sao_Paulo'); // Horário de Brasília (PTBR)
        $datetime = new DateTime('now', $timezone);
        // Formato P gera -03:00 ou -02:00 conforme horário de verão do Brasil
        $dhEvento = $datetime->format('Y-m-d\TH:i:sP');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<eventoNFCom xmlns="http://www.portalfiscal.inf.br/nfcom" versao="1.00">' . "\n";
        $xml .= '  <infEvento Id="' . $idEvento . '">' . "\n";
        $xml .= '    <cOrgao>52</cOrgao>' . "\n"; // Código do órgão (GO = 52)
        $xml .= '    <tpAmb>' . ($nfecom->NFC_TIPO_AMBIENTE ?? 2) . '</tpAmb>' . "\n";
        $xml .= '    <CNPJ>' . preg_replace('/\D/', '', $nfecom->NFC_CNPJ_EMIT) . '</CNPJ>' . "\n";
        $xml .= '    <chNFCom>' . $nfecom->NFC_CH_NFCOM . '</chNFCom>' . "\n";
        $xml .= '    <dhEvento>' . $dhEvento . '</dhEvento>' . "\n";
        $xml .= '    <tpEvento>' . $tpEvento . '</tpEvento>' . "\n";
        $xml .= '    <nSeqEvento>' . $nSeqEvento . '</nSeqEvento>' . "\n";
        // Garantir que o protocolo tenha exatamente 16 dígitos (padrão TProt)
        $nProt = preg_replace('/\D/', '', $nfecom->NFC_N_PROT); // Remover caracteres não numéricos
        $nProt = str_pad($nProt, 16, '0', STR_PAD_LEFT); // Preencher com zeros à esquerda até 16 dígitos
        
        $xml .= '    <detEvento versaoEvento="1.00">' . "\n";
        $xml .= '      <evCancNFCom xmlns="http://www.portalfiscal.inf.br/nfcom">' . "\n";
        $xml .= '        <descEvento>Cancelamento</descEvento>' . "\n";
        $xml .= '        <nProt>' . $nProt . '</nProt>' . "\n";
        $xml .= '        <xJust>' . htmlspecialchars($justificativa, ENT_XML1, 'UTF-8') . '</xJust>' . "\n";
        $xml .= '      </evCancNFCom>' . "\n";
        $xml .= '    </detEvento>' . "\n";
        $xml .= '  </infEvento>' . "\n";
        $xml .= '</eventoNFCom>';

        return $xml;
    }

    private function assinarEventoNFCom($configFiscal, $xmlEvento)
    {
        // Assinar evento usando Signer diretamente com a tag correta (infEvento)
        // Usar namespace completo já que use não pode estar dentro de função
        $certificate = \NFePHP\Common\Certificate::readPfx($configFiscal->CER_ARQUIVO, $configFiscal->CER_SENHA);
        
        // Assinar usando a tag infEvento em vez de infNFCom
        return \NFePHP\Common\Signer::sign(
            $certificate,
            $xmlEvento,
            'infEvento',  // Tag correta para eventos
            'Id',
            OPENSSL_ALGO_SHA1,
            [true, false, null, null]
        );
    }

    private function registrarProtocolo($nfecomId, $numeroProtocolo, $tipo, $motivo = null, $data = null)
    {
        if (empty($nfecomId) || empty($numeroProtocolo) || empty($tipo)) {
            return;
        }

        $this->Nfecom_model->add('protocolos', [
            'NFC_ID' => $nfecomId,
            'PRT_NUMERO_PROTOCOLO' => $numeroProtocolo,
            'PRT_TIPO' => $tipo,
            'PRT_MOTIVO' => $motivo,
            'PRT_DATA' => $data ?: date('Y-m-d H:i:s'),
        ]);
    }

    private function getConfiguracaoNfcom()
    {
        $empresa = $this->db->limit(1)->get('empresas')->row();
        if (!$empresa) {
            return null;
        }

        return $this->ConfiguracoesFiscais_model->getByTipo($empresa->EMP_ID, 'NFCOM');
    }

    private function incrementarSequenciaNfcom()
    {
        $empresa = $this->db->limit(1)->get('empresas')->row();
        if (!$empresa) {
            return;
        }

        $this->ConfiguracoesFiscais_model->incrementarNumero($empresa->EMP_ID, 'NFCOM');
    }

    private function getCodigoUf($uf)
    {
        if (empty($uf)) {
            return null;
        }

        $this->db->select('EST_CODIGO_UF');
        $this->db->from('estados');
        $this->db->where('EST_UF', $uf);
        $row = $this->db->get()->row();

        return $row ? $row->EST_CODIGO_UF : null;
    }


    private function buildInfoComplementar($data, $valorBruto, $comissaoAgencia, $valorLiquido, $mensagensFiscais = [])
    {
        // NFC_INF_CPL deve conter: Observação digitada pelo usuário + Mensagens fiscais das classificações fiscais
        // Separadas por ponto e vírgula
        $info = '';

        // Adicionar observação digitada pelo usuário (se houver)
        $observacaoDigitada = isset($data['observacoes']) ? trim($data['observacoes']) : '';
        if (!empty($observacaoDigitada)) {
            $info = $observacaoDigitada;
        }

        // Adicionar mensagens fiscais das classificações fiscais
        if (!empty($mensagensFiscais) && is_array($mensagensFiscais)) {
            $mensagensUnicas = [];
            foreach ($mensagensFiscais as $mensagem) {
                $mensagemLimpa = trim($mensagem);
                if (!empty($mensagemLimpa)) {
                    // Evitar duplicatas
                    if (!in_array($mensagemLimpa, $mensagensUnicas)) {
                        $mensagensUnicas[] = $mensagemLimpa;
                    }
                }
            }
            
            // Juntar todas as mensagens únicas separadas por ponto e vírgula
            if (!empty($mensagensUnicas)) {
                $mensagensTexto = implode('; ', $mensagensUnicas);
                // Se já tem observação, adicionar as mensagens fiscais após com ponto e vírgula
                if (!empty($info)) {
                    $info .= '; ' . $mensagensTexto;
                } else {
                    $info = $mensagensTexto;
                }
            }
        }

        return $info;
    }

    private function calculateDV($data)
    {
        // Cálculo do dígito verificador usando módulo 11 (padrão SEFAZ)
        $cnpj = preg_replace('/\D/', '', $data['NFC_CNPJ_EMIT']);
        $chave = $data['NFC_CUF'] . date('ym', strtotime($data['NFC_DHEMI'])) .
            $cnpj . $data['NFC_MOD'] .
            str_pad($data['NFC_SERIE'], 3, '0', STR_PAD_LEFT) .
            str_pad($data['NFC_NNF'], 9, '0', STR_PAD_LEFT) .
            $data['NFC_TP_EMIS'] .
            $data['NFC_N_SITE_AUTORIZ'] .
            str_pad($data['NFC_CNF'], 7, '0', STR_PAD_LEFT);

        // Algoritmo módulo 11
        $multiplicador = 2;
        $soma = 0;

        // Percorre a chave de trás para frente
        for ($i = strlen($chave) - 1; $i >= 0; $i--) {
            $soma += intval($chave[$i]) * $multiplicador;
            $multiplicador = ($multiplicador == 9) ? 2 : $multiplicador + 1;
        }

        $resto = $soma % 11;

        if ($resto == 0 || $resto == 1) {
            return 0;
        }

        return 11 - $resto;
    }

    private function generateChave($data)
    {
        $cnpj = preg_replace('/\D/', '', $data['NFC_CNPJ_EMIT']);

        // Gerar chave da NFCom (44 dígitos)
        // cUF(2)+AAMM(4)+CNPJ(14)+mod(2)+serie(3)+nNF(9)+tpEmis(1)+nSiteAutoriz(1)+cNF(7)+cDV(1)
        $chave = $data['NFC_CUF'] .
            date('ym', strtotime($data['NFC_DHEMI'])) .
            $cnpj .
            $data['NFC_MOD'] .
            str_pad($data['NFC_SERIE'], 3, '0', STR_PAD_LEFT) .
            str_pad($data['NFC_NNF'], 9, '0', STR_PAD_LEFT) .
            $data['NFC_TP_EMIS'] .
            $data['NFC_N_SITE_AUTORIZ'] .
            str_pad($data['NFC_CNF'], 7, '0', STR_PAD_LEFT) .
            $data['NFC_CDV'];

        return $chave;
    }

    private function generateNfecomXml($nfecom, $itens)
    {
        // Implementação básica da geração do XML
        // Em produção, deve seguir o schema oficial da NFCom
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<nfcomProc xmlns="http://www.portalfiscal.inf.br/nfcom" versao="1.00">' . "\n";
        $xml .= '<NFCom xmlns="http://www.portalfiscal.inf.br/nfcom">' . "\n";
        $xml .= '<infNFCom Id="NFCom' . $nfecom->NFC_CH_NFCOM . '" versao="1.00">' . "\n";

        // Identificação
        $xml .= '<ide>' . "\n";
        $xml .= '<cUF>' . $nfecom->NFC_CUF . '</cUF>' . "\n";
        $xml .= '<tpAmb>' . $nfecom->NFC_TIPO_AMBIENTE . '</tpAmb>' . "\n";
        $xml .= '<mod>' . $nfecom->NFC_MOD . '</mod>' . "\n";
        $xml .= '<serie>' . $nfecom->NFC_SERIE . '</serie>' . "\n";
        $xml .= '<nNF>' . $nfecom->NFC_NNF . '</nNF>' . "\n";
        $xml .= '<cNF>' . $nfecom->NFC_CNF . '</cNF>' . "\n";
        $xml .= '<cDV>' . $nfecom->NFC_CDV . '</cDV>' . "\n";
        $xml .= '<dhEmi>' . date('Y-m-d\TH:i:sP', strtotime($nfecom->NFC_DHEMI)) . '</dhEmi>' . "\n";
        $xml .= '<tpEmis>' . $nfecom->NFC_TP_EMIS . '</tpEmis>' . "\n";
        $xml .= '<nSiteAutoriz>' . $nfecom->NFC_N_SITE_AUTORIZ . '</nSiteAutoriz>' . "\n";
        $xml .= '<cMunFG>' . $nfecom->NFC_C_MUN_FG . '</cMunFG>' . "\n";
        $xml .= '<finNFCom>' . $nfecom->NFC_FIN_NFCOM . '</finNFCom>' . "\n";
        $xml .= '<tpFat>' . $nfecom->NFC_TP_FAT . '</tpFat>' . "\n";
        $xml .= '<verProc>' . $nfecom->NFC_VER_PROC . '</verProc>' . "\n";
        $xml .= '</ide>' . "\n";

        // Emitente
        $xml .= '<emit>' . "\n";
        $xml .= '<CNPJ>' . $nfecom->NFC_CNPJ_EMIT . '</CNPJ>' . "\n";
        $xml .= '<IE>' . $nfecom->NFC_IE_EMIT . '</IE>' . "\n";
        $xml .= '<CRT>' . $nfecom->NFC_CRT_EMIT . '</CRT>' . "\n";
        $xml .= '<xNome>' . htmlspecialchars($nfecom->NFC_X_NOME_EMIT) . '</xNome>' . "\n";
        if ($nfecom->NFC_X_FANT_EMIT) {
            $xml .= '<xFant>' . htmlspecialchars($nfecom->NFC_X_FANT_EMIT) . '</xFant>' . "\n";
        }
        $xml .= '<enderEmit>' . "\n";
        $xml .= '<xLgr>' . htmlspecialchars($nfecom->NFC_X_LGR_EMIT) . '</xLgr>' . "\n";
        if ($nfecom->NFC_NRO_EMIT) {
            $xml .= '<nro>' . htmlspecialchars($nfecom->NFC_NRO_EMIT) . '</nro>' . "\n";
        }
        if ($nfecom->NFC_X_CPL_EMIT) {
            $xml .= '<xCpl>' . htmlspecialchars($nfecom->NFC_X_CPL_EMIT) . '</xCpl>' . "\n";
        }
        $xml .= '<xBairro>' . htmlspecialchars($nfecom->NFC_X_BAIRRO_EMIT) . '</xBairro>' . "\n";
        $xml .= '<cMun>' . $nfecom->NFC_C_MUN_EMIT . '</cMun>' . "\n";
        $xml .= '<xMun>' . htmlspecialchars($nfecom->NFC_X_MUN_EMIT) . '</xMun>' . "\n";
        $xml .= '<CEP>' . $nfecom->NFC_CEP_EMIT . '</CEP>' . "\n";
        $xml .= '<UF>' . $nfecom->NFC_UF_EMIT . '</UF>' . "\n";
        if ($nfecom->NFC_FONE_EMIT) {
            $xml .= '<fone>' . $nfecom->NFC_FONE_EMIT . '</fone>' . "\n";
        }
        $xml .= '</enderEmit>' . "\n";
        $xml .= '</emit>' . "\n";

        // Destinatário
        $xml .= '<dest>' . "\n";
        $xml .= '<xNome>' . htmlspecialchars($nfecom->NFC_X_NOME_DEST) . '</xNome>' . "\n";
        $xml .= '<CNPJ>' . $nfecom->NFC_CNPJ_DEST . '</CNPJ>' . "\n";
        $xml .= '<indIEDest>' . $nfecom->NFC_IND_IE_DEST . '</indIEDest>' . "\n";
        $xml .= '<enderDest>' . "\n";
        $xml .= '<xLgr>' . htmlspecialchars($nfecom->NFC_X_LGR_DEST) . '</xLgr>' . "\n";
        if ($nfecom->NFC_NRO_DEST) {
            $xml .= '<nro>' . htmlspecialchars($nfecom->NFC_NRO_DEST) . '</nro>' . "\n";
        }
        $xml .= '<xBairro>' . htmlspecialchars($nfecom->NFC_X_BAIRRO_DEST) . '</xBairro>' . "\n";
        $xml .= '<cMun>' . $nfecom->NFC_C_MUN_DEST . '</cMun>' . "\n";
        $xml .= '<xMun>' . htmlspecialchars($nfecom->NFC_X_MUN_DEST) . '</xMun>' . "\n";
        $xml .= '<CEP>' . $nfecom->NFC_CEP_DEST . '</CEP>' . "\n";
        $xml .= '<UF>' . $nfecom->NFC_UF_DEST . '</UF>' . "\n";
        $xml .= '</enderDest>' . "\n";
        $xml .= '</dest>' . "\n";

        // Assinante
        $xml .= '<assinante>' . "\n";
        $xml .= '<iCodAssinante>' . $nfecom->NFC_I_COD_ASSINANTE . '</iCodAssinante>' . "\n";
        $xml .= '<tpAssinante>' . $nfecom->NFC_TP_ASSINANTE . '</tpAssinante>' . "\n";
        $xml .= '<tpServUtil>' . $nfecom->NFC_TP_SERV_UTIL . '</tpServUtil>' . "\n";
        $xml .= '<nContrato>' . $nfecom->NFC_N_CONTRATO . '</nContrato>' . "\n";
        $xml .= '<dContratoIni>' . $nfecom->NFC_D_CONTRATO_INI . '</dContratoIni>' . "\n";
        $xml .= '</assinante>' . "\n";

        // Itens
        foreach ($itens as $item) {
            $xml .= '<det nItem="' . $item->NFI_N_ITEM . '">' . "\n";
            $xml .= '<prod>' . "\n";
            $xml .= '<cProd>' . $item->NFI_C_PROD . '</cProd>' . "\n";
            $xml .= '<xProd>' . htmlspecialchars($item->NFI_X_PROD) . '</xProd>' . "\n";
            $xml .= '<cClass>' . $item->NFI_C_CLASS . '</cClass>' . "\n";
            $xml .= '<CFOP>' . $item->NFI_CFOP . '</CFOP>' . "\n";
            $xml .= '<uMed>' . $item->NFI_U_MED . '</uMed>' . "\n";
            $xml .= '<qFaturada>' . number_format($item->NFI_Q_FATURADA, 4, '.', '') . '</qFaturada>' . "\n";
            $xml .= '<vItem>' . number_format($item->NFI_V_ITEM, 2, '.', '') . '</vItem>' . "\n";
            $xml .= '<vDesc>' . number_format($item->NFI_V_DESC, 2, '.', '') . '</vDesc>' . "\n";
            $xml .= '<vOutro>' . number_format($item->NFI_V_OUTRO, 2, '.', '') . '</vOutro>' . "\n";
            $xml .= '<vProd>' . number_format($item->NFI_V_PROD, 2, '.', '') . '</vProd>' . "\n";
            $xml .= '</prod>' . "\n";
            $xml .= '<imposto>' . "\n";
            $xml .= '<ICMS40><CST>' . $item->NFI_CST_ICMS . '</CST></ICMS40>' . "\n";
            $xml .= '<PIS><CST>' . $item->NFI_CST_PIS . '</CST><vBC>' . number_format($item->NFI_V_BC_PIS, 2, '.', '') . '</vBC><pPIS>' . number_format($item->NFI_P_PIS, 2, '.', '') . '</pPIS><vPIS>' . number_format($item->NFI_V_PIS, 2, '.', '') . '</vPIS></PIS>' . "\n";
            $xml .= '<COFINS><CST>' . $item->NFI_CST_COFINS . '</CST><vBC>' . number_format($item->NFI_V_BC_COFINS, 2, '.', '') . '</vBC><pCOFINS>' . number_format($item->NFI_P_COFINS, 2, '.', '') . '</pCOFINS><vCOFINS>' . number_format($item->NFI_V_COFINS, 2, '.', '') . '</vCOFINS></COFINS>' . "\n";
            $xml .= '<FUST><vBC>' . number_format($item->NFI_V_BC_FUST, 2, '.', '') . '</vBC><pFUST>' . number_format($item->NFI_P_FUST, 2, '.', '') . '</pFUST><vFUST>' . number_format($item->NFI_V_FUST, 2, '.', '') . '</vFUST></FUST>' . "\n";
            $xml .= '<FUNTTEL><vBC>' . number_format($item->NFI_V_BC_FUNTEL, 2, '.', '') . '</vBC><pFUNTTEL>' . number_format($item->NFI_P_FUNTEL, 2, '.', '') . '</pFUNTTEL><vFUNTTEL>' . number_format($item->NFI_V_FUNTEL, 2, '.', '') . '</vFUNTTEL></FUNTTEL>' . "\n";
            $xml .= '<retTrib><vRetPIS>0.00</vRetPIS><vRetCofins>0.00</vRetCofins><vRetCSLL>0.00</vRetCSLL><vBCIRRF>' . number_format($item->NFI_V_BC_IRRF, 2, '.', '') . '</vBCIRRF><vIRRF>' . number_format($item->NFI_V_IRRF, 2, '.', '') . '</vIRRF></retTrib>' . "\n";
            $xml .= '</imposto>' . "\n";
            $xml .= '</det>' . "\n";
        }

        // Totais
        $xml .= '<total>' . "\n";
        $xml .= '<vProd>' . number_format($nfecom->NFC_V_PROD, 2, '.', '') . '</vProd>' . "\n";
        $xml .= '<ICMSTot><vBC>0.00</vBC><vICMS>0.00</vICMS><vICMSDeson>0.00</vICMSDeson><vFCP>0.00</vFCP></ICMSTot>' . "\n";
        $xml .= '<vCOFINS>' . number_format($nfecom->NFC_V_COFINS, 2, '.', '') . '</vCOFINS>' . "\n";
        $xml .= '<vPIS>' . number_format($nfecom->NFC_V_PIS, 2, '.', '') . '</vPIS>' . "\n";
        $xml .= '<vFUNTTEL>' . number_format($nfecom->NFC_V_FUNTEL, 2, '.', '') . '</vFUNTTEL>' . "\n";
        $xml .= '<vFUST>' . number_format($nfecom->NFC_V_FUST, 2, '.', '') . '</vFUST>' . "\n";
        $xml .= '<vRetTribTot><vRetPIS>' . number_format($nfecom->NFC_V_RET_PIS, 2, '.', '') . '</vRetPIS><vRetCofins>' . number_format($nfecom->NFC_V_RET_COFINS, 2, '.', '') . '</vRetCofins><vRetCSLL>' . number_format($nfecom->NFC_V_RET_CSLL, 2, '.', '') . '</vRetCSLL><vIRRF>' . number_format($nfecom->NFC_V_IRRF, 2, '.', '') . '</vIRRF></vRetTribTot>' . "\n";
        $xml .= '<vDesc>' . number_format($nfecom->NFC_V_DESC, 2, '.', '') . '</vDesc>' . "\n";
        $xml .= '<vOutro>' . number_format($nfecom->NFC_V_OUTRO, 2, '.', '') . '</vOutro>' . "\n";
        $xml .= '<vNF>' . number_format($nfecom->NFC_V_NF, 2, '.', '') . '</vNF>' . "\n";
        $xml .= '</total>' . "\n";

        // Grupo de Faturamento
        $xml .= '<gFat>' . "\n";
        $xml .= '<CompetFat>' . $nfecom->NFC_COMPET_FAT . '</CompetFat>' . "\n";
        $xml .= '<dVencFat>' . $nfecom->NFC_D_VENC_FAT . '</dVencFat>' . "\n";
        $xml .= '<dPerUsoIni>' . $nfecom->NFC_D_PER_USO_INI . '</dPerUsoIni>' . "\n";
        $xml .= '<dPerUsoFim>' . $nfecom->NFC_D_PER_USO_FIM . '</dPerUsoFim>' . "\n";
        $xml .= '<codBarras>' . $nfecom->NFC_COD_BARRAS . '</codBarras>' . "\n";
        $xml .= '</gFat>' . "\n";

        // Informações complementares
        if ($nfecom->NFC_INF_CPL) {
            $xml .= '<infAdic>' . "\n";
            $xml .= '<infCpl>' . htmlspecialchars($nfecom->NFC_INF_CPL) . '</infCpl>' . "\n";
            $xml .= '</infAdic>' . "\n";
        }

        $xml .= '</infNFCom>' . "\n";

        // Adicionar QRCode Suplementar
        $urlQrCode = "https://dfe-portal.svrs.rs.gov.br/NFCom/QRCode";
        $params = "chNFCom=" . $nfecom->NFC_CH_NFCOM . "&tpAmb=" . $nfecom->NFC_TIPO_AMBIENTE;
        $fullUrl = $urlQrCode . "?" . $params;

        $xml .= '<infNFComSupl>' . "\n";
        $xml .= '<qrCodNFCom>' . trim($fullUrl) . '</qrCodNFCom>' . "\n";
        $xml .= '</infNFComSupl>' . "\n";

        $xml .= '</NFCom>' . "\n";
        $xml .= '</nfcomProc>' . "\n";

        return $xml;
    }

    public function getCliente()
    {
        $id = $this->uri->segment(3);

        if (!$id) {
            echo json_encode(['error' => 'ID do cliente não informado']);
            return;
        }

        $this->db->select('p.*, e.END_LOGRADOURO as logradouro, e.END_NUMERO as numero, e.END_COMPLEMENTO as complemento, e.END_CEP as cep, m.MUN_NOME as municipio_nome, m.MUN_IBGE, es.EST_UF as estado_uf');
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID');
        $this->db->join('enderecos e', 'e.PES_ID = p.PES_ID AND e.END_PADRAO = 1', 'left'); // Endereço padrão
        $this->db->join('municipios m', 'm.MUN_ID = e.MUN_ID', 'left');
        $this->db->join('estados es', 'es.EST_ID = e.EST_ID', 'left');
        $this->db->where('c.CLN_ID', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $cliente = $query->row();

            $response = [
                'nomeCliente' => $cliente->PES_FISICO_JURIDICO == 'F' ? $cliente->PES_NOME : ($cliente->PES_RAZAO_SOCIAL ?: $cliente->PES_NOME),
                'cnpjCliente' => $cliente->PES_CPFCNPJ,
                'logradouroCliente' => $cliente->logradouro ?? '',
                'numeroCliente' => $cliente->numero ?? '',
                'bairroCliente' => '', // Bairro não disponível
                'municipioCliente' => $cliente->municipio_nome ?? '',
                'codMunCliente' => $cliente->codigo_ibge ?? '',
                'cepCliente' => $cliente->cep ?? '',
                'ufCliente' => $cliente->estado_uf ?? ''
            ];

            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Cliente não encontrado']);
        }
    }

    public function getEnderecosCliente()
    {
        $clienteId = $this->uri->segment(3);

        if (!$clienteId) {
            echo json_encode(['error' => 'ID do cliente não informado']);
            return;
        }

        try {
            // Primeiro, buscar o PES_ID do cliente
            $this->db->select('PES_ID');
            $this->db->from('clientes');
            $this->db->where('CLN_ID', $clienteId);
            $clienteQuery = $this->db->get();

            if ($clienteQuery->num_rows() == 0) {
                echo json_encode(['error' => 'Cliente ID ' . $clienteId . ' não encontrado']);
                return;
            }

            $cliente = $clienteQuery->row();
            $pesId = $cliente->PES_ID;

            // Query simplificada - buscar apenas endereços básicos
            $this->db->select('END_ID as id, END_LOGRADOURO as logradouro, END_NUMERO as numero, END_COMPLEMENTO as complemento, END_CEP as cep, END_PADRAO as enderecoPadrao');
            $this->db->from('enderecos');
            $this->db->where('PES_ID', $pesId);
            $this->db->order_by('END_PADRAO', 'desc');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $enderecos = $query->result_array();

                // Debug: mostrar endereços encontrados
                log_message('debug', 'Endereços encontrados para PES_ID ' . $pesId . ': ' . json_encode($enderecos));

                // Formatar endereços para exibição (sem joins complexos)
                foreach ($enderecos as &$endereco) {
                    $enderecoCompleto = $endereco['logradouro'];
                    if (!empty($endereco['numero'])) {
                        $enderecoCompleto .= ', ' . $endereco['numero'];
                    }
                    if (!empty($endereco['complemento'])) {
                        $enderecoCompleto .= ' - ' . $endereco['complemento'];
                    }
                    if (!empty($endereco['cep'])) {
                        $enderecoCompleto .= ' - CEP: ' . $endereco['cep'];
                    }
                    if ($endereco['enderecoPadrao'] == 1) {
                        $enderecoCompleto .= ' (Padrão)';
                        log_message('debug', 'Endereço marcado como padrão: ' . $endereco['id']);
                    }

                    $endereco['enderecoCompleto'] = $enderecoCompleto;

                    // Adicionar campos vazios para compatibilidade
                    $endereco['municipio'] = '';
                    $endereco['codMun'] = '';
                    $endereco['uf'] = '';
                }

                echo json_encode($enderecos);
            } else {
                echo json_encode(['error' => 'Nenhum endereço encontrado para este cliente']);
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar endereços do cliente: ' . $e->getMessage());
            echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
    }

    public function getTelefonesCliente()
    {
        $clienteId = $this->uri->segment(3);

        if (!$clienteId) {
            echo json_encode(['error' => 'ID do cliente não informado']);
            return;
        }

        try {
            // Buscar PES_ID do cliente
            $this->db->select('PES_ID');
            $this->db->from('clientes');
            $this->db->where('CLN_ID', $clienteId);
            $clienteQuery = $this->db->get();

            if ($clienteQuery->num_rows() == 0) {
                echo json_encode(['error' => 'Cliente ID ' . $clienteId . ' não encontrado']);
                return;
            }

            $cliente = $clienteQuery->row();
            $pesId = $cliente->PES_ID;

            // Buscar telefones por tipo
            $this->db->select('TEL_TIPO, TEL_DDD, TEL_NUMERO');
            $this->db->from('telefones');
            $this->db->where('PES_ID', $pesId);
            $query = $this->db->get();

            $telefone = '';
            $celular = '';

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $tel) {
                    $numero = trim(($tel['TEL_DDD'] ?? '') . ' ' . ($tel['TEL_NUMERO'] ?? ''));
                    if (!$numero) {
                        continue;
                    }
                    if ($tel['TEL_TIPO'] === 'Celular' || $tel['TEL_TIPO'] === 'Whatsapp') {
                        if (!$celular) {
                            $celular = $numero;
                        }
                    } elseif (!$telefone) {
                        $telefone = $numero;
                    }
                }
            }

            echo json_encode([
                'telefone' => $telefone,
                'celular' => $celular,
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar telefones do cliente: ' . $e->getMessage());
            echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
    }

    public function buscarClientes()
    {
        try {
            $termo = $this->input->get('q');
            $page = $this->input->get('page', 1);
            $limit = 20;
            $offset = ($page - 1) * $limit;

            // Buscar clientes com paginação
            $this->db->select("c.CLN_ID as id,
                              CASE
                                WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME
                                ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME)
                              END as text,
                              p.PES_CPFCNPJ as cpf_cnpj");
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');

            if (!empty($termo)) {
                $this->db->group_start();
                $this->db->like("CASE
                                WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME
                                ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME)
                              END", $termo);
                $this->db->or_like('p.PES_CPFCNPJ', $termo);
                $this->db->group_end();
            }

            $this->db->order_by("CASE WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME) END ASC");
            $this->db->limit($limit, $offset);

            $query = $this->db->get();
            $clientes = $query->result();

            // Contar total para paginação
            $this->db->select('COUNT(*) as total');
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');

            if (!empty($termo)) {
                $this->db->group_start();
                $this->db->like("CASE
                                WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME
                                ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME)
                              END", $termo);
                $this->db->or_like('p.PES_CPFCNPJ', $termo);
                $this->db->group_end();
            }

            $total_query = $this->db->get();
            $total = $total_query->row()->total;

            // Formatar resposta para Select2
            $response = [
                'results' => $clientes,
                'pagination' => [
                    'more' => ($offset + $limit) < $total
                ]
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar clientes: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erro interno do servidor']);
        }
    }

    public function getContratosCliente()
    {
        $clienteId = $this->uri->segment(3);

        if (!$clienteId) {
            echo json_encode(['error' => 'ID do cliente não informado']);
            return;
        }

        try {
            // Primeiro, buscar o PES_ID do cliente
            $this->db->select('PES_ID');
            $this->db->from('clientes');
            $this->db->where('CLN_ID', $clienteId);
            $clienteQuery = $this->db->get();

            if ($clienteQuery->num_rows() == 0) {
                echo json_encode(['error' => 'Cliente ID ' . $clienteId . ' não encontrado']);
                return;
            }

            $cliente = $clienteQuery->row();
            $pesId = $cliente->PES_ID;

            // Buscar contratos ativos do cliente
            $this->db->select('CTR_ID, CTR_NUMERO, CTR_DATA_INICIO, CTR_DATA_FIM, CTR_TIPO_ASSINANTE, CTR_OBSERVACAO');
            $this->db->from('contratos');
            $this->db->where('PES_ID', $pesId);
            $this->db->where('CTR_SITUACAO', 1); // Apenas contratos ativos
            $this->db->order_by('CTR_DATA_INICIO', 'desc');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $contratos = $query->result_array();
                
                // Formatar datas para o formato esperado pelo frontend
                foreach ($contratos as &$contrato) {
                    if ($contrato['CTR_DATA_INICIO']) {
                        $contrato['CTR_DATA_INICIO'] = date('Y-m-d', strtotime($contrato['CTR_DATA_INICIO']));
                    }
                    if ($contrato['CTR_DATA_FIM']) {
                        $contrato['CTR_DATA_FIM'] = date('Y-m-d', strtotime($contrato['CTR_DATA_FIM']));
                    }
                }

                header('Content-Type: application/json');
                echo json_encode($contratos);
            } else {
                header('Content-Type: application/json');
                echo json_encode([]);
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar contratos do cliente: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * Endpoint AJAX para buscar classificação fiscal automaticamente
     * 
     * Parâmetros via POST:
     * - operacao_comercial_id: ID da operação comercial
     * - cliente_id: ID do cliente (CLN_ID)
     * - produto_id: ID do produto (PRO_ID) - opcional para serviços
     * 
     * Retorna JSON com CFOP, CST, CSOSN e cClassTrib
     */
    public function getClassificacaoFiscal()
    {
        header('Content-Type: application/json');

        try {
            $operacaoComercialId = $this->input->post('operacao_comercial_id');
            $clienteId = $this->input->post('cliente_id'); // CLN_ID
            $produtoId = $this->input->post('produto_id'); // PRO_ID (opcional)

            if (!$operacaoComercialId || !$clienteId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Operação comercial e cliente são obrigatórios'
                ]);
                return;
            }

            // Buscar PES_ID do cliente
            $this->db->select('PES_ID');
            $this->db->from('clientes');
            $this->db->where('CLN_ID', $clienteId);
            $clienteQuery = $this->db->get();
            
            if ($clienteQuery->num_rows() == 0) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Cliente não encontrado'
                ]);
                return;
            }
            
            $pesId = $clienteQuery->row()->PES_ID;

            // Buscar EMP_ID da empresa logada
            $this->load->model('Mapos_model');
            $configuracao = $this->Mapos_model->getConfiguracao();
            $empresaId = $configuracao['idEmpresa'] ?? 1; // Default 1 se não encontrar

            // Se não tiver produto_id, usar um produto genérico ou null
            // Para serviços, podemos passar null ou um produto genérico
            if (!$produtoId) {
                // Tentar buscar um produto genérico ou usar null
                $produtoId = null;
            }

            // Chamar o serviço de classificação fiscal
            $resultado = $this->fiscalclassificationservice->findClassification(
                $operacaoComercialId,
                $pesId, // PES_ID do cliente
                $produtoId,
                $empresaId
            );

            if ($resultado) {
                $clfId = $resultado['CLF_ID'] ?? null;
                $cfop = $resultado['CLF_CFOP'] ?? null;
                $cst = $resultado['CLF_CST'] ?? null;
                $csosn = $resultado['CLF_CSOSN'] ?? null;
                $cClassTrib = $resultado['CLF_CCLASSTRIB'] ?? null;
                $tipoIcms = $resultado['CLF_TIPO_ICMS'] ?? null;
                $mensagemFiscal = $resultado['CLF_MENSAGEM'] ?? null;
                
                // Log detalhado para identificação
                log_message('info', '=== CLASSIFICAÇÃO FISCAL ENCONTRADA ===');
                log_message('info', 'CLF_ID: ' . ($clfId ?? 'N/A'));
                log_message('info', 'OPC_ID: ' . $operacaoComercialId);
                log_message('info', 'Cliente ID (CLN_ID): ' . $clienteId);
                log_message('info', 'Cliente PES_ID: ' . $pesId);
                log_message('info', 'Produto ID: ' . ($produtoId ?? 'N/A'));
                log_message('info', 'Empresa ID: ' . $empresaId);
                log_message('info', 'CFOP: ' . ($cfop ?? 'N/A'));
                log_message('info', 'CST: ' . ($cst ?? 'N/A'));
                log_message('info', 'CSOSN: ' . ($csosn ?? 'N/A'));
                log_message('info', 'cClassTrib: ' . ($cClassTrib ?? 'N/A'));
                log_message('info', 'Tipo ICMS: ' . ($tipoIcms ?? 'N/A'));
                log_message('info', 'Mensagem Fiscal: ' . (substr($mensagemFiscal ?? '', 0, 100)));
                log_message('info', '==========================================');
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $clfId,
                        'cfop' => $cfop,
                        'cst' => $cst,
                        'csosn' => $csosn,
                        'cClassTrib' => $cClassTrib,
                        'tipo_icms' => $tipoIcms,
                        'mensagem_fiscal' => $mensagemFiscal
                    ]
                ]);
            } else {
                log_message('info', '=== CLASSIFICAÇÃO FISCAL NÃO ENCONTRADA ===');
                log_message('info', 'OPC_ID: ' . $operacaoComercialId);
                log_message('info', 'Cliente ID (CLN_ID): ' . $clienteId);
                log_message('info', 'Cliente PES_ID: ' . $pesId);
                log_message('info', 'Produto ID: ' . ($produtoId ?? 'N/A'));
                log_message('info', 'Empresa ID: ' . $empresaId);
                log_message('info', '==========================================');
                
                echo json_encode([
                    'success' => false,
                    'error' => 'Nenhuma classificação fiscal encontrada para os parâmetros informados'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar classificação fiscal: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao buscar classificação fiscal: ' . $e->getMessage(),
                'trace' => $this->config->item('log_threshold') >= 4 ? $e->getTraceAsString() : null
            ]);
        } catch (Error $e) {
            log_message('error', 'Erro fatal ao buscar classificação fiscal: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'error' => 'Erro fatal ao buscar classificação fiscal: ' . $e->getMessage()
            ]);
        }
    }

    public function autoCompleteServico()
    {
        if (isset($_GET['term'])) {
            $q = trim($_GET['term']);

            $this->db->select('PRO_ID, PRO_DESCRICAO, PRO_PRECO_VENDA, PRO_CCLASS_SERV, PRO_UNID_MEDIDA');
            $this->db->from('produtos');
            $this->db->where('PRO_TIPO', 2);
            if ($q !== '') {
                $this->db->like('PRO_DESCRICAO', $q);
            }
            $this->db->limit(25);
            $query = $this->db->get();

            $row_set = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $preco = $row['PRO_PRECO_VENDA'] ?? 0;
                    $row_set[] = [
                        'label' => $row['PRO_DESCRICAO'],
                        'id' => $row['PRO_ID'],
                        'preco' => $preco,
                        'cClass' => $row['PRO_CCLASS_SERV'],
                        'uMed' => $row['PRO_UNID_MEDIDA']
                    ];
                }
            }

            header('Content-Type: application/json');
            echo json_encode($row_set);
        }
    }
    private function gerarQRCodeNFCom($nfecom, $configFiscal = null)
    {
        $chave = preg_replace('/\D/', '', $nfecom->NFC_CH_NFCOM);
        $tpAmb = (int) $nfecom->NFC_TIPO_AMBIENTE;

        // Schema XSD espera & na query string (o CDATA já protege o caractere)
        $qrCode = 'https://dfe-portal.svrs.rs.gov.br/NFCom/QRCode?chNFCom=' . $chave . '&tpAmb=' . $tpAmb;

        return $qrCode;
    }

    private function gerarHashQRCode($qrCode, $csc)
    {
        return strtoupper(hash('sha256', $qrCode . $csc));
    }

    public function get_cUF($uf)
    {
        $ufs = [
            'AC' => '12',
            'AL' => '27',
            'AP' => '16',
            'AM' => '13',
            'BA' => '29',
            'CE' => '23',
            'DF' => '53',
            'ES' => '32',
            'GO' => '52',
            'MA' => '21',
            'MT' => '51',
            'MS' => '50',
            'MG' => '31',
            'PA' => '15',
            'PB' => '25',
            'PR' => '41',
            'PE' => '26',
            'PI' => '22',
            'RJ' => '33',
            'RN' => '24',
            'RS' => '43',
            'RO' => '11',
            'RR' => '14',
            'SC' => '42',
            'SP' => '35',
            'SE' => '28',
            'TO' => '17'
        ];
        return $ufs[$uf] ?? '43';
    }

    private function prepararDadosEnvio($id)
    {
        $nfecom = $this->Nfecom_model->getById($id);
        if (!$nfecom)
            throw new Exception("NFCom não encontrada.");

        $configFiscal = $this->getConfiguracaoNfcom();
        $emitente = $this->Nfe_model->getEmit();

        // Buscar cliente/destinatário completo com endereço e documentos (IE)
        $this->db->select('c.*, p.*, e.END_LOGRADOURO, e.END_NUMERO, e.END_COMPLEMENTO, e.END_CEP, b.BAI_NOME, m.MUN_NOME, m.MUN_IBGE, es.EST_UF, d.DOC_NUMERO as PES_IE, d.DOC_NATUREZA_CONTRIBUINTE');
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID');
        $this->db->join('enderecos e', 'e.PES_ID = p.PES_ID AND e.END_PADRAO = 1', 'left');
        $this->db->join('bairros b', 'b.BAI_ID = e.BAI_ID', 'left');
        $this->db->join('municipios m', 'm.MUN_ID = e.MUN_ID', 'left');
        $this->db->join('estados es', 'es.EST_ID = e.EST_ID', 'left');
        $this->db->join('documentos d', "d.PES_ID = p.PES_ID AND d.DOC_TIPO_DOCUMENTO = 'Inscrição Estadual'", 'left');

        // Se CLN_ID estiver disponível, usar ele (mais preciso)
        if (!empty($nfecom->CLN_ID)) {
            $this->db->where('c.CLN_ID', $nfecom->CLN_ID);
        } else {
            // Fallback por CNPJ/CPF se for uma nota antiga sem CLN_ID
            $cnpjLimpo = preg_replace('/\D/', '', $nfecom->NFC_CNPJ_DEST);
            $this->db->where('p.PES_CPFCNPJ', $cnpjLimpo);
        }

        $cliente = $this->db->get()->row();

        if (!$cliente)
            throw new Exception("Cliente não encontrado.");

        // Usar a UF do emitente para gerar o cUF correto
        $ufEmit = $emitente['enderEmit']['UF'] ?? $nfecom->NFC_UF_EMIT ?? 'GO';
        $cUF = $this->get_cUF($ufEmit);

        // Fallback manual se cUF ainda não for válido (deve ser 52 para GO, por exemplo)
        if (empty($cUF) || !is_numeric($cUF)) {
            $cUF = '52'; // Default para Goiás conforme solicitado
        }

        $listaItens = [];
        $itens = $this->Nfecom_model->getItens($id);
        log_message('info', 'PrepararDadosEnvio - Total de itens encontrados para NFCom ID ' . $id . ': ' . count($itens));
        
        // Coletar mensagens fiscais dos itens salvos
        $mensagensFiscais = [];
        $fields = $this->db->list_fields('nfecom_itens');
        $temClfId = in_array('CLF_ID', $fields) || in_array('clf_id', $fields);
        
        foreach ($itens as $it) {
            log_message('info', 'Item NFCom - NFI_N_ITEM: ' . $it->NFI_N_ITEM . ', NFI_C_PROD: ' . $it->NFI_C_PROD . ', NFI_X_PROD: ' . $it->NFI_X_PROD);
            
            // Buscar mensagem fiscal se o item tiver CLF_ID
            if ($temClfId) {
                $clfId = isset($it->CLF_ID) ? $it->CLF_ID : (isset($it->clf_id) ? $it->clf_id : null);
                if (!empty($clfId)) {
                    $this->db->select('CLF_MENSAGEM');
                    $this->db->from('classificacao_fiscal');
                    $this->db->where('CLF_ID', $clfId);
                    $clfQuery = $this->db->get();
                    if ($clfQuery->num_rows() > 0) {
                        $clf = $clfQuery->row();
                        if (!empty($clf->CLF_MENSAGEM)) {
                            // Evitar duplicatas
                            if (!in_array($clf->CLF_MENSAGEM, $mensagensFiscais)) {
                                $mensagensFiscais[] = $clf->CLF_MENSAGEM;
                            }
                        }
                    }
                }
            }
            
            $listaItens[] = [
                'nItem' => $it->NFI_N_ITEM,
                'codigo' => $it->NFI_C_PROD,
                'descricao' => $it->NFI_X_PROD,
                'classificacao_item' => $it->NFI_C_CLASS,
                'cfop' => $it->NFI_CFOP,
                'unidade' => $it->NFI_U_MED,
                'quantidade' => $it->NFI_Q_FATURADA,
                'valor_unitario' => $it->NFI_V_ITEM,
                'valor_total' => $it->NFI_V_PROD,
                'desconto' => $it->NFI_V_DESC,
                'outros' => $it->NFI_V_OUTRO,
                'imposto' => [
                    'icms' => [
                        'cst' => $it->NFI_CST_ICMS
                        // Para ICMS40 (isenção), não incluir vBC, pICMS, vICMS
                    ],
                    'pis' => [
                        'cst' => $it->NFI_CST_PIS,
                        'vBC' => $it->NFI_V_BC_PIS,
                        'pPIS' => $it->NFI_P_PIS,
                        'vPIS' => $it->NFI_V_PIS
                    ],
                    'cofins' => [
                        'cst' => $it->NFI_CST_COFINS,
                        'vBC' => $it->NFI_V_BC_COFINS,
                        'pCOFINS' => $it->NFI_P_COFINS,
                        'vCOFINS' => $it->NFI_V_COFINS
                    ]
                ]
            ];
        }
        
        // Atualizar NFC_INF_CPL com observação + mensagens fiscais se necessário
        // NFC_INF_CPL deve conter: Observação digitada pelo usuário + Mensagens fiscais (sem duplicatas)
        $infCplAtual = trim($nfecom->NFC_INF_CPL ?? '');
        
        // Buscar observação original da nota (se houver campo separado ou se estiver no infCpl)
        // Como a observação já deve estar no NFC_INF_CPL quando a nota foi salva,
        // vamos verificar se precisa atualizar apenas as mensagens fiscais
        
        // Se há mensagens fiscais, construir o infCpl completo
        if (!empty($mensagensFiscais)) {
            $mensagensUnicas = [];
            foreach ($mensagensFiscais as $mensagem) {
                $mensagemLimpa = trim($mensagem);
                if (!empty($mensagemLimpa)) {
                    // Evitar duplicatas
                    if (!in_array($mensagemLimpa, $mensagensUnicas)) {
                        $mensagensUnicas[] = $mensagemLimpa;
                    }
                }
            }
            
            // Extrair observação do infCpl atual (tudo antes das mensagens fiscais)
            $observacaoOriginal = '';
            $mensagensNoInfCpl = [];
            
            // Se o infCpl atual contém mensagens fiscais conhecidas, separar observação
            foreach ($mensagensUnicas as $msg) {
                if (strpos($infCplAtual, $msg) !== false) {
                    $mensagensNoInfCpl[] = $msg;
                }
            }
            
            // Se encontrou mensagens no infCpl, extrair a observação (parte antes das mensagens)
            // A observação termina antes da primeira mensagem fiscal (separada por ponto e vírgula)
            if (!empty($mensagensNoInfCpl)) {
                $primeiraMensagem = $mensagensNoInfCpl[0];
                $posPrimeiraMensagem = strpos($infCplAtual, $primeiraMensagem);
                if ($posPrimeiraMensagem !== false) {
                    $observacaoOriginal = trim(substr($infCplAtual, 0, $posPrimeiraMensagem));
                    // Remover ponto e vírgula e espaços no final da observação (se houver)
                    $observacaoOriginal = rtrim($observacaoOriginal, '; ');
                }
            } else {
                // Se não encontrou mensagens, todo o infCpl atual é a observação
                $observacaoOriginal = $infCplAtual;
            }
            
            // Construir infCpl: Observação + Mensagens fiscais (separadas por ponto e vírgula)
            $infCplNovo = '';
            if (!empty($observacaoOriginal)) {
                $infCplNovo = $observacaoOriginal;
            }
            
            if (!empty($mensagensUnicas)) {
                $mensagensTexto = implode('; ', $mensagensUnicas);
                if (!empty($infCplNovo)) {
                    $infCplNovo .= '; ' . $mensagensTexto;
                } else {
                    $infCplNovo = $mensagensTexto;
                }
            }
            
            // Atualizar no banco apenas se for diferente do atual
            if (trim($infCplNovo) !== $infCplAtual) {
                $this->Nfecom_model->edit('nfecom_capa', ['NFC_INF_CPL' => $infCplNovo], 'NFC_ID', $id);
                // Recarregar nfecom com o infCpl atualizado
                $nfecom = $this->Nfecom_model->getById($id);
            }
        }

        return [
            'chave' => preg_replace('/\D/', '', $nfecom->NFC_CH_NFCOM),
            'ide' => [
                'cUF' => $cUF,
                'tpAmb' => $configFiscal->CFG_AMBIENTE,
                'serie' => $nfecom->NFC_SERIE ?: '1',
                'nNF' => $nfecom->NFC_NNF,
                'cNF' => $nfecom->NFC_CNF ?: str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'cDV' => $nfecom->NFC_CDV ?: 0,
                'dhEmi' => date('c', strtotime($nfecom->NFC_DHEMI ?: 'now')),
                'tpEmis' => $nfecom->NFC_TP_EMIS ?: 1,
                'nSiteAutoriz' => 0,
                'cMunFG' => $emitente['enderEmit']['cMun'], // IBGE da Empresa
                'finNFCom' => 0,
                'tpFat' => 0,
                'verProc' => $this->config->item('app_version') ?: '1.0.0'
            ],
            'emitente' => [
                'cnpj' => $emitente['CNPJ'],
                'ie' => $emitente['IE'],
                'crt' => $emitente['CRT'],
                'razao_social' => $emitente['xNome'],
                'nome_fantasia' => $emitente['xFant'],
                'endereco' => [
                    'logradouro' => $emitente['enderEmit']['xLgr'],
                    'numero' => $emitente['enderEmit']['nro'],
                    'complemento' => $emitente['enderEmit']['xCpl'],
                    'bairro' => $emitente['enderEmit']['xBairro'],
                    'codigo_municipio' => $emitente['enderEmit']['cMun'],
                    'municipio' => $emitente['enderEmit']['xMun'],
                    'cep' => $emitente['enderEmit']['CEP'],
                    'uf' => $emitente['enderEmit']['UF']
                ],
                'telefone' => $emitente['enderEmit']['fone']
            ],
            'destinatario' => [
                'nome' => $cliente->PES_NOME,
                'cnpj' => preg_replace('/\D/', '', $cliente->PES_CPFCNPJ),
                'indicador_ie' => ($cliente->DOC_NATUREZA_CONTRIBUINTE == 'Contribuinte') ? '1' : '9',
                'ie' => $cliente->PES_IE,
                'endereco' => [
                    'logradouro' => $cliente->END_LOGRADOURO,
                    'numero' => $cliente->END_NUMERO,
                    'complemento' => $cliente->END_COMPLEMENTO ?? '',
                    'bairro' => $cliente->BAI_NOME,
                    'codigo_municipio' => $cliente->MUN_IBGE,
                    'municipio' => $cliente->MUN_NOME,
                    'cep' => preg_replace('/\D/', '', $cliente->END_CEP),
                    'uf' => $cliente->EST_UF
                ]
            ],
            'assinante' => [
                'codigo' => $nfecom->NFC_I_COD_ASSINANTE ?: preg_replace('/\D/', '', $cliente->PES_CPFCNPJ),
                'tipo' => $nfecom->NFC_TP_ASSINANTE ?: 3,
                'tipo_servico' => $nfecom->NFC_TP_SERV_UTIL ?: 6,
                'numero_contrato' => $nfecom->NFC_N_CONTRATO,
                'data_inicio_contrato' => $nfecom->NFC_D_CONTRATO_INI,
                'data_fim_contrato' => $nfecom->NFC_D_CONTRATO_FIM ?: null
            ],
            'itens' => $listaItens,
            'totais' => [
                'vProd' => array_sum(array_column($listaItens, 'valor_total')),
                'icms' => [
                    'vBC' => 0.00,
                    'vICMS' => 0.00,
                    'vICMSDeson' => 0.00,
                    'vFCP' => 0.00
                ],
                'vCOFINS' => array_sum(array_map(function ($item) {
                    return $item['imposto']['cofins']['vCOFINS'];
                }, $listaItens)),
                'vPIS' => array_sum(array_map(function ($item) {
                    return $item['imposto']['pis']['vPIS'];
                }, $listaItens)),
                'vFUNTTEL' => 0.00, // Valor FUNTTEL
                'vFUST' => 0.00,    // Valor FUST
                'retTribTot' => [
                    'vRetPIS' => 0.00,
                    'vRetCofins' => 0.00,
                    'vRetCSLL' => 0.00,
                    'vIRRF' => 0.00
                ],
                'vDesc' => array_sum(array_column($listaItens, 'desconto')),
                'vOutro' => array_sum(array_column($listaItens, 'outros')),
                'vNF' => array_sum(array_column($listaItens, 'valor_total')) - array_sum(array_column($listaItens, 'desconto')) + array_sum(array_column($listaItens, 'outros'))
            ],
            'faturamento' => [
                'competencia' => $nfecom->NFC_COMPET_FAT,
                'vencimento' => $nfecom->NFC_D_VENC_FAT,
                'periodo_inicio' => $nfecom->NFC_D_PER_USO_INI,
                'periodo_fim' => $nfecom->NFC_D_PER_USO_FIM,
                'cod_barras' => $nfecom->NFC_COD_BARRAS ?? '1' // Código de barras padrão se não informado
            ],
            'informacoes_adicionais' => [
                'complementar' => $nfecom->NFC_INF_CPL
            ],
            'suplementar' => [
                'qrCode' => $this->gerarQRCodeNFCom($nfecom, $configFiscal)
            ]
        ];
    }

    private function getCodMunIBGE($cidade, $uf)
    {
        $this->db->select('m.MUN_IBGE');
        $this->db->from('municipios m');
        $this->db->join('estados e', 'e.EST_ID = m.EST_ID');
        $this->db->like('m.MUN_NOME', $cidade);
        $this->db->where('e.EST_UF', $uf);
        $this->db->limit(1);
        $res = $this->db->get()->row();
        return $res ? $res->MUN_IBGE : '5218300'; // Fallback Posse-GO (7 dígitos)
    }
}
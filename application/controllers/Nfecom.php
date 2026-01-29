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
        $this->db->select("c.cln_id as idClientes,
                          CASE
                            WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome
                            ELSE COALESCE(p.pes_razao_social, p.pes_nome)
                          END as nomeCliente");
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.pes_id = c.pes_id', 'left');
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by("CASE WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome ELSE COALESCE(p.pes_razao_social, p.pes_nome) END ASC");
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
        $this->db->select("$primary_key as idServicos, pro_descricao as nome");
        $this->db->from('produtos');
        $this->db->where('pro_tipo', 2);
        $this->db->where('produtos.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('pro_descricao', 'asc');
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
        $this->db->select("c.cln_id as id,
                          CASE
                            WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome
                            ELSE COALESCE(p.pes_nome, p.pes_razao_social)
                          END as text,
                          p.pes_nome as nome_fantasia,
                          p.pes_razao_social as razao_social,
                          p.pes_cpfcnpj as cpf_cnpj,
                          p.pes_codigo as codigo");
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.pes_id = c.pes_id', 'left');
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by("CASE WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome ELSE COALESCE(p.pes_nome, p.pes_razao_social) END ASC"); // Ordem alfabética para melhor UX
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

        $this->db->select("$produtos_primary_key as idServicos, pro_descricao as nome");
        $this->db->from('produtos');
        $this->db->where('pro_tipo', 2);
        $this->db->where('produtos.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('pro_descricao', 'asc');
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

                // Processar data de vencimento
                // O campo HTML type="date" já envia no formato yyyy-mm-dd
                if (!empty($data['dataVencimento'])) {
                    // Verificar se já está no formato ISO (yyyy-mm-dd)
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['dataVencimento'])) {
                        // Já está no formato correto, usar diretamente
                        $data['dataVencimento'] = $data['dataVencimento'];
                    } else {
                        // Tentar converter de formato brasileiro (dd/mm/yyyy)
                        try {
                            $dataVenc = explode('/', $data['dataVencimento']);
                            if (count($dataVenc) == 3) {
                                $data['dataVencimento'] = $dataVenc[2] . '-' . $dataVenc[1] . '-' . $dataVenc[0];
                            } else {
                                // Se não conseguir converter, usar data padrão
                                $data['dataVencimento'] = date('Y-m-d', strtotime('+30 days'));
                            }
                        } catch (Exception $e) {
                            $data['dataVencimento'] = date('Y-m-d', strtotime('+30 days'));
                        }
                    }
                } else {
                    // Se não foi informada, usar data padrão (30 dias)
                    $data['dataVencimento'] = date('Y-m-d', strtotime('+30 days'));
                }

                // Processar período de uso - RESPEITAR O VALOR INFORMADO PELO USUÁRIO
                if (!empty($data['dataPeriodoIni'])) {
                    // Verificar se já está no formato ISO (yyyy-mm-dd)
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['dataPeriodoIni'])) {
                        // Já está no formato correto, usar diretamente
                        $data['dataPeriodoIni'] = $data['dataPeriodoIni'];
                    } else {
                        // Tentar converter de formato brasileiro (dd/mm/yyyy) ou outros formatos
                        try {
                            // Tentar formato brasileiro (dd/mm/yyyy)
                            if (strpos($data['dataPeriodoIni'], '/') !== false) {
                                $dataPerIni = explode('/', $data['dataPeriodoIni']);
                                if (count($dataPerIni) == 3) {
                                    $data['dataPeriodoIni'] = $dataPerIni[2] . '-' . $dataPerIni[1] . '-' . $dataPerIni[0];
                                } else {
                                    // Se não conseguir converter, manter o valor original
                                    log_message('error', 'Formato de data de período início não reconhecido: ' . $data['dataPeriodoIni']);
                                }
                            } else {
                                // Tentar usar strtotime para outros formatos
                                $timestamp = strtotime($data['dataPeriodoIni']);
                                if ($timestamp !== false) {
                                    $data['dataPeriodoIni'] = date('Y-m-d', $timestamp);
                                } else {
                                    log_message('error', 'Não foi possível converter data de período início: ' . $data['dataPeriodoIni']);
                                }
                            }
                        } catch (Exception $e) {
                            log_message('error', 'Erro ao processar data de período início: ' . $e->getMessage());
                            // NÃO usar data padrão - manter o valor original ou lançar erro
                        }
                    }
                }

                if (!empty($data['dataPeriodoFim'])) {
                    // Verificar se já está no formato ISO (yyyy-mm-dd)
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['dataPeriodoFim'])) {
                        // Já está no formato correto, usar diretamente
                        $data['dataPeriodoFim'] = $data['dataPeriodoFim'];
                    } else {
                        // Tentar converter de formato brasileiro (dd/mm/yyyy) ou outros formatos
                        try {
                            // Tentar formato brasileiro (dd/mm/yyyy)
                            if (strpos($data['dataPeriodoFim'], '/') !== false) {
                                $dataPerFim = explode('/', $data['dataPeriodoFim']);
                                if (count($dataPerFim) == 3) {
                                    $data['dataPeriodoFim'] = $dataPerFim[2] . '-' . $dataPerFim[1] . '-' . $dataPerFim[0];
                                } else {
                                    // Se não conseguir converter, manter o valor original
                                    log_message('error', 'Formato de data de período fim não reconhecido: ' . $data['dataPeriodoFim']);
                                }
                            } else {
                                // Tentar usar strtotime para outros formatos
                                $timestamp = strtotime($data['dataPeriodoFim']);
                                if ($timestamp !== false) {
                                    $data['dataPeriodoFim'] = date('Y-m-d', $timestamp);
                                } else {
                                    log_message('error', 'Não foi possível converter data de período fim: ' . $data['dataPeriodoFim']);
                                }
                            }
                        } catch (Exception $e) {
                            log_message('error', 'Erro ao processar data de período fim: ' . $e->getMessage());
                            // NÃO usar data padrão - manter o valor original ou lançar erro
                        }
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
                    $this->db->select('pro_descricao as descricao');
                    $this->db->from('produtos');
                    $this->db->where($produtos_primary_key, $servico['id']);
                    $this->db->where('produtos.ten_id', $this->session->userdata('ten_id'));
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

                // Buscar operação comercial
                $operacaoId = (int)$this->input->post('opc_id');
                
                // Buscar dados completos do cliente incluindo endereço selecionado (ANTES do cálculo de tributação)
                $enderecoId = $data['enderecoClienteSelect'] ?? null;
                $this->db->select('p.pes_cpfcnpj, p.pes_nome, p.pes_razao_social, p.pes_fisico_juridico, e.end_logradouro as logradouro, e.end_numero as numero, e.end_complemento as complemento, e.end_cep as cep, b.bai_nome as bairro, m.mun_nome as municipio_nome, m.mun_ibge, es.est_uf as estado_uf, COALESCE(c.cln_cobrar_irrf, 0) as cobrar_irrf');
                $this->db->from('clientes c');
                $this->db->join('pessoas p', 'p.pes_id = c.pes_id');
                $this->db->join('enderecos e', 'e.pes_id = p.pes_id', 'left');
                $this->db->join('bairros b', 'b.bai_id = e.bai_id', 'left');
                $this->db->join('municipios m', 'm.mun_id = e.mun_id', 'left');
                $this->db->join('estados es', 'es.est_id = e.est_id', 'left');
                $this->db->where('c.cln_id', $data['clientes_id']);
                $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
                if (!empty($enderecoId)) {
                    $this->db->where('e.end_id', $enderecoId);
                } else {
                    $this->db->where('e.end_padrao', 1);
                }
                $this->db->limit(1);
                $cliente_query = $this->db->get();
                $cliente = $cliente_query->row();

                // Validar dados obrigatórios antes de calcular tributação
                if (!$cliente) {
                    $this->session->set_flashdata('error', 'Cliente não encontrado ou sem endereço cadastrado. Verifique o cadastro do cliente.');
                    redirect(site_url('nfecom/adicionar'));
                }

                if (empty($cliente->estado_uf)) {
                    $this->session->set_flashdata('error', 'Cliente não possui UF cadastrada no endereço selecionado. Verifique o cadastro do cliente.');
                    redirect(site_url('nfecom/adicionar'));
                }

                if (empty($operacaoId)) {
                    $this->session->set_flashdata('error', 'Operação comercial não informada. Selecione uma operação comercial.');
                    redirect(site_url('nfecom/adicionar'));
                }

                if ($cliente) {
                    $data['nomeCliente'] = $cliente->pes_fisico_juridico == 'F' ? $cliente->pes_nome : ($cliente->pes_razao_social ?: $cliente->pes_nome);
                    $data['cnpjCliente'] = $cliente->pes_cpfcnpj ?? '';
                    $data['logradouroCliente'] = $cliente->logradouro ?? '';
                    $data['numeroCliente'] = $cliente->numero ?? '';
                    $data['bairroCliente'] = $cliente->bairro ?? '';
                    $data['municipioCliente'] = $cliente->municipio_nome ?? '';
                    $data['codMunCliente'] = $cliente->mun_ibge ?? '';
                    $data['cepCliente'] = $cliente->cep ?? '';
                    $data['ufCliente'] = $cliente->estado_uf ?? '';
                    $data['cobrar_irrf'] = !empty($cliente->cobrar_irrf); // Do cliente: 1=cobrar IRRF na NFCom, 0=não cobrar
                }

                // Validar dados obrigatórios ANTES de calcular tributação
                if (empty($servicos) || $totalValorBruto == 0) {
                    $this->session->set_flashdata('error', 'É necessário informar pelo menos um serviço válido com quantidade e valor para gerar a NFeCom.');
                    redirect(site_url('nfecom/adicionar'));
                }
                
                // Validar ten_id antes de calcular tributação
                $tenId = $this->session->userdata('ten_id');
                if (empty($tenId) || $tenId == 0) {
                    $this->session->set_flashdata('error', 'Erro: Tenant ID não encontrado na sessão. Faça login novamente.');
                    redirect(site_url('nfecom/adicionar'));
                }
                
                // Calcular tributação usando a API - SEMPRE usar a API, sem valores fixos
                $pis = 0;
                $cofins = 0;
                // IRRF: Aplicar 4,8% sobre o valor líquido (valor bruto - comissão agência)
                // Base de cálculo: valor líquido (valorNF)
                $irrf = 0.00;
                $errosTributacao = [];
                $servicosSemTributacao = [];
                
                // Inicializar totais de ICMS, ICMS ST, FCP e IRRF
                $totalIcms = 0;
                $totalIcmsSt = 0;
                $totalFcp = 0;
                $totalBaseIcms = 0;
                $totalIrrf = 0; // Total do IRRF será a soma dos IRRF dos itens
                
                // Calcular tributação para cada serviço e somar os valores
                if (!empty($servicos) && $operacaoId && !empty($data['clientes_id'])) {
                    foreach ($servicos as $index => $servico) {
                        if (!empty($servico['id']) && !empty($servico['quantidade']) && !empty($servico['valorUnitario'])) {
                            $produtoId = $servico['id'];
                            $quantidade = floatval($servico['quantidade']);
                            $valorUnitario = floatval($servico['valorUnitario']);
                            
                            // Verificar se o produto tem NCM (obrigatório para cálculo)
                            $this->db->select('pro_descricao, ncm_id');
                            $this->db->from('produtos');
                            $this->db->where($produtos_primary_key, $produtoId);
                            $this->db->where('ten_id', $this->session->userdata('ten_id'));
                            $produtoQuery = $this->db->get();
                            $produto = $produtoQuery->row();
                            
                            if (!$produto) {
                                $errosTributacao[] = "Serviço #" . ($index + 1) . ": Produto não encontrado (ID: $produtoId)";
                                $servicosSemTributacao[] = $index + 1;
                                continue;
                            }
                            
                            if (empty($produto->ncm_id)) {
                                $errosTributacao[] = "Serviço #" . ($index + 1) . " ({$produto->pro_descricao}): NCM não configurado no produto";
                                $servicosSemTributacao[] = $index + 1;
                                continue;
                            }
                            
                            // Calcular tributação usando a API (com endereço selecionado)
                            log_message('debug', "Calculando tributação para serviço #" . ($index + 1) . " - Produto ID: $produtoId, Cliente ID: {$data['clientes_id']}, Operação: $operacaoId, Endereço: " . ($enderecoId ?? 'padrão'));
                            
                            $tributacao = $this->calcularTributacao(
                                $produtoId,
                                $data['clientes_id'],
                                $operacaoId,
                                $valorUnitario,
                                $quantidade,
                                'saida',
                                $enderecoId // Passar endereço selecionado
                            );
                            
                            if (!$tributacao) {
                                $errosTributacao[] = "Serviço #" . ($index + 1) . " ({$produto->pro_descricao}): Não foi possível calcular a tributação. Verifique as configurações fiscais e o log de erros.";
                                $servicosSemTributacao[] = $index + 1;
                                continue;
                            }
                            
                            // Validar se retornou dados de PIS/COFINS
                            if (!isset($tributacao['impostos_federais']['pis']) || !isset($tributacao['impostos_federais']['cofins'])) {
                                $errosTributacao[] = "Serviço #" . ($index + 1) . " ({$produto->pro_descricao}): Dados de tributação incompletos retornados pela API.";
                                $servicosSemTributacao[] = $index + 1;
                                continue;
                            }
                            
                            // Somar valores da API
                            $pis += floatval($tributacao['impostos_federais']['pis']['valor'] ?? 0);
                            $cofins += floatval($tributacao['impostos_federais']['cofins']['valor'] ?? 0);
                            
                            log_message('debug', "Tributação calculada para serviço #" . ($index + 1) . " - PIS: " . ($tributacao['impostos_federais']['pis']['valor'] ?? 0) . ", COFINS: " . ($tributacao['impostos_federais']['cofins']['valor'] ?? 0));
                        }
                    }
                } else {
                    // Se não tem operação ou cliente, adicionar erro
                    if (empty($operacaoId)) {
                        $errosTributacao[] = "Operação comercial não informada.";
                    }
                    if (empty($data['clientes_id'])) {
                        $errosTributacao[] = "Cliente não informado.";
                    }
                }
                
                // Se houver erros, não permitir salvar
                if (!empty($errosTributacao)) {
                    $mensagemErro = "Não foi possível calcular a tributação para os seguintes serviços:\n\n" . implode("\n", $errosTributacao);
                    $mensagemErro .= "\n\nPor favor, verifique:\n";
                    $mensagemErro .= "- Se os produtos possuem NCM configurado\n";
                    $mensagemErro .= "- Se existe tributação federal configurada para o NCM\n";
                    $mensagemErro .= "- Se a operação comercial está correta\n";
                    $mensagemErro .= "- Se o cliente possui endereço com UF cadastrada\n";
                    $mensagemErro .= "- Se o Tenant ID está configurado corretamente (ten_id: " . $tenId . ")\n";
                    $mensagemErro .= "- Verifique o log de erros para mais detalhes";
                    
                    log_message('error', 'Erros de tributação na NFeCom: ' . implode(' | ', $errosTributacao) . ' | ten_id: ' . $tenId);
                    
                    $this->session->set_flashdata('error', $mensagemErro);
                    redirect(site_url('nfecom/adicionar'));
                }
                
                // Calcular IRRF: 4,8% sobre o valor líquido (base de cálculo) — somente se o cliente tiver "Cobrar IRRF" ativo
                // NOTA: Este valor será substituído pela soma dos IRRF dos itens após salvar
                $irrf = 0.00;
                if (!empty($data['cobrar_irrf']) && $valorLiquido > 0) {
                    $irrf = ($valorLiquido * 4.8) / 100;
                    $irrf = round($irrf, 2);
                    log_message('info', 'IRRF calculado (provisório): 4,8% sobre valor líquido de R$ ' . number_format($valorLiquido, 2, ',', '.') . ' = R$ ' . number_format($irrf, 2, ',', '.') . ' (será substituído pela soma dos itens)');
                } else {
                    log_message('info', 'IRRF não aplicado: cliente sem cobrança de IRRF ou valor líquido zero.');
                }
                
                // Valor da NF conforme regra G137: vNF = vProd + vOutro - vDesc - vRetPIS - vRetCofins - vRetCSLL - vIRRF
                $valorNF = $valorLiquido - $irrf;
                log_message('info', 'Valor da NF calculado: R$ ' . number_format($valorLiquido, 2, ',', '.') . ' - R$ ' . number_format($irrf, 2, ',', '.') . ' (IRRF) = R$ ' . number_format($valorNF, 2, ',', '.'));

                $nomeServico = implode('; ', $nomesServicos);


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
                $codigoUf = $this->getCodigoUf($emit['enderEmit']['uf'] ?? '');
                $nfecomData = [
                'nfc_cuf' => $codigoUf ?: ($emit['enderEmit']['uf'] ?? ''),
                'nfc_tipo_ambiente' => $configFiscal ? $configFiscal->cfg_ambiente : $this->data['configuration']['ambiente'],
                'nfc_mod' => '62',
                'nfc_serie' => $configFiscal ? $configFiscal->cfg_serie : $data['serie'],
                'nfc_nnf' => 0,
                'nfc_cnf' => str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'nfc_dhemi' => $data['dataEmissao'] . ' ' . date('H:i:s'),
                'nfc_tp_emis' => 1,
                'nfc_n_site_autoriz' => 0,
                'nfc_c_mun_fg' => $emit['enderEmit']['cMun'],
                'nfc_fin_nfcom' => 0,
                'nfc_tp_fat' => 0,
                'nfc_ver_proc' => '1.0.0',
                'nfc_cnpj_emit' => $emit['cnpj'],
                'nfc_ie_emit' => $emit['ie'],
                'nfc_crt_emit' => $emit['CRT'],
                'nfc_x_nome_emit' => $emit['xNome'],
                'nfc_x_fant_emit' => $emit['xNome'], // Assumindo nome fantasia = nome
                'nfc_x_lgr_emit' => $emit['enderEmit']['xLgr'],
                'nfc_nro_emit' => $emit['enderEmit']['nro'],
                'nfc_x_cpl_emit' => $emit['enderEmit']['xCpl'],
                'nfc_x_bairro_emit' => $emit['enderEmit']['xBairro'],
                'nfc_c_mun_emit' => $emit['enderEmit']['cMun'],
                'nfc_x_mun_emit' => $emit['enderEmit']['xMun'],
                'nfc_cep_emit' => $emit['enderEmit']['cep'],
                'nfc_uf_emit' => $emit['enderEmit']['uf'],
                'nfc_fone_emit' => $emit['enderEmit']['fone'],
                'nfc_x_nome_dest' => $data['nomeCliente'],
                'nfc_cnpj_dest' => $cnpjSemMascara,
                'nfc_ind_ie_dest' => 9,
                'nfc_x_lgr_dest' => $data['logradouroCliente'],
                'nfc_nro_dest' => $data['numeroCliente'],
                'nfc_x_bairro_dest' => $data['bairroCliente'],
                'nfc_c_mun_dest' => $data['codMunCliente'],
                'nfc_x_mun_dest' => $data['municipioCliente'],
                'nfc_cep_dest' => $data['cepCliente'],
                'nfc_uf_dest' => $data['ufCliente'],
                'nfc_i_cod_assinante' => !empty($data['iCodAssinante']) ? $data['iCodAssinante'] : $cnpjSemMascara,
                'nfc_tp_assinante' => $data['tpAssinante'],
                'nfc_tp_serv_util' => $data['tpServUtil'],
                'nfc_n_contrato' => $data['numeroContrato'],
                'nfc_d_contrato_ini' => $data['dataContratoIni'],
                'nfc_d_contrato_fim' => !empty($data['dataContratoFim']) ? $data['dataContratoFim'] : null,
                'nfc_v_prod' => $valorLiquido,
                // ICMS e FCP (novos campos)
                'nfc_v_bc_icms' => $totalBaseIcms,
                'nfc_v_icms' => $totalIcms,
                'nfc_v_icms_deson' => 0.00, // Valor do ICMS Desonerado (preencher se necessário)
                'nfc_v_fcp' => $totalFcp,
                // PIS e COFINS
                'nfc_v_cofins' => $cofins,
                'nfc_v_pis' => $pis,
                // FUST e FUNTTEL
                'nfc_v_fust' => 0.00,
                'nfc_v_funtel' => 0.00,
                // Retenções
                'nfc_v_ret_pis' => 0.00,
                'nfc_v_ret_cofins' => 0.00,
                'nfc_v_ret_csll' => 0.00,
                'nfc_v_irrf' => $irrf,
                'nfc_v_ret_trib_tot' => $irrf,
                // Descontos e outros
                'nfc_v_desc' => 0.00,
                'nfc_v_outro' => 0.00,
                'nfc_v_nf' => $valorNF,
                // Competência baseada no período fim (formato YYYYMM)
                // Exemplo: se período fim for 31-12-2025, competência será 202512
                'nfc_compet_fat' => !empty($data['dataPeriodoFim']) ? date('Ym', strtotime($data['dataPeriodoFim'])) : (!empty($data['dataContratoIni']) ? date('Ym', strtotime($data['dataContratoIni'])) : date('Ym', strtotime($data['dataEmissao']))),
                'nfc_d_venc_fat' => $data['dataVencimento'],
                // PERÍODO DE USO: RESPEITAR EXATAMENTE O VALOR INFORMADO PELO USUÁRIO
                // NÃO recalcular baseado em outras datas - usar exatamente o que o usuário informou
                'nfc_d_per_uso_ini' => $data['dataPeriodoIni'],
                'nfc_d_per_uso_fim' => $data['dataPeriodoFim'],
                'nfc_cod_barras' => '1',
                'nfc_status' => 1, // Salvo
                'cln_id' => $data['clientes_id'],
                'opc_id' => $this->input->post('opc_id'),
                'nfc_chave_pix' => $this->input->post('nfc_chave_pix'),
                'nfc_linha_digitavel' => $this->input->post('nfc_linha_digitavel')
                ];

                // Coletar mensagens fiscais dos itens antes de construir informações complementares
                $mensagensFiscais = [];
                foreach ($servicos as $servico) {
                    if (!empty($servico['clf_id'])) {
                        // Buscar mensagem fiscal da classificação
                        $this->db->select('clf_mensagem');
                        $this->db->from('classificacao_fiscal');
                        $this->db->where('clf_id', $servico['clf_id']);
                        $this->db->where('ten_id', $this->session->userdata('ten_id'));
                        $clfQuery = $this->db->get();
                        if ($clfQuery->num_rows() > 0) {
                            $clf = $clfQuery->row();
                            if (!empty($clf->clf_mensagem)) {
                                // Evitar duplicatas
                                if (!in_array($clf->clf_mensagem, $mensagensFiscais)) {
                                    $mensagensFiscais[] = $clf->clf_mensagem;
                                }
                            }
                        }
                    }
                }

                // Construir informações complementares com mensagens fiscais
                $nfecomData['nfc_inf_cpl'] = $this->buildInfoComplementar($data, $valorBruto, $comissaoAgencia, $valorLiquido, $mensagensFiscais);

                // Calcular CDV e Chave
                $nfecomData['nfc_cdv'] = $this->calculateDV($nfecomData);
                $nfecomData['nfc_ch_nfcom'] = $this->generateChave($nfecomData);
                
                // Adicionar ten_id obrigatório para foreign key
                $tenId = $this->session->userdata('ten_id');
                if (empty($tenId) || $tenId == 0) {
                    // Tentar buscar o primeiro tenant disponível
                    $this->db->select('ten_id');
                    $this->db->from('tenants');
                    $this->db->limit(1);
                    $tenant = $this->db->get()->row();
                    
                    if ($tenant) {
                        $tenId = $tenant->ten_id;
                        // Atualizar sessão com o tenant encontrado
                        $this->session->set_userdata('ten_id', $tenId);
                        log_message('info', 'Tenant ID não encontrado na sessão. Usando tenant padrão: ' . $tenId);
                    } else {
                        $this->session->set_flashdata('error', 'Erro: Nenhum tenant encontrado no sistema. Execute o script criar_usuario_super_e_tenant.php primeiro.');
                        redirect(site_url('nfecom/adicionar'));
                    }
                }
                $nfecomData['ten_id'] = (int)$tenId;

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
                            $this->db->select('pro_descricao as descricao');
                            $this->db->from('produtos');
                            $this->db->where($produtos_primary_key, $servico['id']);
                            $this->db->where('produtos.ten_id', $this->session->userdata('ten_id'));
                            $servico_query = $this->db->get();
                            $servico_info = $servico_query->row();
                            $nomeServicoItem = $servico_info ? $servico_info->descricao : 'Serviço não encontrado';
                        }

                        // Calcular tributação específica para este item usando a API
                        // SEMPRE calcular pela API (sem valores fixos)
                        $pisItem = 0;
                        $cofinsItem = 0;
                        $cstPis = null;
                        $cstCofins = null;
                        $aliqPis = 0;
                        $aliqCofins = 0;
                        $basePis = 0;
                        $baseCofins = 0;
                        
                        // Inicializar variáveis de ICMS, ICMS ST e FCP
                        $baseIcms = 0;
                        $aliqIcms = 0;
                        $valorIcms = 0;
                        $baseIcmsSt = 0;
                        $aliqIcmsSt = 0;
                        $valorIcmsSt = 0;
                        $mva = 0;
                        $percentualReducaoSt = 0;
                        $baseFcp = 0;
                        $aliqFcp = 0;
                        $valorFcp = 0;
                        $csosn = null;
                        
                        if ($operacaoId && !empty($data['clientes_id'])) {
                            // Calcular tributação usando a API (com endereço selecionado)
                            $tributacao = $this->calcularTributacao(
                                $servico['id'],
                                $data['clientes_id'],
                                $operacaoId,
                                $valorUnitario,
                                $quantidade,
                                'saida',
                                $enderecoId // Passar endereço selecionado
                            );
                            
                            if ($tributacao) {
                                // Usar valores da API - SEM valores fixos como fallback
                                $pisItem = floatval($tributacao['impostos_federais']['pis']['valor'] ?? 0);
                                $cofinsItem = floatval($tributacao['impostos_federais']['cofins']['valor'] ?? 0);
                                $cstPis = $tributacao['impostos_federais']['pis']['cst'] ?? null;
                                $cstCofins = $tributacao['impostos_federais']['cofins']['cst'] ?? null;
                                $aliqPis = floatval($tributacao['impostos_federais']['pis']['aliquota'] ?? 0);
                                $aliqCofins = floatval($tributacao['impostos_federais']['cofins']['aliquota'] ?? 0);
                                
                                // Base de cálculo deve ser o valor do produto (valorProduto), não o valor retornado pela API se estiver incorreto
                                $basePisApi = floatval($tributacao['impostos_federais']['pis']['base_calculo'] ?? 0);
                                $baseCofinsApi = floatval($tributacao['impostos_federais']['cofins']['base_calculo'] ?? 0);
                                
                                // Validar se a base da API está correta (deve ser igual ao valorProduto para CST 01/02)
                                // Se a base da API for muito diferente do produto, usar o produto
                                if ($basePisApi > 0 && abs($basePisApi - $valorProduto) > ($valorProduto * 0.1)) {
                                    // Base da API está muito diferente (mais de 10% de diferença)
                                    // Usar o valor do produto como base correta
                                    log_message('debug', "Base PIS corrigida: API retornou $basePisApi, usando valorProduto $valorProduto");
                                    $basePis = $valorProduto;
                                } else {
                                    $basePis = $basePisApi > 0 ? $basePisApi : $valorProduto;
                                }
                                
                                if ($baseCofinsApi > 0 && abs($baseCofinsApi - $valorProduto) > ($valorProduto * 0.1)) {
                                    // Base da API está muito diferente (mais de 10% de diferença)
                                    // Usar o valor do produto como base correta
                                    log_message('debug', "Base COFINS corrigida: API retornou $baseCofinsApi, usando valorProduto $valorProduto");
                                    $baseCofins = $valorProduto;
                                } else {
                                    $baseCofins = $baseCofinsApi > 0 ? $baseCofinsApi : $valorProduto;
                                }
                                
                                // Extrair dados de ICMS, ICMS ST e FCP da API
                                $impostosEstaduais = $tributacao['impostos_estaduais'] ?? [];
                                $icms = $impostosEstaduais['icms'] ?? [];
                                $icmsSt = $impostosEstaduais['icms_st'] ?? [];
                                $fcp = $impostosEstaduais['fcp'] ?? [];
                                
                                // ICMS básico
                                $baseIcms = floatval($icms['base_calculo'] ?? 0);
                                $aliqIcms = floatval($icms['aliquota'] ?? 0);
                                $valorIcms = floatval($icms['valor'] ?? 0);
                                
                                // ICMS ST
                                $baseIcmsSt = floatval($icmsSt['base_calculo'] ?? 0);
                                $aliqIcmsSt = floatval($icmsSt['aliquota'] ?? 0);
                                $valorIcmsSt = floatval($icmsSt['valor'] ?? 0);
                                $mva = floatval($icmsSt['mva'] ?? 0);
                                $percentualReducaoSt = floatval($icmsSt['percentual_reducao'] ?? 0);
                                
                                // FCP
                                $baseFcp = floatval($fcp['base_calculo'] ?? 0);
                                $aliqFcp = floatval($fcp['aliquota'] ?? 0);
                                $valorFcp = floatval($fcp['valor'] ?? 0);
                                
                                // CSOSN (se disponível na classificação fiscal)
                                $csosn = $tributacao['classificacao_fiscal']['csosn'] ?? null;
                                
                                log_message('debug', "Item #$itemNumero - Valor Produto: $valorProduto | Base PIS: $basePis | Base COFINS: $baseCofins | ICMS: base=$baseIcms, aliq=$aliqIcms%, valor=$valorIcms | ICMS ST: base=$baseIcmsSt, valor=$valorIcmsSt | FCP: base=$baseFcp, valor=$valorFcp");
                            } else {
                                // Se não houver tributação, inicializar valores com zero
                                $baseIcms = 0;
                                $aliqIcms = 0;
                                $valorIcms = 0;
                                $baseIcmsSt = 0;
                                $aliqIcmsSt = 0;
                                $valorIcmsSt = 0;
                                $mva = 0;
                                $percentualReducaoSt = 0;
                                $baseFcp = 0;
                                $aliqFcp = 0;
                                $valorFcp = 0;
                                $csosn = null;
                            }
                        }
                        
                        // Calcular IRRF proporcional por item: 4,8% sobre o valor do item — somente se o cliente tiver "Cobrar IRRF" ativo
                        $irrfItem = (!empty($data['cobrar_irrf'])) ? round(($valorProduto * 4.8) / 100, 2) : 0;

                        // Garantir que CST PIS e COFINS não sejam null (valor padrão '01' conforme estrutura da tabela)
                        if (empty($cstPis) || $cstPis === null) {
                            $cstPis = '01'; // Valor padrão conforme estrutura da tabela
                            log_message('info', "CST PIS estava null, usando valor padrão '01' para item #$itemNumero");
                        }
                        
                        if (empty($cstCofins) || $cstCofins === null) {
                            $cstCofins = '01'; // Valor padrão conforme estrutura da tabela
                            log_message('info', "CST COFINS estava null, usando valor padrão '01' para item #$itemNumero");
                        }

                        // Validar e corrigir bases de cálculo antes de salvar
                        // Para CST 01/02 (tributável), a base deve ser o valor do produto
                        // Se a base estiver muito diferente, usar o valor do produto
                        if (in_array($cstPis, ['01', '02'])) {
                            // CST tributável: base deve ser igual ao valor do produto
                            if (abs($basePis - $valorProduto) > ($valorProduto * 0.05)) {
                                // Diferença maior que 5% - usar valor do produto
                                log_message('info', "Base PIS corrigida antes de salvar: $basePis → $valorProduto (CST: $cstPis)");
                                $basePis = $valorProduto;
                            }
                        } else {
                            // CST isento/não tributado: base deve ser 0
                            $basePis = 0;
                        }
                        
                        if (in_array($cstCofins, ['01', '02'])) {
                            // CST tributável: base deve ser igual ao valor do produto
                            if (abs($baseCofins - $valorProduto) > ($valorProduto * 0.05)) {
                                // Diferença maior que 5% - usar valor do produto
                                log_message('info', "Base COFINS corrigida antes de salvar: $baseCofins → $valorProduto (CST: $cstCofins)");
                                $baseCofins = $valorProduto;
                            }
                        } else {
                            // CST isento/não tributado: base deve ser 0
                            $baseCofins = 0;
                        }
                        
                        // Validar e corrigir valores de ICMS para CST isento/não tributado
                        // CST 40, 41, 50, 51, 60: isento/não tributado (sem base/valor/alíquota)
                        $cstsIsentos = ['40', '41', '50', '51', '60'];
                        if (in_array($cstIcms, $cstsIsentos)) {
                            // CST isento/não tributado: zerar base, alíquota e valor
                            $baseIcms = 0;
                            $aliqIcms = 0;
                            $valorIcms = 0;
                            log_message('info', "CST ICMS $cstIcms detectado - zerando base, alíquota e valor de ICMS para item #$itemNumero");
                        }
                        
                        $itemData = [
                            'nfc_id' => $idNfecom,
                            'nfi_n_item' => $itemNumero,
                            'nfi_c_prod' => $servico['id'],
                            'nfi_x_prod' => $nomeServicoItem,
                            'nfi_c_class' => $cClass,
                            'nfi_cfop' => $cfop,
                            'nfi_u_med' => $unidade,
                            'nfi_q_faturada' => $quantidade,
                            'nfi_v_item' => $valorItem,
                            'nfi_v_desc' => $valorDesconto,
                            'nfi_v_outro' => $valorOutros,
                            'nfi_v_prod' => $valorProduto,
                            'nfi_cst_icms' => $cstIcms,
                            // ICMS básico
                            'nfi_v_bc_icms' => $baseIcms ?? 0,
                            'nfi_p_icms' => $aliqIcms ?? 0,
                            'nfi_v_icms' => $valorIcms ?? 0,
                            'nfi_v_icms_deson' => 0.00, // Valor do ICMS Desonerado (preencher se necessário)
                            'nfi_mot_des_icms' => null, // Motivo da Desoneração (preencher se necessário)
                            // ICMS ST
                            'nfi_v_bc_icms_st' => $baseIcmsSt ?? 0,
                            'nfi_p_icms_st' => $aliqIcmsSt ?? 0,
                            'nfi_v_icms_st' => $valorIcmsSt ?? 0,
                            'nfi_v_bc_st_ret' => 0.00, // Base de Cálculo do ST Retido (preencher se necessário)
                            'nfi_v_icms_st_ret' => 0.00, // Valor do ICMS ST Retido (preencher se necessário)
                            'nfi_p_st' => $percentualReducaoSt > 0 ? $aliqIcmsSt : 0, // Alíquota do ST
                            'nfi_v_icms_subst' => 0.00, // Valor do ICMS Próprio do Substituto (preencher se necessário)
                            // FCP
                            'nfi_v_bc_fcp' => $baseFcp ?? 0,
                            'nfi_p_fcp' => $aliqFcp ?? 0,
                            'nfi_v_fcp' => $valorFcp ?? 0,
                            'nfi_v_fcp_st' => 0.00, // Valor do FCP ST (preencher se necessário)
                            'nfi_v_fcp_st_ret' => 0.00, // Valor do FCP ST Retido (preencher se necessário)
                            // CSOSN
                            'nfi_csosn' => $csosn ?? null,
                            // PIS
                            'nfi_cst_pis' => $cstPis,
                            'nfi_v_bc_pis' => $basePis,
                            'nfi_p_pis' => $aliqPis,
                            'nfi_v_pis' => $pisItem,
                            // COFINS
                            'nfi_cst_cofins' => $cstCofins,
                            'nfi_v_bc_cofins' => $baseCofins,
                            'nfi_p_cofins' => $aliqCofins,
                            'nfi_v_cofins' => $cofinsItem,
                            // FUST e FUNTTEL
                            'nfi_v_bc_fust' => 0.00,
                            'nfi_p_fust' => 0.00,
                            'nfi_v_fust' => 0.00,
                            'nfi_v_bc_funtel' => 0.00,
                            'nfi_p_funtel' => 0.00,
                            'nfi_v_funtel' => 0.00,
                            // IRRF (base e valor zerados se cliente não cobrar IRRF)
                            'nfi_v_bc_irrf' => !empty($data['cobrar_irrf']) ? $valorProduto : 0,
                            'nfi_v_irrf' => $irrfItem,
                            'nfi_data_cadastro' => date('Y-m-d H:i:s'),
                            'nfi_data_atualizacao' => date('Y-m-d H:i:s')
                        ];

                        // Adicionar clf_id se existir o campo na tabela e se tiver valor
                        if ($clfId) {
                            // Verificar se o campo existe antes de adicionar
                            $fields = $this->db->list_fields('nfecom_itens');
                            if (in_array('clf_id', $fields) || in_array('clf_id', $fields)) {
                                $itemData['clf_id'] = $clfId;
                                log_message('info', 'Item NFCom #' . $itemNumero . ' - clf_id adicionado: ' . $clfId);
                            } else {
                                log_message('info', 'Campo clf_id não existe na tabela nfecom_itens. Não foi possível salvar o ID da classificação fiscal.');
                            }
                        } else {
                            log_message('info', 'Item NFCom #' . $itemNumero . ' - clf_id não fornecido no serviço');
                        }
                        
                        // Adicionar ten_id obrigatório para foreign key (se a tabela tiver esse campo)
                        $tenId = $this->session->userdata('ten_id');
                        $fields = $this->db->list_fields('nfecom_itens');
                        if (in_array('ten_id', $fields)) {
                            // Sempre adicionar ten_id, mesmo que seja 0 (será corrigido depois)
                            $itemData['ten_id'] = !empty($tenId) ? (int)$tenId : 1;
                        }

                        $this->Nfecom_model->add('nfecom_itens', $itemData);
                        log_message('info', 'Item NFCom #' . $itemNumero . ' salvo com sucesso. nfi_id: ' . $this->db->insert_id());
                        
                        // Acumular totais de ICMS, ICMS ST, FCP e IRRF
                        $totalIcms += $valorIcms ?? 0;
                        $totalIcmsSt += $valorIcmsSt ?? 0;
                        $totalFcp += $valorFcp ?? 0;
                        $totalBaseIcms += $baseIcms ?? 0;
                        $totalIrrf += $irrfItem; // Acumular IRRF de cada item (já arredondado)
                        
                        $itemNumero++;
                    }
                }

                // Se não há serviços válidos, não permitir criar item genérico sem tributação
                if ($itemNumero == 1) {
                    // Não permitir salvar sem serviços válidos
                    $this->session->set_flashdata('error', 'Não é possível salvar NFeCom sem serviços válidos com tributação calculada.');
                    redirect(site_url('nfecom/adicionar'));
                }

                // Arredondar o total do IRRF para 2 casas decimais
                $totalIrrf = round($totalIrrf, 2);
                
                // Atualizar o total do IRRF na capa com a soma dos IRRF dos itens
                // Conforme rejeição 685: Total do IRRF retido deve ser igual ao somatório dos itens
                $this->Nfecom_model->edit('nfecom_capa', ['nfc_v_irrf' => $totalIrrf], 'nfc_id', $idNfecom);
                log_message('info', 'Total do IRRF atualizado na capa: R$ ' . number_format($totalIrrf, 2, ',', '.') . ' (soma dos IRRF dos itens, arredondado)');
                
                // Recalcular o vNF usando o total do IRRF atualizado (soma dos itens)
                // Regra G137: vNF = vProd + vOutro - vDesc - vRetPIS - vRetCofins - vRetCSLL - vIRRF
                $vNfRecalculado = $valorLiquido - $totalIrrf; // Usar totalIrrf (soma dos itens) em vez de $irrf
                $vNfRecalculado = round($vNfRecalculado, 2); // Arredondar vNF também
                $this->Nfecom_model->edit('nfecom_capa', ['nfc_v_nf' => $vNfRecalculado], 'nfc_id', $idNfecom);
                log_message('info', 'Valor da NF recalculado: R$ ' . number_format($valorLiquido, 2, ',', '.') . ' - R$ ' . number_format($totalIrrf, 2, ',', '.') . ' (IRRF soma itens) = R$ ' . number_format($vNfRecalculado, 2, ',', '.'));

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
        if ($nfecom->nfc_status != 4) {
            $this->session->set_flashdata('error', 'Apenas NFCom rejeitadas podem ser editadas.');
            redirect(site_url('nfecom/visualizar/' . $id));
        }

        $this->db->trans_begin();

        try {
            // Carregar Operações Comerciais para a tela de edição
            $this->data['operacoes'] = $this->OperacaoComercial_model->getAll();

            // Atualizar dados básicos da NFCom (NÃO alterar o número da NFE)
            // Processar data de vencimento (campo pode vir como dataVencimento ou nfc_d_venc_fat)
            $dataVencimento = $this->input->post('dataVencimento') ?: $this->input->post('nfc_d_venc_fat');
            if (!empty($dataVencimento)) {
                // Verificar se já está no formato ISO (yyyy-mm-dd)
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataVencimento)) {
                    // Tentar converter de formato brasileiro (dd/mm/yyyy)
                    $dataVenc = explode('/', $dataVencimento);
                    if (count($dataVenc) == 3) {
                        $dataVencimento = $dataVenc[2] . '-' . $dataVenc[1] . '-' . $dataVenc[0];
                    }
                }
            }
            
            $dados = [
                'nfc_serie' => $this->input->post('nfc_serie'),
                'nfc_n_contrato' => $this->input->post('nfc_n_contrato'),
                'nfc_d_contrato_ini' => $this->input->post('nfc_d_contrato_ini'),
                // Competência baseada no período fim (formato YYYYMM)
                // Exemplo: se período fim for 31-12-2025, competência será 202512
                'nfc_compet_fat' => !empty($this->input->post('nfc_d_per_uso_fim')) ? date('Ym', strtotime($this->input->post('nfc_d_per_uso_fim'))) : (!empty($this->input->post('nfc_d_contrato_ini')) ? date('Ym', strtotime($this->input->post('nfc_d_contrato_ini'))) : ($this->input->post('nfc_compet_fat') ?: date('Ym'))),
                'nfc_d_venc_fat' => $dataVencimento,
                // PERÍODO DE USO: RESPEITAR EXATAMENTE O VALOR INFORMADO PELO USUÁRIO
                'nfc_d_per_uso_ini' => $this->input->post('nfc_d_per_uso_ini'),
                'nfc_d_per_uso_fim' => $this->input->post('nfc_d_per_uso_fim'),
                'nfc_inf_cpl' => str_replace(["\r\n", "\r", "\n"], '; ', $this->input->post('nfc_inf_cpl')),
                'nfc_i_cod_assinante' => $this->input->post('nfc_i_cod_assinante'),
                'nfc_tp_assinante' => $this->input->post('nfc_tp_assinante'),
                'nfc_tp_serv_util' => $this->input->post('nfc_tp_serv_util'),
                'nfc_d_contrato_fim' => !empty($this->input->post('nfc_d_contrato_fim')) ? $this->input->post('nfc_d_contrato_fim') : null,
                'nfc_chave_pix' => $this->input->post('nfc_chave_pix'),
                'nfc_linha_digitavel' => $this->input->post('nfc_linha_digitavel'),
                'opc_id' => $this->input->post('opc_id'),
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
                        // Garantir valores padrão para campos obrigatórios
                        $cstPis = !empty($item['cst_pis']) ? $item['cst_pis'] : '01';
                        $cstCofins = !empty($item['cst_cofins']) ? $item['cst_cofins'] : '01';
                        
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
                            // Campos obrigatórios PIS e COFINS com valores padrão
                            'nfi_cst_pis' => $cstPis,
                            'nfi_v_bc_pis' => str_replace(',', '.', $item['v_bc_pis'] ?? '0.00'),
                            'nfi_p_pis' => str_replace(',', '.', $item['p_pis'] ?? '0.00'),
                            'nfi_v_pis' => str_replace(',', '.', $item['v_pis'] ?? '0.00'),
                            'nfi_cst_cofins' => $cstCofins,
                            'nfi_v_bc_cofins' => str_replace(',', '.', $item['v_bc_cofins'] ?? '0.00'),
                            'nfi_p_cofins' => str_replace(',', '.', $item['p_cofins'] ?? '0.00'),
                            'nfi_v_cofins' => str_replace(',', '.', $item['v_cofins'] ?? '0.00'),
                        ];

                        // Calcular valor total
                        $itemData['nfi_v_prod'] = $itemData['nfi_q_faturada'] * $itemData['nfi_v_item'];
                        
                        // Adicionar ten_id se o campo existir na tabela
                        $tenId = $this->session->userdata('ten_id');
                        $fields = $this->db->list_fields('nfecom_itens');
                        if (in_array('ten_id', $fields)) {
                            $itemData['ten_id'] = !empty($tenId) ? (int)$tenId : 1;
                        }

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
        
        // Para requisições AJAX, definir header JSON desde o início e limpar output
        if ($isAjax) {
            // Limpar qualquer output anterior
            if (ob_get_length()) {
                ob_clean();
            }
            header('Content-Type: application/json; charset=utf-8');
        }

        try {
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
                if ($isAjax) {
                    $response = ['success' => false, 'message' => 'Você não tem permissão para emitir NFECom.'];
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    return;
                } else {
                    $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFECom.');
                    redirect(base_url());
                }
            }

            $id = $this->input->post('id') ?? $this->uri->segment(3);
            
            if (empty($id)) {
                throw new Exception('ID da NFCom não informado.');
            }
            
            $nfecom = $this->Nfecom_model->getById($id);

            if ($nfecom == null) {
                if ($isAjax) {
                    $response = ['success' => false, 'message' => 'NFECom não encontrada.'];
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    return;
                } else {
                    $this->session->set_flashdata('error', 'NFECom não encontrada.');
                    redirect(site_url('nfecom'));
                }
            }

            // Se ainda não foi autorizada, fazer a autorização automática
            // Inclui reemissões (status 4)
            if ($nfecom->nfc_status < 3 || $nfecom->nfc_status == 4) {
                $isReemissao = ($nfecom->nfc_status == 4);
                log_message('info', 'NFCom ' . ($isReemissao ? 'Reemissão' : 'Emissão') . ' iniciada - ID: ' . $id . ', Status: ' . $nfecom->nfc_status);
                
                // Atualizar dados fiscais e gerar XML se necessário
                $configFiscal = $this->getConfiguracaoNfcom();
                if ($configFiscal) {
                    $ufEmit = $nfecom->nfc_uf_emit ?? 'GO';
                    $codigoUf = $this->get_cUF($ufEmit);

                    // Para NFCom rejeitadas (reemissão), manter o número, chave e data originais
                    // Para NFCom novas, apenas pegar número atual (incremento será feito no final do processo)
                    if ($nfecom->nfc_status < 2) {
                        $numeroNota = $configFiscal->cfg_numero_atual;
                        
                        $atualizacao = [
                            'nfc_tipo_ambiente' => $configFiscal->cfg_ambiente,
                            'nfc_serie' => $configFiscal->cfg_serie,
                            'nfc_nnf' => $numeroNota,
                            'nfc_cuf' => $codigoUf,
                            'nfc_dhemi' => date('Y-m-d H:i:s'), // Usar data atual na emissão
                        ];

                        // Calcular nova chave para NFCom nova
                        $chaveData = [
                            'nfc_cuf' => $atualizacao['nfc_cuf'],
                            'nfc_dhemi' => $atualizacao['nfc_dhemi'],
                            'nfc_cnpj_emit' => $nfecom->nfc_cnpj_emit,
                            'nfc_mod' => $nfecom->nfc_mod,
                            'nfc_serie' => $atualizacao['nfc_serie'],
                            'nfc_nnf' => $atualizacao['nfc_nnf'],
                            'nfc_tp_emis' => $nfecom->nfc_tp_emis,
                            'nfc_cnf' => $nfecom->nfc_cnf,
                            'nfc_n_site_autoriz' => 0,
                        ];

                        $atualizacao['nfc_cdv'] = $this->calculateDV($chaveData);
                        $chaveData['nfc_cdv'] = $atualizacao['nfc_cdv'];
                        $atualizacao['nfc_ch_nfcom'] = $this->generateChave($chaveData);
                    } else {
                        // NFCom rejeitada (reemissão) - manter número, chave, série e data originais
                        $atualizacao = [
                            'nfc_tipo_ambiente' => $configFiscal->cfg_ambiente,
                            // Manter série original (não atualizar)
                            // Manter número original (não atualizar)
                            // Manter chave original (não atualizar)
                            // Manter data de emissão original (não atualizar)
                            // Limpar apenas os campos de rejeição
                            'nfc_x_motivo' => null,
                            'nfc_c_stat' => null,
                            'nfc_n_prot' => null,
                            'nfc_dh_recbto' => null,
                            'nfc_xml' => null,
                        ];
                        // Não recalcular chave, manter a original
                    }

                    $this->Nfecom_model->edit('nfecom_capa', $atualizacao, 'nfc_id', $id);
                    $nfecom = $this->Nfecom_model->getById($id);
                }

                // Chamar o método de autorização (pode lançar exceções)
                $resultadoAutorizacao = $this->autorizar($id, false);
                
                if ($resultadoAutorizacao === false) {
                    throw new Exception('Falha na autorização da NFCom. Verifique os logs para mais detalhes.');
                }

                // Recarregar dados após autorização
                $nfecom = $this->Nfecom_model->getById($id);
            }

            // Preparar dados para o modal
            // Recarregar dados atualizados após autorização
            $nfecom = $this->Nfecom_model->getById($id);
            
            if (!$nfecom) {
                throw new Exception('NFECom não encontrada após autorização.');
            }
            
            // Garantir que o status seja um número inteiro
            $statusAtual = (int) $nfecom->nfc_status;
            
            // Determinar status baseado no status atual e cStat da SEFAZ
            $statusDescricao = $this->getStatusDescricao($statusAtual);
            
            // Se foi autorizado (status 3), garantir que mostra "Autorizado"
            if ($statusAtual == 3) {
                $statusDescricao = 'Autorizado';
            } elseif ($statusAtual == 4 && !empty($nfecom->nfc_c_stat)) {
                // Para notas rejeitadas, incluir o cStat da SEFAZ
                $statusDescricao = 'Rejeitada (cStat: ' . $nfecom->nfc_c_stat . ')';
            } elseif ($statusAtual == 7) {
                $statusDescricao = 'Cancelada';
            }

            $modalData = [
                'numero_nfcom' => $nfecom->nfc_nnf ?? '',
                'chave_nfcom' => $nfecom->nfc_ch_nfcom ?? '',
                'status' => $statusDescricao,
                'cstat' => $nfecom->nfc_c_stat ?? '',
                'motivo' => $nfecom->nfc_x_motivo ?? 'NFCom processada com sucesso',
                'protocolo' => $nfecom->nfc_n_prot ?? '',
                'id' => $id
            ];

            // Se foi autorizado, incluir informações adicionais
            if ($statusAtual == 3) {
                $modalData['motivo'] = $nfecom->nfc_x_motivo ?? 'Autorizado o uso da NFCom';
                // Garantir que o status seja "Autorizado"
                $modalData['status'] = 'Autorizado';
                
                // Montar retorno detalhado em JSON quando autorizado
                $retornoSefaz = [
                    'cStat' => $nfecom->nfc_c_stat ?? '100',
                    'xMotivo' => $nfecom->nfc_x_motivo ?? 'Autorizado o uso da NFCom',
                    'nProt' => $nfecom->nfc_n_prot ?? '',
                    'dhRecbto' => $nfecom->nfc_dh_recbto ?? '',
                    'xml' => $nfecom->nfc_xml ?? ''
                ];
                $modalData['retorno'] = json_encode($retornoSefaz, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } elseif ($statusAtual == 4) {
                // Para rejeitadas, também montar retorno detalhado
                $retornoSefaz = [
                    'cStat' => $nfecom->nfc_c_stat ?? '',
                    'xMotivo' => $nfecom->nfc_x_motivo ?? 'Rejeitada',
                    'nProt' => '',
                    'dhRecbto' => '',
                    'xml' => ''
                ];
                $modalData['retorno'] = json_encode($retornoSefaz, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            if ($isAjax) {
                $response = ['success' => true, 'modal' => $modalData];
                echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                // Se não for AJAX e estiver autorizada, fazer download do XML autorizado
                if ($statusAtual == 3 && !empty($nfecom->nfc_xml)) {
                    // Limpar qualquer output anterior
                    if (ob_get_length()) {
                        ob_clean();
                    }
                    
                    // Recarregar dados para garantir que temos o XML mais recente
                    $nfecom = $this->Nfecom_model->getById($id);
                    
                    if (empty($nfecom->nfc_xml)) {
                        log_message('error', 'NFCom: Tentativa de download mas nfc_xml está vazio. ID: ' . $id);
                        $this->session->set_flashdata('error', 'XML autorizado não encontrado no banco de dados.');
                        redirect(site_url('nfecom'));
                        return;
                    }
                    
                    // Configurar headers para download do XML autorizado
                    $filename = 'NFCom_' . str_pad($nfecom->nfc_nnf, 9, '0', STR_PAD_LEFT) . '_' . date('YmdHis') . '.xml';
                    
                    // Limpar qualquer output buffer
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    
                    header('Content-Type: application/xml; charset=utf-8');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    header('Content-Length: ' . strlen($nfecom->nfc_xml));
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    
                    // Output do XML autorizado
                    echo $nfecom->nfc_xml;
                    exit;
                } else {
                    // Se não estiver autorizada ou não tiver XML, mostrar modal
                    $this->session->set_flashdata('nfecom_modal', $modalData);
                    redirect(site_url('nfecom'));
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Erro em gerarXml NFCom: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            
            if ($isAjax) {
                // Limpar qualquer output anterior
                if (ob_get_length()) {
                    ob_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                $response = ['success' => false, 'message' => 'Erro ao processar NFCom: ' . $e->getMessage()];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            } else {
                $this->session->set_flashdata('error', 'Erro ao processar NFCom: ' . $e->getMessage());
                redirect(site_url('nfecom'));
            }
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
            if (empty($nfecom->nfc_nnf) || $nfecom->nfc_nnf == 0) {
                $configFiscal = $this->getConfiguracaoNfcom();
                $numeroNota = $configFiscal ? $configFiscal->cfg_numero_atual : '000000000';
            } else {
                $numeroNota = $nfecom->nfc_nnf;
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
        if ($nfecom->nfc_status == 3) {
            $this->session->set_flashdata('error', 'Não é possível excluir NFCom autorizada.');
            redirect(site_url('nfecom'));
        }
        if ($nfecom->nfc_status == 7) {
            $this->session->set_flashdata('error', 'Não é possível excluir NFCom cancelada.');
            redirect(site_url('nfecom'));
        }

        // Excluir itens primeiro
        $this->Nfecom_model->delete('nfecom_itens', 'nfc_id', $id);

        // Excluir protocolos
        $this->Nfecom_model->delete('protocolos', 'nfc_id', $id);

        // Excluir NFCom
        if ($this->Nfecom_model->delete('nfecom_capa', 'nfc_id', $id)) {
            $this->session->set_flashdata('success', 'NFECom excluída com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir NFECom.');
        }

        redirect(site_url('nfecom'));
    }

    private function getStatusDescricao($status)
    {
        // Garantir que o status seja um número inteiro
        $status = (int) $status;
        
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

    private function montarXmlAutorizado($xmlSigned, $xmlRetorno)
    {
        try {
            // Carregar XML assinado
            $domNFCom = new DOMDocument();
            $domNFCom->preserveWhiteSpace = false;
            $domNFCom->formatOutput = true;
            
            // Remover namespaces para facilitar parsing
            $xmlSignedClean = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$3', $xmlSigned);
            @$domNFCom->loadXML($xmlSignedClean);
            
            // Extrair o elemento NFCom do XML assinado
            $nfcomNode = $domNFCom->getElementsByTagName('NFCom')->item(0);
            if (!$nfcomNode) {
                log_message('error', 'NFCom: Elemento NFCom não encontrado no XML assinado');
                return $xmlSigned;
            }
            
            // Carregar XML de retorno da SEFAZ
            $domRetorno = new DOMDocument();
            $domRetorno->preserveWhiteSpace = false;
            $domRetorno->formatOutput = true;
            
            // Remover namespaces do retorno
            $xmlRetornoClean = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$3', $xmlRetorno);
            @$domRetorno->loadXML($xmlRetornoClean);
            
            // Extrair o protocolo de autorização - tentar várias formas
            $protNFCom = null;
            
            // Tentar 1: protNFCom direto
            $protNFCom = $domRetorno->getElementsByTagName('protNFCom')->item(0);
            
            // Tentar 2: infProt dentro de protNFCom
            if (!$protNFCom) {
                $infProt = $domRetorno->getElementsByTagName('infProt')->item(0);
                if ($infProt) {
                    // Criar protNFCom com infProt dentro
                    $protNFCom = $domRetorno->createElement('protNFCom');
                    $infProtClone = $domRetorno->importNode($infProt, true);
                    $protNFCom->appendChild($infProtClone);
                }
            }
            
            // Tentar 3: usar XPath
            if (!$protNFCom) {
                $xpath = new DOMXPath($domRetorno);
                $xpath->registerNamespace('soap', 'http://www.w3.org/2003/05/soap-envelope');
                $protNFCom = $xpath->query('//protNFCom')->item(0);
                if (!$protNFCom) {
                    $protNFCom = $xpath->query('//infProt')->item(0);
                    if ($protNFCom) {
                        // Criar protNFCom com infProt dentro
                        $protNFComTemp = $domRetorno->createElement('protNFCom');
                        $infProtClone = $domRetorno->importNode($protNFCom, true);
                        $protNFComTemp->appendChild($infProtClone);
                        $protNFCom = $protNFComTemp;
                    }
                }
            }
            
            if (!$protNFCom) {
                log_message('error', 'NFCom: Protocolo não encontrado no XML de retorno');
                return $xmlSigned;
            }
            
            // Criar novo documento para o XML autorizado
            $domAutorizado = new DOMDocument('1.0', 'UTF-8');
            $domAutorizado->preserveWhiteSpace = false;
            $domAutorizado->formatOutput = true;
            
            // Criar elemento raiz nfeProc
            $nfeProc = $domAutorizado->createElement('nfeProc');
            $nfeProc->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/nfcom');
            $nfeProc->setAttribute('versao', '1.00');
            $domAutorizado->appendChild($nfeProc);
            
            // Importar NFCom (com namespaces preservados)
            $nfcomImportado = $domAutorizado->importNode($nfcomNode, true);
            $nfeProc->appendChild($nfcomImportado);
            
            // Importar protocolo
            $protImportado = $domAutorizado->importNode($protNFCom, true);
            $nfeProc->appendChild($protImportado);
            
            $xmlAutorizado = $domAutorizado->saveXML();
            
            log_message('info', 'NFCom: XML autorizado montado com sucesso. Tamanho: ' . strlen($xmlAutorizado) . ' bytes');
            
            return $xmlAutorizado;
            
        } catch (Exception $e) {
            log_message('error', 'Erro ao montar XML autorizado: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            // Em caso de erro, retornar XML assinado original
            return $xmlSigned;
        }
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
        if ($nfecom->nfc_status < 2) {
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
        if ($nfecom->nfc_status < 2 || $nfecom->nfc_status == 4) {
            $configFiscal = $this->getConfiguracaoNfcom();
            if ($configFiscal) {
                $ufEmit = $nfecom->nfc_uf_emit ?? 'GO';
                $codigoUf = $this->get_cUF($ufEmit);

                // Para NFCom rejeitadas (reemissão), manter o número atual
                // Para NFCom novas, apenas pegar número atual (incremento será feito apenas na autorização)
                if ($nfecom->nfc_status < 2) {
                    $numeroNota = $configFiscal->cfg_numero_atual;
                } else {
                    // NFCom rejeitada mantém o número atual
                    $numeroNota = $nfecom->nfc_nnf;
                }

                $atualizacao = [
                    'nfc_tipo_ambiente' => $configFiscal->cfg_ambiente,
                    'nfc_serie' => $configFiscal->cfg_serie,
                    'nfc_nnf' => $numeroNota,
                    'nfc_cuf' => $codigoUf,
                ];

                // Para reemissão (NFCom rejeitada), limpar o motivo antigo
                if ($nfecom->nfc_status == 4) {
                    $atualizacao['nfc_x_motivo'] = null;
                    $atualizacao['nfc_c_stat'] = null;
                    $atualizacao['nfc_n_prot'] = null;
                    $atualizacao['nfc_dh_recbto'] = null;
                    $atualizacao['nfc_xml'] = null;
                }

                $chaveData = [
                    'nfc_cuf' => $atualizacao['nfc_cuf'],
                    'nfc_dhemi' => $nfecom->nfc_dhemi,
                    'nfc_cnpj_emit' => $nfecom->nfc_cnpj_emit,
                    'nfc_mod' => $nfecom->nfc_mod,
                    'nfc_serie' => $atualizacao['nfc_serie'],
                    'nfc_nnf' => $atualizacao['nfc_nnf'],
                    'nfc_tp_emis' => $nfecom->nfc_tp_emis,
                    'nfc_cnf' => $nfecom->nfc_cnf,
                ];

                // nSiteAutoriz é necessário para a chave e DV
                $nSiteAutoriz = 0; // Default
                $chaveData['nfc_n_site_autoriz'] = $nSiteAutoriz;

                $atualizacao['nfc_cdv'] = $this->calculateDV($chaveData);
                $chaveData['nfc_cdv'] = $atualizacao['nfc_cdv'];
                $atualizacao['nfc_ch_nfcom'] = $this->generateChave($chaveData);

                $this->Nfecom_model->edit('nfecom_capa', $atualizacao, 'nfc_id', $id);
                $nfecom = $this->Nfecom_model->getById($id);
            }
        }

        // Validar certificado configurado para NFCOM
        $configFiscal = $this->getConfiguracaoNfcom();
        if (!$configFiscal || empty($configFiscal->cer_arquivo) || empty($configFiscal->cer_senha)) {
            $this->session->set_flashdata('error', 'Nenhum certificado válido configurado para NFCOM.');
            redirect(site_url('nfecom/visualizar/' . $id));
        }

        try {
            $this->load->library('NFComService');
            $nfcomService = new NFComService([
                'ambiente' => $configFiscal->cfg_ambiente,
                'disable_cert_validation' => true
            ]);
            $nfcomService->setCertificate($configFiscal->cer_arquivo, $configFiscal->cer_senha);

            // Consulta Real na SEFAZ
            $retorno = $nfcomService->consult($nfecom->nfc_ch_nfcom, $configFiscal->cfg_ambiente);

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
                    'nfc_status' => 3, // Autorizado
                    'nfc_c_stat' => $cStat,
                    'nfc_x_motivo' => $xMotivo,
                    'nfc_n_prot' => $protocolo,
                    'nfc_dh_recbto' => $dhRecbto
                ];

                $this->Nfecom_model->updateStatus($id, $dadosAtu);
                $this->registrarProtocolo($id, $protocolo, 'AUTORIZACAO', $xMotivo, $dhRecbto);

                // Flashdata removido - não exibir SweetAlert após consulta bem-sucedida
                // $this->session->set_flashdata('success', 'NFCom consultada e Autorizada! Status: ' . $xMotivo);
            } elseif ($cStat == '101') { // Cancelamento homologado
                $statusTexto = 'Cancelada';
                $protocolo = $retorno['protocolo']['nProt'] ?? '';
                $dhRecbto = $retorno['protocolo']['dhRecbto'] ?? '';

                $dadosAtu = [
                    'nfc_status' => 7, // Cancelada
                    'nfc_c_stat' => $cStat,
                    'nfc_x_motivo' => $xMotivo,
                    'nfc_n_prot_canc' => $protocolo
                ];

                $this->Nfecom_model->updateStatus($id, $dadosAtu);
                if ($protocolo) {
                    $this->registrarProtocolo($id, $protocolo, 'CANCELAMENTO', $xMotivo, $dhRecbto);
                }

                $this->session->set_flashdata('info', 'NFCom consultada e identificada como Cancelada! Status: ' . $xMotivo);
            } else {
                // Outros status ou rejeição
                $dadosAtu = [
                    'nfc_c_stat' => $cStat,
                    'nfc_x_motivo' => $xMotivo
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
                'numero_nfcom' => $nfecom->nfc_nnf,
                'chave_nfcom' => $nfecom->nfc_ch_nfcom,
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
                'cnpj' => $emit['cnpj'],
                'ie' => $emit['ie'],
                'logo' => !empty($emit['url_logo']) ? str_replace('/', DIRECTORY_SEPARATOR, FCPATH . $emit['url_logo']) : null,
                'endereco' => [
                    'logradouro' => $emit['enderEmit']['xLgr'],
                    'numero' => $emit['enderEmit']['nro'],
                    'complemento' => $emit['enderEmit']['xCpl'] ?? '',
                    'bairro' => $emit['enderEmit']['xBairro'],
                    'municipio' => $emit['enderEmit']['xMun'],
                    'uf' => $emit['enderEmit']['uf'],
                    'cep' => $emit['enderEmit']['cep']
                ]
            ],
            'debug_logo' => [
                'url_logo_db' => $emit['url_logo'] ?? 'NULL',
                'caminho_completo' => !empty($emit['url_logo']) ? FCPATH . $emit['url_logo'] : 'NULL',
                'arquivo_existe' => !empty($emit['url_logo']) && file_exists(FCPATH . $emit['url_logo']) ? 'SIM' : 'NÃO'
            ],
            'serie' => $nfecom->nfc_serie,
            'numero_inicial' => $nfecom->nfc_nnf,
            'nSiteAutoriz' => $nfecom->nfc_n_site_autoriz,
            'classe' => '0101011',
            'diretorios' => [
                'temp' => FCPATH . 'assets/temp'
            ]
        ];

        // Preparar dados do destinatário
        $destinatario = [
            'nome' => $nfecom->nfc_x_nome_dest,
            'cnpj' => $nfecom->nfc_cnpj_dest ?? '',
            'cpf' => '',
            'ie' => '',
            'endereco' => [
                'logradouro' => $nfecom->nfc_x_lgr_dest ?? '',
                'numero' => $nfecom->nfc_nro_dest ?? '',
                'bairro' => $nfecom->nfc_x_bairro_dest ?? '',
                'municipio' => $nfecom->nfc_x_mun_dest ?? '',
                'uf' => $nfecom->nfc_uf_dest ?? '',
                'cep' => $nfecom->nfc_cep_dest ?? '',
                'telefone' => ''
            ]
        ];

        // Preparar dados do assinante
        $assinante = [
            'iCodAssinante' => $nfecom->nfc_i_cod_assinante ?? '',
            'numero_contrato' => $nfecom->nfc_n_contrato ?? '',
            'identificador_debito' => ''
        ];

        // Preparar dados de faturamento
        // Competência baseada no período fim (formato YYYYMM)
        // Exemplo: se período fim for 31-12-2025, competência será 202512
        $competencia = $nfecom->nfc_compet_fat;
        if (empty($competencia) && !empty($nfecom->nfc_d_per_uso_fim)) {
            $competencia = date('Ym', strtotime($nfecom->nfc_d_per_uso_fim));
        } elseif (empty($competencia) && !empty($nfecom->nfc_d_contrato_ini)) {
            $competencia = date('Ym', strtotime($nfecom->nfc_d_contrato_ini));
        } elseif (empty($competencia)) {
            $competencia = date('Ym');
        }
        // Garantir formato YYYYMM (sem hífen)
        $competencia = str_replace('-', '', $competencia);
        if (strlen($competencia) == 7 && strpos($competencia, '-') !== false) {
            $competencia = str_replace('-', '', $competencia);
        }
        
        $faturamento = [
            'competencia' => $competencia,
            'periodo_inicio' => date('d/m/Y', strtotime($nfecom->nfc_d_per_uso_ini)),
            'periodo_fim' => date('d/m/Y', strtotime($nfecom->nfc_d_per_uso_fim)),
            'vencimento' => date('d/m/Y', strtotime($nfecom->nfc_d_venc_fat)),
            'linha_digitavel' => $nfecom->nfc_linha_digitavel ?? '',
            'cod_barras' => $nfecom->nfc_linha_digitavel ?? ''
        ];

        // Preparar itens - TODOS os dados vêm do banco
        $itensFormatados = [];
        foreach ($itens as $item) {
            // Log para debug - TODOS os valores de ICMS, ICMS ST e FCP
            log_message('debug', 'DANFE Item Formatado - nfi_id: ' . ($item->nfi_id ?? 'N/A') . 
                        ' | nfi_v_prod: ' . ($item->nfi_v_prod ?? 0) . 
                        ' | nfi_v_bc_icms: ' . ($item->nfi_v_bc_icms ?? 0) . 
                        ' | nfi_p_icms: ' . ($item->nfi_p_icms ?? 0) . 
                        ' | nfi_v_icms: ' . ($item->nfi_v_icms ?? 0) . 
                        ' | nfi_v_bc_icms_st: ' . ($item->nfi_v_bc_icms_st ?? 0) . 
                        ' | nfi_v_icms_st: ' . ($item->nfi_v_icms_st ?? 0) . 
                        ' | nfi_v_bc_fcp: ' . ($item->nfi_v_bc_fcp ?? 0) . 
                        ' | nfi_v_fcp: ' . ($item->nfi_v_fcp ?? 0) . 
                        ' | nfi_v_bc_pis: ' . ($item->nfi_v_bc_pis ?? 0) . 
                        ' | nfi_v_bc_cofins: ' . ($item->nfi_v_bc_cofins ?? 0) . 
                        ' | nfi_cst_icms: ' . ($item->nfi_cst_icms ?? 'N/A') . 
                        ' | nfi_cst_pis: ' . ($item->nfi_cst_pis ?? 'N/A') . 
                        ' | nfi_cst_cofins: ' . ($item->nfi_cst_cofins ?? 'N/A'));
            
            $itensFormatados[] = [
                'descricao' => $item->nfi_x_prod ?? '',
                'cclass' => $item->nfi_c_class ?? '',
                'cfop' => $item->nfi_cfop ?? '', // Adicionar CFOP
                'unidade' => $item->nfi_u_med ?? '',
                'quantidade' => floatval($item->nfi_q_faturada ?? 0),
                // Valor unitário = Valor Produto / Quantidade (usar valor do banco, sem cálculo)
                'valor_unitario' => ($item->nfi_q_faturada > 0 && $item->nfi_q_faturada != 0) ? (floatval($item->nfi_v_prod ?? 0) / floatval($item->nfi_q_faturada)) : 0,
                'valor_total' => floatval($item->nfi_v_prod ?? 0),
                'desconto' => floatval($item->nfi_v_desc ?? 0),
                'outros' => floatval($item->nfi_v_outro ?? 0),
                'base_calculo' => floatval($item->nfi_v_bc_icms ?? 0), // Base de cálculo ICMS (usar valor salvo diretamente do banco)
                'aliquota_icms' => floatval($item->nfi_p_icms ?? 0), // Alíquota ICMS
                'valor_icms' => floatval($item->nfi_v_icms ?? 0), // Valor ICMS
                'icms' => [
                    'cst' => $item->nfi_cst_icms ?? '',
                    'csosn' => $item->nfi_csosn ?? null, // CSOSN para Simples Nacional
                    'base_calculo' => floatval($item->nfi_v_bc_icms ?? 0),
                    'aliquota' => floatval($item->nfi_p_icms ?? 0),
                    'valor' => floatval($item->nfi_v_icms ?? 0),
                    'valor_deson' => floatval($item->nfi_v_icms_deson ?? 0), // Valor ICMS Desonerado
                    'motivo_deson' => $item->nfi_mot_des_icms ?? null // Motivo da Desoneração
                ],
                'icms_st' => [
                    'base_calculo' => floatval($item->nfi_v_bc_icms_st ?? 0),
                    'aliquota' => floatval($item->nfi_p_icms_st ?? 0),
                    'valor' => floatval($item->nfi_v_icms_st ?? 0),
                    'base_ret' => floatval($item->nfi_v_bc_st_ret ?? 0), // Base de Cálculo do ST Retido
                    'valor_ret' => floatval($item->nfi_v_icms_st_ret ?? 0), // Valor do ICMS ST Retido
                    'aliquota_st' => floatval($item->nfi_p_st ?? 0), // Alíquota do ST
                    'valor_subst' => floatval($item->nfi_v_icms_subst ?? 0) // Valor do ICMS Próprio do Substituto
                ],
                'fcp' => [
                    'base_calculo' => floatval($item->nfi_v_bc_fcp ?? 0),
                    'aliquota' => floatval($item->nfi_p_fcp ?? 0),
                    'valor' => floatval($item->nfi_v_fcp ?? 0),
                    'valor_st' => floatval($item->nfi_v_fcp_st ?? 0), // Valor do FCP ST
                    'valor_st_ret' => floatval($item->nfi_v_fcp_st_ret ?? 0) // Valor do FCP ST Retido
                ],
                'pis' => [
                    'cst' => $item->nfi_cst_pis ?? '',
                    'base_calculo' => floatval($item->nfi_v_bc_pis ?? 0), // Usar valor salvo diretamente do banco
                    'aliquota' => floatval($item->nfi_p_pis ?? 0),
                    'valor' => floatval($item->nfi_v_pis ?? 0)
                ],
                'cofins' => [
                    'cst' => $item->nfi_cst_cofins ?? '',
                    'base_calculo' => floatval($item->nfi_v_bc_cofins ?? 0), // Usar valor salvo diretamente do banco
                    'aliquota' => floatval($item->nfi_p_cofins ?? 0),
                    'valor' => floatval($item->nfi_v_cofins ?? 0)
                ],
                'fust' => [
                    'base_calculo' => floatval($item->nfi_v_bc_fust ?? 0),
                    'aliquota' => floatval($item->nfi_p_fust ?? 0),
                    'valor' => floatval($item->nfi_v_fust ?? 0)
                ],
                'funtel' => [
                    'base_calculo' => floatval($item->nfi_v_bc_funtel ?? 0),
                    'aliquota' => floatval($item->nfi_p_funtel ?? 0),
                    'valor' => floatval($item->nfi_v_funtel ?? 0)
                ],
                'irrf' => [
                    'base_calculo' => floatval($item->nfi_v_bc_irrf ?? 0),
                    'valor' => floatval($item->nfi_v_irrf ?? 0)
                ]
            ];
        }

        // Calcular bases de cálculo totais dos itens (soma direta dos valores salvos no banco)
        $totalBasePis = 0;
        $totalBaseCofins = 0;
        $totalBaseIcms = 0;
        $totalBaseIcmsSt = 0;
        $totalBaseFcp = 0;
        foreach ($itens as $item) {
            // Usar diretamente os valores salvos no banco de dados (nfecom_itens)
            $totalBasePis += floatval($item->nfi_v_bc_pis ?? 0);
            $totalBaseCofins += floatval($item->nfi_v_bc_cofins ?? 0);
            $totalBaseIcms += floatval($item->nfi_v_bc_icms ?? 0);
            $totalBaseIcmsSt += floatval($item->nfi_v_bc_icms_st ?? 0);
            $totalBaseFcp += floatval($item->nfi_v_bc_fcp ?? 0);
        }
        
        // Preparar totais - TODOS os valores vêm da nota (capa)
        $totais = [
            'valor_total' => floatval($nfecom->nfc_v_nf ?? 0),
            'valor_base_calculo' => floatval($nfecom->nfc_v_bc_icms ?? 0), // Base de cálculo ICMS - APENAS valor do banco, sem fallback
            'valor_produtos' => floatval($nfecom->nfc_v_prod ?? 0),
            // ICMS
            'valor_icms' => floatval($nfecom->nfc_v_icms ?? 0), // Valor Total do ICMS
            'valor_icms_deson' => floatval($nfecom->nfc_v_icms_deson ?? 0), // Valor Total do ICMS Desonerado
            'base_calculo_icms' => floatval($nfecom->nfc_v_bc_icms ?? 0), // Base de Cálculo ICMS total
            // ICMS ST (somar dos itens, pois não há campo total na capa)
            'valor_icms_st' => 0, // Será calculado somando dos itens se necessário
            'base_calculo_icms_st' => floatval($totalBaseIcmsSt), // Base de Cálculo ICMS ST total
            // FCP
            'valor_fcp' => floatval($nfecom->nfc_v_fcp ?? 0), // Valor Total do FCP
            'base_calculo_fcp' => floatval($totalBaseFcp), // Base de Cálculo FCP total
            // Outros
            'valor_isento' => 0,
            'valor_desconto' => floatval($nfecom->nfc_v_desc ?? 0),
            'valor_outros' => floatval($nfecom->nfc_v_outro ?? 0),
            // PIS e COFINS
            'valor_pis' => floatval($nfecom->nfc_v_pis ?? 0),
            'valor_cofins' => floatval($nfecom->nfc_v_cofins ?? 0),
            'base_calculo_pis' => floatval($totalBasePis), // Base de cálculo total PIS
            'base_calculo_cofins' => floatval($totalBaseCofins), // Base de cálculo total COFINS
            // FUST e FUNTTEL
            'valor_fust' => floatval($nfecom->nfc_v_fust ?? 0),
            'valor_funtel' => floatval($nfecom->nfc_v_funtel ?? 0),
            // Retenções
            'valor_ret_pis' => floatval($nfecom->nfc_v_ret_pis ?? 0),
            'valor_ret_cofins' => floatval($nfecom->nfc_v_ret_cofins ?? 0),
            'valor_ret_csll' => floatval($nfecom->nfc_v_ret_csll ?? 0),
            'valor_irrf' => floatval($nfecom->nfc_v_irrf ?? 0)
        ];
        
        // Calcular total de ICMS ST somando dos itens (se não houver campo na capa)
        $totalIcmsSt = 0;
        foreach ($itens as $item) {
            $totalIcmsSt += floatval($item->nfi_v_icms_st ?? 0);
        }
        $totais['valor_icms_st'] = floatval($totalIcmsSt);
        
        // Log para debug dos totais
        log_message('debug', 'DANFE Totais - nfc_v_bc_icms: ' . ($nfecom->nfc_v_bc_icms ?? 0) . 
                    ' | nfc_v_icms: ' . ($nfecom->nfc_v_icms ?? 0) . 
                    ' | nfc_v_fcp: ' . ($nfecom->nfc_v_fcp ?? 0) . 
                    ' | Total Base ICMS (soma itens): ' . $totalBaseIcms . 
                    ' | Total ICMS ST (soma itens): ' . $totalIcmsSt . 
                    ' | Total Base FCP (soma itens): ' . $totalBaseFcp);

        // Preparar dados completos
        // Calcular totais para regra G137 usando os itens do banco
        $totalVProd = 0.00;
        $totalVDesc = 0.00;
        $totalVOutro = 0.00;
        foreach ($itens as $item) {
            $totalVProd += floatval($item->nfi_v_prod ?? 0);
            $totalVDesc += floatval($item->nfi_v_desc ?? 0);
            $totalVOutro += floatval($item->nfi_v_outro ?? 0);
        }
        $vRetPis = floatval($nfecom->nfc_v_ret_pis ?? 0);
        $vRetCofins = floatval($nfecom->nfc_v_ret_cofins ?? 0);
        $vRetCsll = floatval($nfecom->nfc_v_ret_csll ?? 0);
        
        // Calcular total do IRRF somando os itens (conforme rejeição 685)
        // SEMPRE usar a soma dos itens, não o valor do banco
        $totalIrrfItens = 0;
        foreach ($itens as $item) {
            $totalIrrfItens += floatval($item->nfi_v_irrf ?? 0);
        }
        
        // Arredondar para 2 casas decimais para evitar problemas de precisão
        $totalIrrfItens = round($totalIrrfItens, 2);
        
        // SEMPRE atualizar o banco com a soma dos itens
        $vIrrf = $totalIrrfItens;
        $irrfBanco = floatval($nfecom->nfc_v_irrf ?? 0);
        if (abs($totalIrrfItens - $irrfBanco) > 0.001) { // Tolerância menor (0.001 para garantir precisão)
            // Atualizar total do IRRF no banco com a soma dos itens
            $this->Nfecom_model->edit('nfecom_capa', ['nfc_v_irrf' => $totalIrrfItens], 'nfc_id', $id);
            log_message('info', 'Total do IRRF atualizado em prepararDadosEnvio: R$ ' . number_format($irrfBanco, 2, ',', '.') . ' → R$ ' . number_format($totalIrrfItens, 2, ',', '.') . ' (soma dos itens)');
            // Recarregar nfecom para ter o valor atualizado
            $nfecom = $this->Nfecom_model->getById($id);
        }

        // Regra G137: vNF = vProd + vOutro - vDesc - vRetPIS - vRetCofins - vRetCSLL - vIRRF
        $vNfCalculado = $totalVProd + $totalVOutro - $totalVDesc - $vRetPis - $vRetCofins - $vRetCsll - $vIrrf;

        // Atualizar vNF no banco se estiver divergente (tolerância de centavos)
        $vNfBanco = floatval($nfecom->nfc_v_nf ?? 0);
        if (abs($vNfCalculado - $vNfBanco) > 0.01) {
            $this->Nfecom_model->edit('nfecom_capa', ['nfc_v_nf' => $vNfCalculado], 'nfc_id', $id);
            $nfecom->nfc_v_nf = $vNfCalculado;
            log_message('info', 'vNF ajustado pela regra G137: ' . number_format($vNfBanco, 2, '.', '') . ' → ' . number_format($vNfCalculado, 2, '.', ''));
        }

        $dados = [
            'numero' => $nfecom->nfc_nnf,
            'chave' => $nfecom->nfc_ch_nfcom,
            'status' => $nfecom->nfc_status,
            'protocolo' => $nfecom->nfc_n_prot,
            'data_autorizacao' => $nfecom->nfc_dh_recbto ? date('d/m/Y H:i:s', strtotime($nfecom->nfc_dh_recbto)) : '',
            'destinatario' => $destinatario,
            'assinante' => $assinante,
            'faturamento' => $faturamento,
            'itens' => $itensFormatados,
            'totais' => $totais,
            'informacoes_adicionais' => $nfecom->nfc_inf_cpl ?? ''
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
            header('Content-Disposition: inline; filename="danfe_nfcom_' . $nfecom->nfc_nnf . '.pdf"');
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
                'cnpj' => $emit['cnpj'],
                'ie' => $emit['ie'],
                'endereco' => $emit['enderEmit']['xLgr'] . ', ' . $emit['enderEmit']['nro'] .
                             (!empty($emit['enderEmit']['xCpl']) ? ' - ' . $emit['enderEmit']['xCpl'] : '') .
                             ' - ' . $emit['enderEmit']['xBairro'] . ' - ' . $emit['enderEmit']['xMun'] .
                             '/' . $emit['enderEmit']['uf'] . ' - CEP: ' . $emit['enderEmit']['cep'],
                'telefone' => $emit['enderEmit']['fone'] ?? '',
                'email' => '',
                'logo' => FCPATH . 'assets/uploads/logomarca.png'
            ],
            'nfcom' => [
                'numero' => $nfecom->nfc_nnf,
                'serie' => $nfecom->nfc_serie,
                'data_emissao' => date('d/m/Y H:i:s', strtotime($nfecom->nfc_dhemi)),
                'chave_acesso' => $nfecom->nfc_ch_nfcom,
                'protocolo' => $nfecom->nfc_n_prot,
                'data_autorizacao' => $nfecom->nfc_dh_recbto ? date('d/m/Y H:i:s', strtotime($nfecom->nfc_dh_recbto)) : ''
            ]
        ];

        // Preparar dados dos itens - TODOS os dados vêm do banco
        $produtos = [];
        foreach ($itens as $item) {
            // Log para debug
            log_message('debug', 'DANFE Item - nfi_id: ' . ($item->nfi_id ?? 'N/A') . 
                        ' | nfi_v_prod: ' . ($item->nfi_v_prod ?? 0) . 
                        ' | nfi_v_bc_pis: ' . ($item->nfi_v_bc_pis ?? 0) . 
                        ' | nfi_v_bc_cofins: ' . ($item->nfi_v_bc_cofins ?? 0) . 
                        ' | nfi_cst_pis: ' . ($item->nfi_cst_pis ?? 'N/A') . 
                        ' | nfi_cst_cofins: ' . ($item->nfi_cst_cofins ?? 'N/A') . 
                        ' | nfi_p_pis: ' . ($item->nfi_p_pis ?? 0) . 
                        ' | nfi_p_cofins: ' . ($item->nfi_p_cofins ?? 0) . 
                        ' | nfi_v_pis: ' . ($item->nfi_v_pis ?? 0) . 
                        ' | nfi_v_cofins: ' . ($item->nfi_v_cofins ?? 0));
            
            $produtos[] = [
                'codigo' => $item->nfi_c_prod ?? '',
                'descricao' => $item->nfi_x_prod ?? '',
                'ncm' => '', // NCM não está na tabela nfecom_itens
                'cfop' => $item->nfi_cfop ?? '',
                'c_class' => $item->nfi_c_class ?? '', // Adicionar código de classificação
                'unidade' => $item->nfi_u_med ?? '',
                'quantidade' => floatval($item->nfi_q_faturada ?? 0),
                // Valor unitário = Valor Produto / Quantidade (usar valor do banco, sem cálculo)
                'valor_unitario' => ($item->nfi_q_faturada > 0 && $item->nfi_q_faturada != 0) ? (floatval($item->nfi_v_prod ?? 0) / floatval($item->nfi_q_faturada)) : 0,
                'valor_total' => floatval($item->nfi_v_prod ?? 0),
                'desconto' => floatval($item->nfi_v_desc ?? 0),
                'outros' => floatval($item->nfi_v_outro ?? 0),
                'base_calculo' => floatval($item->nfi_v_bc_icms ?? 0), // Base de cálculo ICMS
                'aliquota_icms' => floatval($item->nfi_p_icms ?? 0), // Alíquota ICMS
                'valor_icms' => floatval($item->nfi_v_icms ?? 0), // Valor ICMS
                'icms' => [
                    'cst' => $item->nfi_cst_icms ?? '',
                    'csosn' => $item->nfi_csosn ?? null, // CSOSN para Simples Nacional
                    'base_calculo' => floatval($item->nfi_v_bc_icms ?? 0),
                    'aliquota' => floatval($item->nfi_p_icms ?? 0),
                    'valor' => floatval($item->nfi_v_icms ?? 0),
                    'valor_deson' => floatval($item->nfi_v_icms_deson ?? 0), // Valor ICMS Desonerado
                    'motivo_deson' => $item->nfi_mot_des_icms ?? null // Motivo da Desoneração
                ],
                'icms_st' => [
                    'base_calculo' => floatval($item->nfi_v_bc_icms_st ?? 0),
                    'aliquota' => floatval($item->nfi_p_icms_st ?? 0),
                    'valor' => floatval($item->nfi_v_icms_st ?? 0),
                    'base_ret' => floatval($item->nfi_v_bc_st_ret ?? 0), // Base de Cálculo do ST Retido
                    'valor_ret' => floatval($item->nfi_v_icms_st_ret ?? 0), // Valor do ICMS ST Retido
                    'aliquota_st' => floatval($item->nfi_p_st ?? 0), // Alíquota do ST
                    'valor_subst' => floatval($item->nfi_v_icms_subst ?? 0) // Valor do ICMS Próprio do Substituto
                ],
                'fcp' => [
                    'base_calculo' => floatval($item->nfi_v_bc_fcp ?? 0),
                    'aliquota' => floatval($item->nfi_p_fcp ?? 0),
                    'valor' => floatval($item->nfi_v_fcp ?? 0),
                    'valor_st' => floatval($item->nfi_v_fcp_st ?? 0), // Valor do FCP ST
                    'valor_st_ret' => floatval($item->nfi_v_fcp_st_ret ?? 0) // Valor do FCP ST Retido
                ],
                'pis' => [
                    'cst' => $item->nfi_cst_pis ?? '',
                    'base_calculo' => floatval($item->nfi_v_bc_pis ?? 0), // Usar valor salvo diretamente do banco
                    'aliquota' => floatval($item->nfi_p_pis ?? 0),
                    'valor' => floatval($item->nfi_v_pis ?? 0)
                ],
                'cofins' => [
                    'cst' => $item->nfi_cst_cofins ?? '',
                    'base_calculo' => floatval($item->nfi_v_bc_cofins ?? 0), // Usar valor salvo diretamente do banco
                    'aliquota' => floatval($item->nfi_p_cofins ?? 0),
                    'valor' => floatval($item->nfi_v_cofins ?? 0)
                ],
                'fust' => [
                    'base_calculo' => floatval($item->nfi_v_bc_fust ?? 0),
                    'aliquota' => floatval($item->nfi_p_fust ?? 0),
                    'valor' => floatval($item->nfi_v_fust ?? 0)
                ],
                'funtel' => [
                    'base_calculo' => floatval($item->nfi_v_bc_funtel ?? 0),
                    'aliquota' => floatval($item->nfi_p_funtel ?? 0),
                    'valor' => floatval($item->nfi_v_funtel ?? 0)
                ],
                'irrf' => [
                    'base_calculo' => floatval($item->nfi_v_bc_irrf ?? 0),
                    'valor' => floatval($item->nfi_v_irrf ?? 0)
                ]
            ];
        }

        // Calcular bases de cálculo totais dos itens (soma direta dos valores salvos no banco)
        $totalBasePis = 0;
        $totalBaseCofins = 0;
        $totalBaseIcms = 0;
        $totalBaseIcmsSt = 0;
        $totalBaseFcp = 0;
        foreach ($itens as $item) {
            // Usar diretamente os valores salvos no banco de dados (nfecom_itens)
            $totalBasePis += floatval($item->nfi_v_bc_pis ?? 0);
            $totalBaseCofins += floatval($item->nfi_v_bc_cofins ?? 0);
            $totalBaseIcms += floatval($item->nfi_v_bc_icms ?? 0);
            $totalBaseIcmsSt += floatval($item->nfi_v_bc_icms_st ?? 0);
            $totalBaseFcp += floatval($item->nfi_v_bc_fcp ?? 0);
        }
        
        // Preparar totais - TODOS os valores vêm da nota (capa)
        $totais = [
            'valor_total' => floatval($nfecom->nfc_v_nf ?? 0),
            'valor_base_calculo' => floatval($nfecom->nfc_v_bc_icms ?? 0), // Base de cálculo ICMS - APENAS valor do banco, sem fallback
            'valor_produtos' => floatval($nfecom->nfc_v_prod ?? 0),
            // ICMS
            'valor_icms' => floatval($nfecom->nfc_v_icms ?? 0), // Valor Total do ICMS
            'valor_icms_deson' => floatval($nfecom->nfc_v_icms_deson ?? 0), // Valor Total do ICMS Desonerado
            'base_calculo_icms' => floatval($nfecom->nfc_v_bc_icms ?? 0), // Base de Cálculo ICMS total
            // ICMS ST (somar dos itens, pois não há campo total na capa)
            'valor_icms_st' => 0, // Será calculado somando dos itens se necessário
            'base_calculo_icms_st' => floatval($totalBaseIcmsSt), // Base de Cálculo ICMS ST total
            // FCP
            'valor_fcp' => floatval($nfecom->nfc_v_fcp ?? 0), // Valor Total do FCP
            'base_calculo_fcp' => floatval($totalBaseFcp), // Base de Cálculo FCP total
            // Outros
            'valor_isento' => 0,
            'valor_desconto' => floatval($nfecom->nfc_v_desc ?? 0),
            'valor_outros' => floatval($nfecom->nfc_v_outro ?? 0),
            // PIS e COFINS
            'valor_pis' => floatval($nfecom->nfc_v_pis ?? 0),
            'valor_cofins' => floatval($nfecom->nfc_v_cofins ?? 0),
            'base_calculo_pis' => floatval($totalBasePis), // Base de cálculo total PIS
            'base_calculo_cofins' => floatval($totalBaseCofins), // Base de cálculo total COFINS
            // FUST e FUNTTEL
            'valor_fust' => floatval($nfecom->nfc_v_fust ?? 0),
            'valor_funtel' => floatval($nfecom->nfc_v_funtel ?? 0),
            // Retenções
            'valor_ret_pis' => floatval($nfecom->nfc_v_ret_pis ?? 0),
            'valor_ret_cofins' => floatval($nfecom->nfc_v_ret_cofins ?? 0),
            'valor_ret_csll' => floatval($nfecom->nfc_v_ret_csll ?? 0),
            'valor_irrf' => floatval($nfecom->nfc_v_irrf ?? 0)
        ];
        
        // Calcular total de ICMS ST somando dos itens (se não houver campo na capa)
        $totalIcmsSt = 0;
        foreach ($itens as $item) {
            $totalIcmsSt += floatval($item->nfi_v_icms_st ?? 0);
        }
        $totais['valor_icms_st'] = floatval($totalIcmsSt);
        
        // Log para debug dos totais
        log_message('debug', 'DANFE Totais - nfc_v_bc_icms: ' . ($nfecom->nfc_v_bc_icms ?? 0) . 
                    ' | nfc_v_icms: ' . ($nfecom->nfc_v_icms ?? 0) . 
                    ' | nfc_v_fcp: ' . ($nfecom->nfc_v_fcp ?? 0) . 
                    ' | Total Base ICMS (soma itens): ' . $totalBaseIcms . 
                    ' | Total ICMS ST (soma itens): ' . $totalIcmsSt . 
                    ' | Total Base FCP (soma itens): ' . $totalBaseFcp);

        // Preparar dados completos
        $dados = [
            'numero' => $nfecom->nfc_nnf,
            'chave' => $nfecom->nfc_ch_nfcom,
            'destinatario' => [
                'nome' => $nfecom->nfc_x_nome_dest,
                'cnpj' => $nfecom->nfc_cnpj_dest,
                'endereco' => $nfecom->NFC_X_LOGRADOURO_DEST . ', ' . $nfecom->NFC_N_DEST .
                             (!empty($nfecom->NFC_X_COMPLEMENTO_DEST) ? ' - ' . $nfecom->NFC_X_COMPLEMENTO_DEST : '') .
                             ' - ' . $nfecom->nfc_x_bairro_dest . ' - ' . $nfecom->NFC_X_MUNICIPIO_DEST .
                             '/' . $nfecom->nfc_uf_dest . ' - CEP: ' . $nfecom->nfc_cep_dest
            ],
            'assinante' => [
                'codigo' => $nfecom->nfc_cnpj_dest,
                'tipo' => 3, // Pessoa Jurídica
                'servico' => 6, // Telecomunicações
                'contrato' => $nfecom->nfc_n_contrato ?? ''
            ],
            'faturamento' => [
                // Competência baseada no período fim (formato YYYYMM)
                // Exemplo: se período fim for 31-12-2025, competência será 202512
                'competencia' => !empty($nfecom->nfc_d_per_uso_fim) && empty($nfecom->nfc_compet_fat) 
                    ? date('Ym', strtotime($nfecom->nfc_d_per_uso_fim)) 
                    : (!empty($nfecom->nfc_d_contrato_ini) && empty($nfecom->nfc_compet_fat)
                        ? date('Ym', strtotime($nfecom->nfc_d_contrato_ini))
                        : (str_replace('-', '', $nfecom->nfc_compet_fat) ?: date('Ym'))),
                'vencimento' => date('d/m/Y', strtotime($nfecom->nfc_d_venc_fat)),
                'periodo_inicio' => date('d/m/Y', strtotime($nfecom->nfc_d_per_uso_ini)),
                'periodo_fim' => date('d/m/Y', strtotime($nfecom->nfc_d_per_uso_fim)),
                'cod_barras' => $nfecom->nfc_cod_barras ?? '1'
            ],
            'itens' => $produtos,
            'totais' => $totais,
            'informacoes_adicionais' => $nfecom->nfc_inf_cpl ?? ''
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
            header('Content-Disposition: attachment; filename="danfe_nfcom_' . $nfecom->nfc_nnf . '.pdf"');
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
        if ($nfecom->nfc_status != 4) {
            $this->session->set_flashdata('error', 'Apenas NFCom rejeitadas podem ser reemitidas.');
            redirect(site_url('nfecom/visualizar/' . $id));
        }

        try {
            // Recalcular total do IRRF somando os itens antes de preparar reemissão
            $itens = $this->Nfecom_model->getItens($id);
            $totalIrrfItens = 0;
            foreach ($itens as $item) {
                $totalIrrfItens += floatval($item->nfi_v_irrf ?? 0);
            }
            $totalIrrfItens = round($totalIrrfItens, 2);
            
            // Recalcular vNF também
            $totalVProd = 0;
            $totalVDesc = 0;
            $totalVOutro = 0;
            foreach ($itens as $item) {
                $totalVProd += floatval($item->nfi_v_prod ?? 0);
                $totalVDesc += floatval($item->nfi_v_desc ?? 0);
                $totalVOutro += floatval($item->nfi_v_outro ?? 0);
            }
            $vRetPis = floatval($nfecom->nfc_v_ret_pis ?? 0);
            $vRetCofins = floatval($nfecom->nfc_v_ret_cofins ?? 0);
            $vRetCsll = floatval($nfecom->nfc_v_ret_csll ?? 0);
            // Regra G137: vNF = vProd + vOutro - vDesc - vRetPIS - vRetCofins - vRetCSLL - vIRRF
            $vNfRecalculado = $totalVProd + $totalVOutro - $totalVDesc - $vRetPis - $vRetCofins - $vRetCsll - $totalIrrfItens;
            $vNfRecalculado = round($vNfRecalculado, 2);
            
            // Resetar apenas o status, mantendo a mesma chave de acesso
            // IMPORTANTE: Atualizar total do IRRF e vNF com valores recalculados
            $dadosAtualizacao = [
                'nfc_status' => 1, // Voltar para status "Salvo"
                'nfc_xml' => null, // Limpar XML antigo
                'nfc_c_stat' => null,
                'nfc_x_motivo' => null,
                'nfc_n_prot' => null,
                'nfc_dh_recbto' => null,
                'nfc_v_irrf' => $totalIrrfItens, // Atualizar com soma dos itens
                'nfc_v_nf' => $vNfRecalculado // Atualizar vNF recalculado
            ];

            $this->Nfecom_model->edit('nfecom_capa', $dadosAtualizacao, 'nfc_id', $id);
            
            log_message('info', 'Reemissão NFCom preparada - Total IRRF: R$ ' . number_format($totalIrrfItens, 2, ',', '.') . ' (soma dos itens), vNF: R$ ' . number_format($vNfRecalculado, 2, ',', '.'));

            $this->session->set_flashdata('success', 'NFCom preparada para reemissão com a mesma chave: ' . $nfecom->nfc_ch_nfcom);

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
        if ($nfecom->nfc_status < 2 || $nfecom->nfc_status == 4) {
            $configFiscal = $this->getConfiguracaoNfcom();
            $emitente = $this->Nfe_model->getEmit(); // Buscar dados atualizados do emitente

            if ($configFiscal && $emitente) {
                // Calcular cUF a partir da UF do emitente, não do destinatário
                $ufEmit = $emitente['enderEmit']['uf'] ?? 'GO';
                $codigoUf = $this->get_cUF($ufEmit);

                // Para NFCom rejeitadas (reemissão), manter o número, chave, série e data originais
                // Para NFCom novas, apenas pegar número atual (incremento será feito no final do processo)
                $isReemissao = ($nfecom->nfc_status == 4);
                
                if ($nfecom->nfc_status < 2) {
                    $numeroNota = $configFiscal->cfg_numero_atual;
                } else {
                    // NFCom rejeitada mantém o número atual
                    $numeroNota = $nfecom->nfc_nnf;
                }

                // Buscar dados atualizados do cliente/destinatário
                $this->db->select('c.*, p.*, e.end_logradouro, e.end_numero, e.end_complemento, e.end_cep, b.bai_nome, m.mun_nome, m.mun_ibge, es.est_uf, d.doc_numero as PES_IE, d.doc_natureza_contribuinte');
                $this->db->from('clientes c');
                $this->db->join('pessoas p', 'p.pes_id = c.pes_id');
                $this->db->join('enderecos e', 'e.pes_id = p.pes_id AND e.end_padrao = 1', 'left');
                $this->db->join('bairros b', 'b.bai_id = e.bai_id', 'left');
                $this->db->join('municipios m', 'm.mun_id = e.mun_id', 'left');
                $this->db->join('estados es', 'es.est_id = e.est_id', 'left');
                $this->db->join('documentos d', "d.pes_id = p.pes_id AND d.doc_tipo_documento = 'Inscrição Estadual'", 'left');
                $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
                if (!empty($nfecom->cln_id)) {
                    $this->db->where('c.cln_id', $nfecom->cln_id);
                } else {
                    $cnpjLimpo = preg_replace('/\D/', '', $nfecom->nfc_cnpj_dest);
                    $this->db->where('p.pes_cpfcnpj', $cnpjLimpo);
                }
                $cliente = $this->db->get()->row();

                $atualizacao = [
                    'nfc_tipo_ambiente' => $configFiscal->cfg_ambiente,
                    // Sincronizar dados do emitente do cadastro de empresas
                    'nfc_cnpj_emit' => $emitente['cnpj'],
                    'nfc_ie_emit' => $emitente['ie'],
                    'nfc_crt_emit' => $emitente['CRT'],
                    'nfc_x_nome_emit' => $emitente['xNome'],
                    'nfc_x_fant_emit' => $emitente['xFant'],
                    'nfc_x_lgr_emit' => $emitente['enderEmit']['xLgr'],
                    'nfc_nro_emit' => $emitente['enderEmit']['nro'],
                    'nfc_x_cpl_emit' => $emitente['enderEmit']['xCpl'],
                    'nfc_x_bairro_emit' => $emitente['enderEmit']['xBairro'],
                    'nfc_c_mun_emit' => $emitente['enderEmit']['cMun'],
                    'nfc_x_mun_emit' => $emitente['enderEmit']['xMun'],
                    'nfc_cep_emit' => $emitente['enderEmit']['cep'],
                    'nfc_uf_emit' => $emitente['enderEmit']['uf'],
                    'nfc_fone_emit' => $emitente['enderEmit']['fone'],
                ];
                
                // Para reemissão, manter série, número, chave e data originais
                if (!$isReemissao) {
                    $atualizacao['nfc_serie'] = $configFiscal->cfg_serie;
                    $atualizacao['nfc_nnf'] = $numeroNota;
                    $atualizacao['nfc_cuf'] = $codigoUf;
                }
                // Se for reemissão, não atualizar série, número, CUF (mantém originais)

                // Sincronizar dados do destinatário do cadastro de clientes
                if ($cliente) {
                    $atualizacao['nfc_x_nome_dest'] = $cliente->pes_nome;
                    $atualizacao['nfc_cnpj_dest'] = preg_replace('/\D/', '', $cliente->pes_cpfcnpj);
                    $atualizacao['nfc_ind_ie_dest'] = ($cliente->doc_natureza_contribuinte == 'Contribuinte') ? '1' : '9';
                    $atualizacao['nfc_x_lgr_dest'] = $cliente->end_logradouro;
                    $atualizacao['nfc_nro_dest'] = $cliente->end_numero;
                    $atualizacao['nfc_x_cpl_dest'] = $cliente->end_complemento;
                    $atualizacao['nfc_x_bairro_dest'] = $cliente->bai_nome;
                    $atualizacao['nfc_c_mun_dest'] = $cliente->mun_ibge;
                    $atualizacao['nfc_x_mun_dest'] = $cliente->mun_nome;
                    $atualizacao['nfc_cep_dest'] = preg_replace('/\D/', '', $cliente->end_cep);
                    $atualizacao['nfc_uf_dest'] = $cliente->est_uf;
                }

                // Para reemissão (NFCom rejeitada), limpar o motivo antigo e manter chave original
                if ($isReemissao) {
                    $atualizacao['nfc_x_motivo'] = null;
                    $atualizacao['nfc_c_stat'] = null;
                    $atualizacao['nfc_n_prot'] = null;
                    $atualizacao['nfc_dh_recbto'] = null;
                    $atualizacao['nfc_xml'] = null;
                    // Não recalcular chave, manter a original
                    
                    // IMPORTANTE: Recalcular total do IRRF somando os itens (conforme rejeição 685)
                    $itens = $this->Nfecom_model->getItens($id);
                    $totalIrrfItens = 0;
                    foreach ($itens as $item) {
                        $totalIrrfItens += floatval($item->nfi_v_irrf ?? 0);
                    }
                    $totalIrrfItens = round($totalIrrfItens, 2);
                    
                    // Atualizar total do IRRF na capa com a soma dos itens
                    $atualizacao['nfc_v_irrf'] = $totalIrrfItens;
                    log_message('info', 'Reemissão NFCom - Total do IRRF recalculado: R$ ' . number_format($totalIrrfItens, 2, ',', '.') . ' (soma dos IRRF dos itens)');
                    
                    // Recalcular vNF também
                    $totalVProd = 0;
                    $totalVDesc = 0;
                    $totalVOutro = 0;
                    foreach ($itens as $item) {
                        $totalVProd += floatval($item->nfi_v_prod ?? 0);
                        $totalVDesc += floatval($item->nfi_v_desc ?? 0);
                        $totalVOutro += floatval($item->nfi_v_outro ?? 0);
                    }
                    $vRetPis = floatval($nfecom->nfc_v_ret_pis ?? 0);
                    $vRetCofins = floatval($nfecom->nfc_v_ret_cofins ?? 0);
                    $vRetCsll = floatval($nfecom->nfc_v_ret_csll ?? 0);
                    // Regra G137: vNF = vProd + vOutro - vDesc - vRetPIS - vRetCofins - vRetCSLL - vIRRF
                    $vNfRecalculado = $totalVProd + $totalVOutro - $totalVDesc - $vRetPis - $vRetCofins - $vRetCsll - $totalIrrfItens;
                    $vNfRecalculado = round($vNfRecalculado, 2);
                    $atualizacao['nfc_v_nf'] = $vNfRecalculado;
                    log_message('info', 'Reemissão NFCom - Valor da NF recalculado: R$ ' . number_format($vNfRecalculado, 2, ',', '.'));
                } else {
                    // Para NFCom nova, calcular nova chave
                    $chaveData = [
                        'nfc_cuf' => $atualizacao['nfc_cuf'],
                        'nfc_dhemi' => $nfecom->nfc_dhemi ?? date('Y-m-d H:i:s'),
                        'nfc_cnpj_emit' => $atualizacao['nfc_cnpj_emit'],
                        'nfc_mod' => $nfecom->nfc_mod,
                        'nfc_serie' => $atualizacao['nfc_serie'],
                        'nfc_nnf' => $atualizacao['nfc_nnf'],
                        'nfc_tp_emis' => $nfecom->nfc_tp_emis,
                        'nfc_cnf' => $nfecom->nfc_cnf,
                    ];

                    // nSiteAutoriz é necessário para a chave e DV
                    $nSiteAutoriz = 0; // Default
                    $chaveData['nfc_n_site_autoriz'] = $nSiteAutoriz;

                    $atualizacao['nfc_cdv'] = $this->calculateDV($chaveData);
                    $chaveData['nfc_cdv'] = $atualizacao['nfc_cdv'];
                    $atualizacao['nfc_ch_nfcom'] = $this->generateChave($chaveData);
                }

                $this->Nfecom_model->edit('nfecom_capa', $atualizacao, 'nfc_id', $id);
                $nfecom = $this->Nfecom_model->getById($id); // Recarregar a NFCom com os dados atualizados
                
                // Se for reemissão, garantir que o total do IRRF está correto
                if ($isReemissao) {
                    // Recalcular e atualizar total do IRRF uma vez mais para garantir
                    $itens = $this->Nfecom_model->getItens($id);
                    $totalIrrfItens = 0;
                    foreach ($itens as $item) {
                        $totalIrrfItens += floatval($item->nfi_v_irrf ?? 0);
                    }
                    $totalIrrfItens = round($totalIrrfItens, 2);
                    
                    $irrfBanco = floatval($nfecom->nfc_v_irrf ?? 0);
                    if (abs($totalIrrfItens - $irrfBanco) > 0.001) {
                        $this->Nfecom_model->edit('nfecom_capa', ['nfc_v_irrf' => $totalIrrfItens], 'nfc_id', $id);
                        log_message('info', 'Reemissão NFCom - Total do IRRF corrigido após atualização: R$ ' . number_format($irrfBanco, 2, ',', '.') . ' → R$ ' . number_format($totalIrrfItens, 2, ',', '.'));
                        $nfecom = $this->Nfecom_model->getById($id);
                    }
                }
            }
        }

        // Verificar se é reemissão
        $isReemissao = ($nfecom->nfc_status == 4);
        
        // Validar certificado configurado para NFCOM
        $configFiscal = $this->getConfiguracaoNfcom();
        if (!$configFiscal || empty($configFiscal->cer_arquivo) || empty($configFiscal->cer_senha)) {
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
                $nfcomService->setCertificate($configFiscal->cer_arquivo, $configFiscal->cer_senha);
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
                
                // Montar XML autorizado (NFCom + protocolo)
                $xmlAutorizado = $this->montarXmlAutorizado($xmlSigned, $retorno['xml']);
                
                // Log para debug
                log_message('info', 'NFCom: XML autorizado montado. Tamanho: ' . strlen($xmlAutorizado) . ' bytes');
                
                // Verificar se o XML autorizado foi montado corretamente
                if (empty($xmlAutorizado) || strlen($xmlAutorizado) < 100) {
                    log_message('error', 'NFCom: XML autorizado parece estar vazio ou inválido. Usando XML assinado como fallback.');
                    $xmlAutorizado = $xmlSigned;
                }

                $dadosAtu = [
                    'nfc_status' => 3, // Autorizado
                    'nfc_tipo_ambiente' => $dados['ide']['tpAmb'],
                    'nfc_ch_nfcom' => $chaveAcesso,
                    'nfc_c_stat' => $cStat,
                    'nfc_x_motivo' => $motivo,
                    'nfc_n_prot' => $protocolo,
                    'nfc_dh_recbto' => $dhRecbto,
                    'nfc_xml' => $xmlAutorizado // Salva o XML autorizado com protocolo
                ];

                $this->Nfecom_model->updateStatus($id, $dadosAtu);
                
                // Verificar se foi salvo corretamente
                $nfecomVerificacao = $this->Nfecom_model->getById($id);
                if (empty($nfecomVerificacao->nfc_xml)) {
                    log_message('error', 'NFCom: ERRO - XML autorizado não foi salvo no banco! ID: ' . $id);
                } else {
                    log_message('info', 'NFCom: XML autorizado salvo com sucesso no banco. Tamanho salvo: ' . strlen($nfecomVerificacao->nfc_xml) . ' bytes');
                }
                $this->registrarProtocolo($id, $protocolo, 'AUTORIZACAO', $motivo, $dhRecbto);

                log_info('NFCom Autorizada Real (ID: ' . $id . ', Chave: ' . $chaveAcesso . ')');
                log_message('info', 'NFCom ' . ($isReemissao ? 'Reemissão' : 'Emissão') . ' concluída com sucesso - ID: ' . $id);
                $this->session->set_flashdata('success', 'NFCom ' . ($isReemissao ? 'reemitida' : 'autorizada') . ' com sucesso no SEFAZ! Chave: ' . $chaveAcesso);
            } elseif (in_array($cStat, ['110', '205', '301', '302', '303'])) {
                // Erro de validação/rejeição
                $motivo = $retorno['xMotivo'] ?? 'Erro de validação';
                $dadosAtu = [
                    'nfc_status' => 4, // Rejeitada
                    'nfc_c_stat' => $cStat,
                    'nfc_x_motivo' => $motivo
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
                    'nfc_status' => 4, // Rejeitada
                    'nfc_c_stat' => $cStat,
                    'nfc_x_motivo' => $motivo
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
                // Limpar qualquer output anterior
                if (ob_get_length()) {
                    ob_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                $response = ['success' => false, 'message' => $mensagemErro];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                return false;
            }

            // Se não for redirecionamento, apenas retornar false sem gerar output
            if (!$redirect) {
                return false;
            }
            
            $this->session->set_flashdata('error', $mensagemErro);
            redirect(site_url('nfecom/visualizar/' . $id));
            return false;
        }

        // Incrementar sequência apenas uma vez no final do processo para NFCom novas
        if ($nfecom->nfc_status < 2) {
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
            if ($nfecom->nfc_status != 3 && $nfecom->nfc_status != 5) {
                throw new Exception('Apenas NFCom autorizadas podem ser canceladas. Status atual: ' . $nfecom->nfc_status);
            }

            // Verificar se já está cancelada
            if ($nfecom->nfc_status == 7) {
                throw new Exception('Esta NFCom já está cancelada.');
            }

            // Verificar se tem chave de acesso
            if (empty($nfecom->nfc_ch_nfcom)) {
                throw new Exception('Chave de acesso da NFCom não encontrada.');
            }

            // Verificar se tem protocolo
            if (empty($nfecom->nfc_n_prot)) {
                throw new Exception('Protocolo da NFCom não encontrado. É necessário ter um protocolo de autorização para cancelar.');
            }

            // Validar certificado configurado
            $configFiscal = $this->getConfiguracaoNfcom();
            if (!$configFiscal || empty($configFiscal->cer_arquivo) || empty($configFiscal->cer_senha)) {
                throw new Exception('Nenhum certificado válido configurado para NFCOM.');
            }

            // Carregar bibliotecas
            $this->load->library('NFComService');

            // Configurar serviço
            $nfcomService = new NFComService([
                'ambiente' => $nfecom->nfc_tipo_ambiente ?? 2,
                'disable_cert_validation' => true
            ]);
            $nfcomService->setCertificate($configFiscal->cer_arquivo, $configFiscal->cer_senha);

            // Gerar evento de cancelamento
            $eventoCancelamento = $this->gerarEventoCancelamento($nfecom, $justificativa);
            
            // Assinar evento usando método específico para eventos (infEvento)
            $eventoAssinado = $this->assinarEventoNFCom($configFiscal, $eventoCancelamento);
            
            // Enviar evento para SEFAZ
            $retornoCancelamento = $nfcomService->sendEvent($eventoAssinado, $nfecom->nfc_tipo_ambiente ?? 2);
            
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
                    'nfc_status' => 7, // Cancelada
                    'nfc_x_motivo' => 'Cancelada: ' . $justificativa,
                    'nfc_c_stat' => $cStat,
                    'nfc_n_prot_canc' => $protocoloCancelamento
                ];

                $this->Nfecom_model->edit('nfecom_capa', $dadosAtualizacao, 'nfc_id', $nfecom_id);

                // Registrar protocolo de cancelamento
                $this->registrarProtocolo($nfecom_id, $protocoloCancelamento, 'CANCELAMENTO', $motivo);

                log_message('info', 'NFCom cancelada com sucesso na SEFAZ - ID: ' . $nfecom_id . ', Chave: ' . $nfecom->nfc_ch_nfcom . ', Protocolo: ' . $protocoloCancelamento);
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

    /**
     * Cancelar NFCom informando apenas a chave de acesso.
     */
    public function cancelarPorChave()
    {
        $isAjax = $this->input->is_ajax_request() || $this->input->post('ajax') === 'true';

        if ($isAjax) {
            $this->output->set_content_type('application/json');
        }

        try {
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
                throw new Exception('Você não tem permissão para cancelar NFCom.');
            }

            if ($this->input->method() !== 'post') {
                // Exibir formulário
                $this->data['view'] = 'nfecom/cancelar_por_chave';
                return $this->layout();
            }

            $chave = trim((string) $this->input->post('chave_nfcom'));
            $justificativa = trim((string) $this->input->post('justificativa'));

            if ($chave === '') {
                throw new Exception('Chave de acesso não informada.');
            }

            if ($justificativa === '') {
                throw new Exception('Justificativa não informada.');
            }

            if (mb_strlen($justificativa, 'UTF-8') < 15) {
                throw new Exception('A justificativa deve ter no mínimo 15 caracteres.');
            }

            $chaveLimpa = preg_replace('/\D/', '', $chave);
            if (strlen($chaveLimpa) !== 44) {
                throw new Exception('A chave de acesso deve conter 44 dígitos.');
            }

            $configFiscal = $this->getConfiguracaoNfcom();
            if (!$configFiscal || empty($configFiscal->cer_arquivo) || empty($configFiscal->cer_senha)) {
                throw new Exception('Nenhum certificado válido configurado para NFCOM.');
            }

            // Consultar NFCom na SEFAZ para obter o protocolo
            $this->load->library('NFComService');
            $ambiente = $configFiscal->cfg_ambiente ?? 2;
            $nfcomService = new NFComService([
                'ambiente' => $ambiente,
                'disable_cert_validation' => true
            ]);
            $nfcomService->setCertificate($configFiscal->cer_arquivo, $configFiscal->cer_senha);

            $consulta = $nfcomService->consult($chaveLimpa, $ambiente);
            if (isset($consulta['error'])) {
                throw new Exception('Erro ao consultar SEFAZ: ' . $consulta['error']);
            }

            $cStat = $consulta['cStat'] ?? '999';
            $xMotivo = $consulta['xMotivo'] ?? 'Erro desconhecido';

            if ($cStat == '101') {
                // Já cancelada
                $msg = 'NFCom já está cancelada na SEFAZ. Motivo: ' . $xMotivo;
                if ($isAjax) {
                    echo json_encode(['success' => true, 'message' => $msg]);
                    return;
                }
                $this->session->set_flashdata('info', $msg);
                redirect(site_url('nfecom'));
                return;
            }

            if ($cStat != '100') {
                throw new Exception('NFCom não autorizada para cancelamento. Status: ' . $cStat . ' - ' . $xMotivo);
            }

            $nProt = $consulta['protocolo']['nProt'] ?? '';
            if (empty($nProt)) {
                throw new Exception('Protocolo de autorização não encontrado na consulta.');
            }

            // Criar objeto mínimo para geração do evento
            $nfecomStub = new stdClass();
            $nfecomStub->nfc_ch_nfcom = $chaveLimpa;
            $nfecomStub->nfc_n_prot = $nProt;
            $nfecomStub->nfc_cnpj_emit = $this->extrairCnpjDaChave($chaveLimpa);
            $nfecomStub->nfc_tipo_ambiente = $ambiente;

            if (empty($nfecomStub->nfc_cnpj_emit)) {
                throw new Exception('Não foi possível identificar o CNPJ do emitente pela chave.');
            }

            $eventoCancelamento = $this->gerarEventoCancelamento($nfecomStub, $justificativa);
            $eventoAssinado = $this->assinarEventoNFCom($configFiscal, $eventoCancelamento);
            $retornoCancelamento = $nfcomService->sendEvent($eventoAssinado, $ambiente);

            if (isset($retornoCancelamento['error'])) {
                throw new Exception('Erro ao cancelar na SEFAZ: ' . $retornoCancelamento['error']);
            }

            $cStat = $retornoCancelamento['cStat'] ?? '999';
            if ($cStat == '135') {
                $protocoloCancelamento = $retornoCancelamento['nProt'] ?? '';
                $motivo = $retornoCancelamento['xMotivo'] ?? 'Cancelamento autorizado';

                // Atualizar registro local se existir
                $registro = $this->db->get_where('nfecom_capa', ['nfc_ch_nfcom' => $chaveLimpa])->row();
                if ($registro) {
                    $dadosAtualizacao = [
                        'nfc_status' => 7,
                        'nfc_x_motivo' => 'Cancelada: ' . $justificativa,
                        'nfc_c_stat' => $cStat,
                        'nfc_n_prot_canc' => $protocoloCancelamento
                    ];
                    $this->Nfecom_model->edit('nfecom_capa', $dadosAtualizacao, 'nfc_id', $registro->nfc_id);
                    $this->registrarProtocolo($registro->nfc_id, $protocoloCancelamento, 'CANCELAMENTO', $motivo);
                }

                $msg = 'NFCom cancelada com sucesso na SEFAZ. Protocolo: ' . $protocoloCancelamento;
                if ($isAjax) {
                    echo json_encode(['success' => true, 'message' => $msg]);
                    return;
                }
                $this->session->set_flashdata('success', $msg);
                redirect(site_url('nfecom'));
                return;
            }

            $motivo = $retornoCancelamento['xMotivo'] ?? 'Erro desconhecido';
            throw new Exception('Cancelamento rejeitado pela SEFAZ: ' . $cStat . ' - ' . $motivo);
        } catch (Exception $e) {
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                return;
            }
            $this->session->set_flashdata('error', $e->getMessage());
            redirect(site_url('nfecom'));
        }
    }

    /**
     * Extrai o CNPJ da chave de acesso (posição padrão NFCom).
     */
    private function extrairCnpjDaChave($chave)
    {
        $chave = preg_replace('/\D/', '', (string) $chave);
        if (strlen($chave) !== 44) {
            return '';
        }
        // cUF(2) + AAMM(4) => CNPJ inicia na posição 6 (0-based) e tem 14 dígitos
        $cnpj = substr($chave, 6, 14);
        return ctype_digit($cnpj) ? $cnpj : '';
    }

    private function gerarEventoCancelamento($nfecom, $justificativa)
    {
        // Gerar XML do evento de cancelamento conforme schema evCancNFCom_v1.00.xsd
        // O Id deve seguir o padrão: "id" + tpEvento (6 dígitos) + chave NFCom (44 dígitos) + nSeqEvento (3 dígitos)
        $tpEvento = '110111'; // Código do evento de cancelamento
        
        // Extrair apenas os dígitos numéricos da chave
        $chaveOriginal = trim($nfecom->nfc_ch_nfcom ?? '');
        // Remover TODOS os caracteres não numéricos (espaços, hífens, pontos, etc.)
        $chaveNFCom = preg_replace('/[^0-9]/', '', $chaveOriginal);
        
        // Log para debug
        log_message('info', 'Chave original NFCom: [' . $chaveOriginal . '] (tamanho: ' . strlen($chaveOriginal) . ')');
        log_message('info', 'Chave após remoção de não numéricos: [' . $chaveNFCom . '] (tamanho: ' . strlen($chaveNFCom) . ')');
        
        // Garantir que a chave tenha exatamente 44 dígitos
        if (strlen($chaveNFCom) > 44) {
            // Se tiver mais de 44 dígitos, pegar apenas os últimos 44
            $chaveNFCom = substr($chaveNFCom, -44);
            log_message('info', 'Chave NFCom truncada para 44 dígitos: ' . $chaveNFCom);
        } elseif (strlen($chaveNFCom) < 44) {
            // Se tiver menos, completar com zeros à esquerda
            $chaveNFCom = str_pad($chaveNFCom, 44, '0', STR_PAD_LEFT);
            log_message('info', 'Chave NFCom preenchida para 44 dígitos: ' . $chaveNFCom);
        }
        
        // Validar que a chave tem exatamente 44 dígitos antes de gerar o Id
        if (strlen($chaveNFCom) !== 44) {
            throw new Exception('Chave NFCom não tem 44 dígitos após normalização. Tamanho: ' . strlen($chaveNFCom) . ', Chave original: ' . $chaveOriginal . ', Chave limpa: ' . $chaveNFCom);
        }
        
        // Validar que a chave contém apenas dígitos
        if (!ctype_digit($chaveNFCom)) {
            throw new Exception('Chave NFCom contém caracteres não numéricos após normalização: ' . $chaveNFCom);
        }
        
        $nSeqEvento = '001'; // Primeiro evento de cancelamento
        
        // Validar tamanhos antes de concatenar
        if (strlen($tpEvento) !== 6) {
            throw new Exception('tpEvento deve ter 6 dígitos. Valor: [' . $tpEvento . '], Tamanho: ' . strlen($tpEvento));
        }
        if (strlen($chaveNFCom) !== 44) {
            throw new Exception('Chave NFCom deve ter 44 dígitos. Valor: [' . $chaveNFCom . '], Tamanho: ' . strlen($chaveNFCom));
        }
        if (strlen($nSeqEvento) !== 3) {
            throw new Exception('nSeqEvento deve ter 3 dígitos. Valor: [' . $nSeqEvento . '], Tamanho: ' . strlen($nSeqEvento));
        }
        
        // O padrão do schema é "ID" (maiúsculas) + 53 dígitos = 55 caracteres total
        // Formato: "ID" + tpEvento (6) + chave NFCom (44) + nSeqEvento (3)
        $idEvento = 'ID' . $tpEvento . $chaveNFCom . $nSeqEvento;
        
        // Validar que o Id tem exatamente 55 caracteres (2 + 6 + 44 + 3)
        // O padrão do schema é "ID" (maiúsculas) + 53 dígitos = 55 caracteres total
        $tamanhoEsperado = 2 + 6 + 44 + 3; // 55
        if (strlen($idEvento) !== $tamanhoEsperado) {
            throw new Exception('Id do evento não tem ' . $tamanhoEsperado . ' caracteres. Tamanho: ' . strlen($idEvento) . ', Id: [' . $idEvento . '], Componentes: ID(' . strlen('ID') . ') + tpEvento(' . strlen($tpEvento) . ') + chave(' . strlen($chaveNFCom) . ') + nSeq(' . strlen($nSeqEvento) . ')');
        }
        
        log_message('info', 'Id do evento de cancelamento gerado: [' . $idEvento . '] (tamanho: ' . strlen($idEvento) . ')');
        
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
        $xml .= '    <tpAmb>' . ($nfecom->nfc_tipo_ambiente ?? 2) . '</tpAmb>' . "\n";
        $xml .= '    <CNPJ>' . preg_replace('/\D/', '', $nfecom->nfc_cnpj_emit) . '</CNPJ>' . "\n";
        $xml .= '    <chNFCom>' . $nfecom->nfc_ch_nfcom . '</chNFCom>' . "\n";
        $xml .= '    <dhEvento>' . $dhEvento . '</dhEvento>' . "\n";
        $xml .= '    <tpEvento>' . $tpEvento . '</tpEvento>' . "\n";
        $xml .= '    <nSeqEvento>' . $nSeqEvento . '</nSeqEvento>' . "\n";
        // Garantir que o protocolo tenha exatamente 16 dígitos (padrão TProt)
        $nProt = preg_replace('/\D/', '', $nfecom->nfc_n_prot); // Remover caracteres não numéricos
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
        $certificate = \NFePHP\Common\Certificate::readPfx($configFiscal->cer_arquivo, $configFiscal->cer_senha);
        
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
            'nfc_id' => $nfecomId,
            'prt_numero_protocolo' => $numeroProtocolo,
            'prt_tipo' => $tipo,
            'prt_motivo' => $motivo,
            'prt_data' => $data ?: date('Y-m-d H:i:s'),
        ]);
    }

    private function getConfiguracaoNfcom()
    {
        $empresa = $this->db->limit(1)->get('empresas')->row();
        if (!$empresa) {
            return null;
        }

        return $this->ConfiguracoesFiscais_model->getByTipo($empresa->emp_id, 'NFCOM');
    }

    private function incrementarSequenciaNfcom()
    {
        $empresa = $this->db->limit(1)->get('empresas')->row();
        if (!$empresa) {
            return;
        }

        $this->ConfiguracoesFiscais_model->incrementarNumero($empresa->emp_id, 'NFCOM');
    }

    private function getCodigoUf($uf)
    {
        if (empty($uf)) {
            return null;
        }

        $this->db->select('est_codigo_uf');
        $this->db->from('estados');
        $this->db->where('est_uf', $uf);
        $row = $this->db->get()->row();

        return $row ? $row->est_codigo_uf : null;
    }


    private function buildInfoComplementar($data, $valorBruto, $comissaoAgencia, $valorLiquido, $mensagensFiscais = [])
    {
        // nfc_inf_cpl deve conter: Observação digitada pelo usuário + Mensagens fiscais das classificações fiscais
        // Separadas por ponto e vírgula
        $info = '';

        // Adicionar observação digitada pelo usuário (se houver)
        $observacaoDigitada = isset($data['observacoes']) ? trim($data['observacoes']) : '';
        if (!empty($observacaoDigitada)) {
            // Substituir quebras de linha por ponto e vírgula e espaço
            $observacaoDigitada = str_replace(["\r\n", "\r", "\n"], '; ', $observacaoDigitada);
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
        $cnpj = preg_replace('/\D/', '', $data['nfc_cnpj_emit']);
        $chave = $data['nfc_cuf'] . date('ym', strtotime($data['nfc_dhemi'])) .
            $cnpj . $data['nfc_mod'] .
            str_pad($data['nfc_serie'], 3, '0', STR_PAD_LEFT) .
            str_pad($data['nfc_nnf'], 9, '0', STR_PAD_LEFT) .
            $data['nfc_tp_emis'] .
            $data['nfc_n_site_autoriz'] .
            str_pad($data['nfc_cnf'], 7, '0', STR_PAD_LEFT);

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
        $cnpj = preg_replace('/\D/', '', $data['nfc_cnpj_emit']);

        // Gerar chave da NFCom (44 dígitos)
        // cUF(2)+AAMM(4)+CNPJ(14)+mod(2)+serie(3)+nNF(9)+tpEmis(1)+nSiteAutoriz(1)+cNF(7)+cDV(1)
        $chave = $data['nfc_cuf'] .
            date('ym', strtotime($data['nfc_dhemi'])) .
            $cnpj .
            $data['nfc_mod'] .
            str_pad($data['nfc_serie'], 3, '0', STR_PAD_LEFT) .
            str_pad($data['nfc_nnf'], 9, '0', STR_PAD_LEFT) .
            $data['nfc_tp_emis'] .
            $data['nfc_n_site_autoriz'] .
            str_pad($data['nfc_cnf'], 7, '0', STR_PAD_LEFT) .
            $data['nfc_cdv'];

        return $chave;
    }

    private function generateNfecomXml($nfecom, $itens)
    {
        // Implementação básica da geração do XML
        // Em produção, deve seguir o schema oficial da NFCom
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<nfcomProc xmlns="http://www.portalfiscal.inf.br/nfcom" versao="1.00">' . "\n";
        $xml .= '<NFCom xmlns="http://www.portalfiscal.inf.br/nfcom">' . "\n";
        $xml .= '<infNFCom Id="NFCom' . $nfecom->nfc_ch_nfcom . '" versao="1.00">' . "\n";

        // Identificação
        $xml .= '<ide>' . "\n";
        $xml .= '<cUF>' . $nfecom->nfc_cuf . '</cUF>' . "\n";
        $xml .= '<tpAmb>' . $nfecom->nfc_tipo_ambiente . '</tpAmb>' . "\n";
        $xml .= '<mod>' . $nfecom->nfc_mod . '</mod>' . "\n";
        $xml .= '<serie>' . $nfecom->nfc_serie . '</serie>' . "\n";
        $xml .= '<nNF>' . $nfecom->nfc_nnf . '</nNF>' . "\n";
        $xml .= '<cNF>' . $nfecom->nfc_cnf . '</cNF>' . "\n";
        $xml .= '<cDV>' . $nfecom->nfc_cdv . '</cDV>' . "\n";
        $xml .= '<dhEmi>' . date('Y-m-d\TH:i:sP', strtotime($nfecom->nfc_dhemi)) . '</dhEmi>' . "\n";
        $xml .= '<tpEmis>' . $nfecom->nfc_tp_emis . '</tpEmis>' . "\n";
        $xml .= '<nSiteAutoriz>' . $nfecom->nfc_n_site_autoriz . '</nSiteAutoriz>' . "\n";
        $xml .= '<cMunFG>' . $nfecom->nfc_c_mun_fg . '</cMunFG>' . "\n";
        $xml .= '<finNFCom>' . $nfecom->nfc_fin_nfcom . '</finNFCom>' . "\n";
        $xml .= '<tpFat>' . $nfecom->nfc_tp_fat . '</tpFat>' . "\n";
        $xml .= '<verProc>' . $nfecom->nfc_ver_proc . '</verProc>' . "\n";
        $xml .= '</ide>' . "\n";

        // Emitente
        $xml .= '<emit>' . "\n";
        $xml .= '<CNPJ>' . $nfecom->nfc_cnpj_emit . '</CNPJ>' . "\n";
        $xml .= '<IE>' . $nfecom->nfc_ie_emit . '</IE>' . "\n";
        $xml .= '<CRT>' . $nfecom->nfc_crt_emit . '</CRT>' . "\n";
        $xml .= '<xNome>' . htmlspecialchars($nfecom->nfc_x_nome_emit) . '</xNome>' . "\n";
        if ($nfecom->nfc_x_fant_emit) {
            $xml .= '<xFant>' . htmlspecialchars($nfecom->nfc_x_fant_emit) . '</xFant>' . "\n";
        }
        $xml .= '<enderEmit>' . "\n";
        $xml .= '<xLgr>' . htmlspecialchars($nfecom->nfc_x_lgr_emit) . '</xLgr>' . "\n";
        if ($nfecom->nfc_nro_emit) {
            $xml .= '<nro>' . htmlspecialchars($nfecom->nfc_nro_emit) . '</nro>' . "\n";
        }
        if ($nfecom->nfc_x_cpl_emit) {
            $xml .= '<xCpl>' . htmlspecialchars($nfecom->nfc_x_cpl_emit) . '</xCpl>' . "\n";
        }
        $xml .= '<xBairro>' . htmlspecialchars($nfecom->nfc_x_bairro_emit) . '</xBairro>' . "\n";
        $xml .= '<cMun>' . $nfecom->nfc_c_mun_emit . '</cMun>' . "\n";
        $xml .= '<xMun>' . htmlspecialchars($nfecom->nfc_x_mun_emit) . '</xMun>' . "\n";
        $xml .= '<CEP>' . $nfecom->nfc_cep_emit . '</CEP>' . "\n";
        $xml .= '<UF>' . $nfecom->nfc_uf_emit . '</UF>' . "\n";
        if ($nfecom->nfc_fone_emit) {
            $xml .= '<fone>' . $nfecom->nfc_fone_emit . '</fone>' . "\n";
        }
        $xml .= '</enderEmit>' . "\n";
        $xml .= '</emit>' . "\n";

        // Destinatário
        $xml .= '<dest>' . "\n";
        $xml .= '<xNome>' . htmlspecialchars($nfecom->nfc_x_nome_dest) . '</xNome>' . "\n";
        $xml .= '<CNPJ>' . $nfecom->nfc_cnpj_dest . '</CNPJ>' . "\n";
        $xml .= '<indIEDest>' . $nfecom->nfc_ind_ie_dest . '</indIEDest>' . "\n";
        $xml .= '<enderDest>' . "\n";
        $xml .= '<xLgr>' . htmlspecialchars($nfecom->nfc_x_lgr_dest) . '</xLgr>' . "\n";
        if ($nfecom->nfc_nro_dest) {
            $xml .= '<nro>' . htmlspecialchars($nfecom->nfc_nro_dest) . '</nro>' . "\n";
        }
        $xml .= '<xBairro>' . htmlspecialchars($nfecom->nfc_x_bairro_dest) . '</xBairro>' . "\n";
        $xml .= '<cMun>' . $nfecom->nfc_c_mun_dest . '</cMun>' . "\n";
        $xml .= '<xMun>' . htmlspecialchars($nfecom->nfc_x_mun_dest) . '</xMun>' . "\n";
        $xml .= '<CEP>' . $nfecom->nfc_cep_dest . '</CEP>' . "\n";
        $xml .= '<UF>' . $nfecom->nfc_uf_dest . '</UF>' . "\n";
        $xml .= '</enderDest>' . "\n";
        $xml .= '</dest>' . "\n";

        // Assinante
        $xml .= '<assinante>' . "\n";
        $xml .= '<iCodAssinante>' . $nfecom->nfc_i_cod_assinante . '</iCodAssinante>' . "\n";
        $xml .= '<tpAssinante>' . $nfecom->nfc_tp_assinante . '</tpAssinante>' . "\n";
        $xml .= '<tpServUtil>' . $nfecom->nfc_tp_serv_util . '</tpServUtil>' . "\n";
        $xml .= '<nContrato>' . $nfecom->nfc_n_contrato . '</nContrato>' . "\n";
        $xml .= '<dContratoIni>' . $nfecom->nfc_d_contrato_ini . '</dContratoIni>' . "\n";
        $xml .= '</assinante>' . "\n";

        // Itens
        foreach ($itens as $item) {
            $xml .= '<det nItem="' . $item->nfi_n_item . '">' . "\n";
            $xml .= '<prod>' . "\n";
            $xml .= '<cProd>' . $item->nfi_c_prod . '</cProd>' . "\n";
            $xml .= '<xProd>' . htmlspecialchars($item->nfi_x_prod) . '</xProd>' . "\n";
            $xml .= '<cClass>' . $item->nfi_c_class . '</cClass>' . "\n";
            $xml .= '<CFOP>' . $item->nfi_cfop . '</CFOP>' . "\n";
            $xml .= '<uMed>' . $item->nfi_u_med . '</uMed>' . "\n";
            $xml .= '<qFaturada>' . number_format($item->nfi_q_faturada, 4, '.', '') . '</qFaturada>' . "\n";
            $xml .= '<vItem>' . number_format($item->nfi_v_item, 2, '.', '') . '</vItem>' . "\n";
            $xml .= '<vDesc>' . number_format($item->nfi_v_desc, 2, '.', '') . '</vDesc>' . "\n";
            $xml .= '<vOutro>' . number_format($item->nfi_v_outro, 2, '.', '') . '</vOutro>' . "\n";
            $xml .= '<vProd>' . number_format($item->nfi_v_prod, 2, '.', '') . '</vProd>' . "\n";
            $xml .= '</prod>' . "\n";
            $xml .= '<imposto>' . "\n";
            
            // ICMS - Estrutura dinâmica baseada no CST e valores do banco
            $cstIcms = $item->nfi_cst_icms ?? '';
            $baseIcms = floatval($item->nfi_v_bc_icms ?? 0);
            $aliqIcms = floatval($item->nfi_p_icms ?? 0);
            $valorIcms = floatval($item->nfi_v_icms ?? 0);
            $valorIcmsDeson = floatval($item->nfi_v_icms_deson ?? 0);
            $motDesIcms = $item->nfi_mot_des_icms ?? null;
            
            // Determinar qual tag ICMS usar baseado no CST
            // CST 00, 10, 20, 30, 70: têm base e valor
            // CST 40, 41, 50, 51, 60: isento/não tributado (sem base/valor)
            if (in_array($cstIcms, ['00', '10', '20', '30', '70']) && ($baseIcms > 0 || $valorIcms > 0)) {
                // ICMS com base e valor
                if ($cstIcms == '00') {
                    $xml .= '<ICMS00><CST>' . $cstIcms . '</CST><vBC>' . number_format($baseIcms, 2, '.', '') . '</vBC><pICMS>' . number_format($aliqIcms, 2, '.', '') . '</pICMS><vICMS>' . number_format($valorIcms, 2, '.', '') . '</vICMS></ICMS00>' . "\n";
                } elseif ($cstIcms == '10') {
                    $xml .= '<ICMS10><CST>' . $cstIcms . '</CST><vBC>' . number_format($baseIcms, 2, '.', '') . '</vBC><pICMS>' . number_format($aliqIcms, 2, '.', '') . '</pICMS><vICMS>' . number_format($valorIcms, 2, '.', '') . '</vICMS></ICMS10>' . "\n";
                } elseif ($cstIcms == '20') {
                    $xml .= '<ICMS20><CST>' . $cstIcms . '</CST><vBC>' . number_format($baseIcms, 2, '.', '') . '</vBC><pICMS>' . number_format($aliqIcms, 2, '.', '') . '</pICMS><vICMS>' . number_format($valorIcms, 2, '.', '') . '</vICMS></ICMS20>' . "\n";
                } elseif ($cstIcms == '30') {
                    $xml .= '<ICMS30><CST>' . $cstIcms . '</CST></ICMS30>' . "\n";
                } elseif ($cstIcms == '70') {
                    $xml .= '<ICMS70><CST>' . $cstIcms . '</CST><vBC>' . number_format($baseIcms, 2, '.', '') . '</vBC><pICMS>' . number_format($aliqIcms, 2, '.', '') . '</pICMS><vICMS>' . number_format($valorIcms, 2, '.', '') . '</vICMS></ICMS70>' . "\n";
                }
            } else {
                // ICMS isento/não tributado (CST 40, 41, 50, 51, 60 ou sem valores)
                $xml .= '<ICMS40><CST>' . $cstIcms . '</CST></ICMS40>' . "\n";
            }
            
            // ICMS ST (se houver valores)
            $baseIcmsSt = floatval($item->nfi_v_bc_icms_st ?? 0);
            $valorIcmsSt = floatval($item->nfi_v_icms_st ?? 0);
            if ($baseIcmsSt > 0 || $valorIcmsSt > 0) {
                $xml .= '<ICMSST><vBCST>' . number_format($baseIcmsSt, 2, '.', '') . '</vBCST><pICMSST>' . number_format(floatval($item->nfi_p_icms_st ?? 0), 2, '.', '') . '</pICMSST><vICMSST>' . number_format($valorIcmsSt, 2, '.', '') . '</vICMSST></ICMSST>' . "\n";
            }
            
            // FCP (se houver valores)
            $baseFcp = floatval($item->nfi_v_bc_fcp ?? 0);
            $valorFcp = floatval($item->nfi_v_fcp ?? 0);
            if ($baseFcp > 0 || $valorFcp > 0) {
                $xml .= '<FCP><vBCFCP>' . number_format($baseFcp, 2, '.', '') . '</vBCFCP><pFCP>' . number_format(floatval($item->nfi_p_fcp ?? 0), 2, '.', '') . '</pFCP><vFCP>' . number_format($valorFcp, 2, '.', '') . '</vFCP></FCP>' . "\n";
            }
            
            $xml .= '<PIS><CST>' . $item->nfi_cst_pis . '</CST><vBC>' . number_format($item->nfi_v_bc_pis, 2, '.', '') . '</vBC><pPIS>' . number_format($item->nfi_p_pis, 2, '.', '') . '</pPIS><vPIS>' . number_format($item->nfi_v_pis, 2, '.', '') . '</vPIS></PIS>' . "\n";
            $xml .= '<COFINS><CST>' . $item->nfi_cst_cofins . '</CST><vBC>' . number_format($item->nfi_v_bc_cofins, 2, '.', '') . '</vBC><pCOFINS>' . number_format($item->nfi_p_cofins, 2, '.', '') . '</pCOFINS><vCOFINS>' . number_format($item->nfi_v_cofins, 2, '.', '') . '</vCOFINS></COFINS>' . "\n";
            $xml .= '<FUST><vBC>' . number_format($item->nfi_v_bc_fust, 2, '.', '') . '</vBC><pFUST>' . number_format($item->nfi_p_fust, 2, '.', '') . '</pFUST><vFUST>' . number_format($item->nfi_v_fust, 2, '.', '') . '</vFUST></FUST>' . "\n";
            $xml .= '<FUNTTEL><vBC>' . number_format($item->nfi_v_bc_funtel, 2, '.', '') . '</vBC><pFUNTTEL>' . number_format($item->nfi_p_funtel, 2, '.', '') . '</pFUNTTEL><vFUNTTEL>' . number_format($item->nfi_v_funtel, 2, '.', '') . '</vFUNTTEL></FUNTTEL>' . "\n";
            $xml .= '<retTrib><vRetPIS>0.00</vRetPIS><vRetCofins>0.00</vRetCofins><vRetCSLL>0.00</vRetCSLL><vBCIRRF>' . number_format($item->nfi_v_bc_irrf, 2, '.', '') . '</vBCIRRF><vIRRF>' . number_format($item->nfi_v_irrf, 2, '.', '') . '</vIRRF></retTrib>' . "\n";
            $xml .= '</imposto>' . "\n";
            $xml .= '</det>' . "\n";
        }

        // Calcular total do IRRF somando os itens (conforme rejeição 685)
        // SEMPRE usar a soma dos itens, não o valor do banco
        $totalIrrfItens = 0;
        foreach ($itens as $item) {
            $totalIrrfItens += floatval($item->nfi_v_irrf ?? 0);
        }
        
        // Arredondar para 2 casas decimais para evitar problemas de precisão
        $totalIrrfItens = round($totalIrrfItens, 2);
        
        // SEMPRE atualizar o banco com a soma dos itens antes de gerar o XML
        $irrfBanco = floatval($nfecom->nfc_v_irrf ?? 0);
        if (abs($totalIrrfItens - $irrfBanco) > 0.001) { // Tolerância menor (0.001 para garantir precisão)
            // Atualizar total do IRRF no banco com a soma dos itens
            $this->Nfecom_model->edit('nfecom_capa', ['nfc_v_irrf' => $totalIrrfItens], 'nfc_id', $id);
            log_message('info', 'Total do IRRF atualizado na geração do XML: R$ ' . number_format($irrfBanco, 2, ',', '.') . ' → R$ ' . number_format($totalIrrfItens, 2, ',', '.') . ' (soma dos itens)');
            // Recarregar nfecom para ter o valor atualizado
            $nfecom = $this->Nfecom_model->getById($id);
        }
        
        // SEMPRE usar o valor calculado (soma dos itens) no XML, não o do banco
        $irrfParaXml = $totalIrrfItens;
        
        // Totais
        $xml .= '<total>' . "\n";
        $xml .= '<vProd>' . number_format($nfecom->nfc_v_prod, 2, '.', '') . '</vProd>' . "\n";
        // Totais de ICMS - APENAS valores do banco, sem valores fixos
        $xml .= '<ICMSTot>' . "\n";
        $xml .= '<vBC>' . number_format(floatval($nfecom->nfc_v_bc_icms ?? 0), 2, '.', '') . '</vBC>' . "\n";
        $xml .= '<vICMS>' . number_format(floatval($nfecom->nfc_v_icms ?? 0), 2, '.', '') . '</vICMS>' . "\n";
        $xml .= '<vICMSDeson>' . number_format(floatval($nfecom->nfc_v_icms_deson ?? 0), 2, '.', '') . '</vICMSDeson>' . "\n";
        $xml .= '<vFCP>' . number_format(floatval($nfecom->nfc_v_fcp ?? 0), 2, '.', '') . '</vFCP>' . "\n";
        $xml .= '</ICMSTot>' . "\n";
        $xml .= '<vCOFINS>' . number_format($nfecom->nfc_v_cofins, 2, '.', '') . '</vCOFINS>' . "\n";
        $xml .= '<vPIS>' . number_format($nfecom->nfc_v_pis, 2, '.', '') . '</vPIS>' . "\n";
        $xml .= '<vFUNTTEL>' . number_format($nfecom->nfc_v_funtel, 2, '.', '') . '</vFUNTTEL>' . "\n";
        $xml .= '<vFUST>' . number_format($nfecom->nfc_v_fust, 2, '.', '') . '</vFUST>' . "\n";
        $xml .= '<vRetTribTot><vRetPIS>' . number_format($nfecom->nfc_v_ret_pis, 2, '.', '') . '</vRetPIS><vRetCofins>' . number_format($nfecom->nfc_v_ret_cofins, 2, '.', '') . '</vRetCofins><vRetCSLL>' . number_format($nfecom->nfc_v_ret_csll, 2, '.', '') . '</vRetCSLL><vIRRF>' . number_format($irrfParaXml, 2, '.', '') . '</vIRRF></vRetTribTot>' . "\n";
        $xml .= '<vDesc>' . number_format($nfecom->nfc_v_desc, 2, '.', '') . '</vDesc>' . "\n";
        $xml .= '<vOutro>' . number_format($nfecom->nfc_v_outro, 2, '.', '') . '</vOutro>' . "\n";
        $xml .= '<vNF>' . number_format($nfecom->nfc_v_nf, 2, '.', '') . '</vNF>' . "\n";
        $xml .= '</total>' . "\n";

        // Grupo de Faturamento
        // Competência baseada no período fim (formato YYYYMM)
        // Exemplo: se período fim for 31-12-2025, competência será 202512
        $competencia = $nfecom->nfc_compet_fat;
        if (empty($competencia) && !empty($nfecom->nfc_d_per_uso_fim)) {
            $competencia = date('Ym', strtotime($nfecom->nfc_d_per_uso_fim));
        } elseif (empty($competencia) && !empty($nfecom->nfc_d_contrato_ini)) {
            $competencia = date('Ym', strtotime($nfecom->nfc_d_contrato_ini));
        } elseif (empty($competencia)) {
            $competencia = date('Ym');
        }
        // Garantir formato YYYYMM (sem hífen)
        $competencia = str_replace('-', '', $competencia);
        if (strlen($competencia) == 7 && strpos($competencia, '-') !== false) {
            $competencia = str_replace('-', '', $competencia);
        }
        
        $xml .= '<gFat>' . "\n";
        $xml .= '<CompetFat>' . $competencia . '</CompetFat>' . "\n";
        $xml .= '<dVencFat>' . $nfecom->nfc_d_venc_fat . '</dVencFat>' . "\n";
        // PERÍODO DE USO: Usar exatamente o valor salvo no banco (respeitando o informado pelo usuário)
        $xml .= '<dPerUsoIni>' . $nfecom->nfc_d_per_uso_ini . '</dPerUsoIni>' . "\n";
        $xml .= '<dPerUsoFim>' . $nfecom->nfc_d_per_uso_fim . '</dPerUsoFim>' . "\n";
        $xml .= '<codBarras>' . $nfecom->nfc_cod_barras . '</codBarras>' . "\n";
        $xml .= '</gFat>' . "\n";

        // Informações complementares
        if ($nfecom->nfc_inf_cpl) {
            $xml .= '<infAdic>' . "\n";
            $xml .= '<infCpl>' . htmlspecialchars($nfecom->nfc_inf_cpl) . '</infCpl>' . "\n";
            $xml .= '</infAdic>' . "\n";
        }

        $xml .= '</infNFCom>' . "\n";

        // Adicionar QRCode Suplementar
        $urlQrCode = "https://dfe-portal.svrs.rs.gov.br/NFCom/QRCode";
        $params = "chNFCom=" . $nfecom->nfc_ch_nfcom . "&tpAmb=" . $nfecom->nfc_tipo_ambiente;
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

        $this->db->select('p.*, e.end_logradouro as logradouro, e.end_numero as numero, e.end_complemento as complemento, e.end_cep as cep, m.mun_nome as municipio_nome, m.mun_ibge, es.est_uf as estado_uf');
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.pes_id = c.pes_id');
        $this->db->join('enderecos e', 'e.pes_id = p.pes_id AND e.end_padrao = 1', 'left'); // Endereço padrão
        $this->db->join('municipios m', 'm.mun_id = e.mun_id', 'left');
        $this->db->join('estados es', 'es.est_id = e.est_id', 'left');
        $this->db->where('c.cln_id', $id);
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $cliente = $query->row();

            $response = [
                'nomeCliente' => $cliente->pes_fisico_juridico == 'F' ? $cliente->pes_nome : ($cliente->pes_razao_social ?: $cliente->pes_nome),
                'cnpjCliente' => $cliente->pes_cpfcnpj,
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
            // Primeiro, buscar o pes_id do cliente
            $this->db->select('pes_id');
            $this->db->from('clientes');
            $this->db->where('cln_id', $clienteId);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $clienteQuery = $this->db->get();

            if ($clienteQuery->num_rows() == 0) {
                echo json_encode(['error' => 'Cliente ID ' . $clienteId . ' não encontrado']);
                return;
            }

            $cliente = $clienteQuery->row();
            $pesId = $cliente->pes_id;

            // Query simplificada - buscar apenas endereços básicos
            $this->db->select('end_id as id, end_logradouro as logradouro, end_numero as numero, end_complemento as complemento, end_cep as cep, end_padrao as enderecoPadrao');
            $this->db->from('enderecos');
            $this->db->where('pes_id', $pesId);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $this->db->order_by('end_padrao', 'desc');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $enderecos = $query->result_array();

                // Debug: mostrar endereços encontrados
                log_message('debug', 'Endereços encontrados para pes_id ' . $pesId . ': ' . json_encode($enderecos));

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
            // Buscar pes_id do cliente
            $this->db->select('pes_id');
            $this->db->from('clientes');
            $this->db->where('cln_id', $clienteId);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $clienteQuery = $this->db->get();

            if ($clienteQuery->num_rows() == 0) {
                echo json_encode(['error' => 'Cliente ID ' . $clienteId . ' não encontrado']);
                return;
            }

            $cliente = $clienteQuery->row();
            $pesId = $cliente->pes_id;

            // Buscar email (tabela emails, se existir)
            $email = '';
            if ($this->db->table_exists('emails')) {
                $this->db->select('eml_email, eml_tipo');
                $this->db->from('emails');
                $this->db->where('pes_id', $pesId);
                $this->db->order_by("CASE WHEN eml_tipo = 'Geral' THEN 0 ELSE 1 END, eml_id ASC");
                $emailQuery = $this->db->get();
                if ($emailQuery->num_rows() > 0) {
                    $email = $emailQuery->row()->eml_email ?? '';
                }
            } else {
                $this->db->select('pes_email');
                $this->db->from('pessoas');
                $this->db->where('pes_id', $pesId);
                $pessoaQuery = $this->db->get();
                if ($pessoaQuery->num_rows() > 0) {
                    $email = $pessoaQuery->row()->pes_email ?? '';
                }
            }

            // Buscar telefones por tipo
            $this->db->select('tel_tipo, tel_ddd, tel_numero');
            $this->db->from('telefones');
            $this->db->where('pes_id', $pesId);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $query = $this->db->get();

            $telefone = '';
            $celular = '';

            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $tel) {
                    $numero = trim(($tel['tel_ddd'] ?? '') . ' ' . ($tel['tel_numero'] ?? ''));
                    if (!$numero) {
                        continue;
                    }
                    if ($tel['tel_tipo'] === 'Celular' || $tel['tel_tipo'] === 'Whatsapp') {
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
                'email' => $email,
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
            $this->db->select("c.cln_id as id,
                              CASE
                                WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome
                                ELSE COALESCE(p.pes_nome, p.pes_razao_social)
                              END as text,
                              p.pes_nome as nome_fantasia,
                              p.pes_razao_social as razao_social,
                              p.pes_cpfcnpj as cpf_cnpj,
                              p.pes_codigo as codigo");
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'p.pes_id = c.pes_id', 'left');
            $this->db->where('c.ten_id', $this->session->userdata('ten_id'));

            if (!empty($termo)) {
                $this->db->group_start();
                $this->db->like("CASE
                                WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome
                                ELSE COALESCE(p.pes_nome, p.pes_razao_social)
                              END", $termo);
                $this->db->or_like('p.pes_nome', $termo);
                $this->db->or_like('p.pes_razao_social', $termo);
                $this->db->or_like('p.pes_cpfcnpj', $termo);
                $this->db->or_like('p.pes_codigo', $termo);
                $this->db->group_end();
            }

            $this->db->order_by("CASE WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome ELSE COALESCE(p.pes_nome, p.pes_razao_social) END ASC");
            $this->db->limit($limit, $offset);

            $query = $this->db->get();
            $clientes = $query->result();

            // Contar total para paginação
            $this->db->select('COUNT(*) as total');
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'p.pes_id = c.pes_id', 'left');
            $this->db->where('c.ten_id', $this->session->userdata('ten_id'));

            if (!empty($termo)) {
                $this->db->group_start();
                $this->db->like("CASE
                                WHEN p.pes_fisico_juridico = 'F' THEN p.pes_nome
                                ELSE COALESCE(p.pes_nome, p.pes_razao_social)
                              END", $termo);
                $this->db->or_like('p.pes_nome', $termo);
                $this->db->or_like('p.pes_razao_social', $termo);
                $this->db->or_like('p.pes_cpfcnpj', $termo);
                $this->db->or_like('p.pes_codigo', $termo);
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

    public function previewTributacaoItem()
    {
        try {
            $produtoId = $this->input->post('produto_id');
            $clienteId = $this->input->post('cliente_id');
            $operacaoId = $this->input->post('operacao_id');
            $enderecoId = $this->input->post('endereco_id');
            $valorUnitario = str_replace(',', '.', $this->input->post('valor_unitario'));
            $quantidade = str_replace(',', '.', $this->input->post('quantidade'));
            $valorDesconto = str_replace(',', '.', $this->input->post('v_desc'));
            $valorOutros = str_replace(',', '.', $this->input->post('v_outro'));

            $valorUnitario = floatval($valorUnitario);
            $quantidade = floatval($quantidade);
            $valorDesconto = floatval($valorDesconto);
            $valorOutros = floatval($valorOutros);

            if (!$produtoId || !$clienteId || !$operacaoId) {
                echo json_encode(['success' => false, 'error' => 'Parâmetros obrigatórios ausentes']);
                return;
            }

            $valorItem = $valorUnitario * $quantidade;
            $valorProduto = $valorItem - $valorDesconto + $valorOutros;

            // Verificar se cliente cobra IRRF
            $cobrarIrrf = false;
            $this->db->select('cln_cobrar_irrf');
            $this->db->from('clientes');
            $this->db->where('cln_id', $clienteId);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $clienteQuery = $this->db->get();
            if ($clienteQuery->num_rows() > 0) {
                $cobrarIrrf = !empty($clienteQuery->row()->cln_cobrar_irrf);
            }

            $tributacao = $this->calcularTributacao(
                $produtoId,
                $clienteId,
                $operacaoId,
                $valorUnitario,
                $quantidade,
                'saida',
                $enderecoId
            );

            $pis = [
                'base' => 0,
                'aliquota' => 0,
                'valor' => 0,
                'cst' => null,
            ];
            $cofins = [
                'base' => 0,
                'aliquota' => 0,
                'valor' => 0,
                'cst' => null,
            ];
            $icms = [
                'base' => 0,
                'aliquota' => 0,
                'valor' => 0,
            ];
            $icmsSt = [
                'base' => 0,
                'aliquota' => 0,
                'valor' => 0,
            ];

            if ($tributacao) {
                $pisApi = $tributacao['impostos_federais']['pis'] ?? [];
                $cofinsApi = $tributacao['impostos_federais']['cofins'] ?? [];

                $pis['cst'] = $pisApi['cst'] ?? null;
                $pis['aliquota'] = floatval($pisApi['aliquota'] ?? 0);
                $pis['valor'] = floatval($pisApi['valor'] ?? 0);
                $pis['base'] = floatval($pisApi['base_calculo'] ?? 0);
                $pis['base'] = $this->getBaseCalculoPisCofins($pis['cst'], $pis['base'], $valorProduto);

                $cofins['cst'] = $cofinsApi['cst'] ?? null;
                $cofins['aliquota'] = floatval($cofinsApi['aliquota'] ?? 0);
                $cofins['valor'] = floatval($cofinsApi['valor'] ?? 0);
                $cofins['base'] = floatval($cofinsApi['base_calculo'] ?? 0);
                $cofins['base'] = $this->getBaseCalculoPisCofins($cofins['cst'], $cofins['base'], $valorProduto);

                $impostosEstaduais = $tributacao['impostos_estaduais'] ?? [];
                $icmsApi = $impostosEstaduais['icms'] ?? [];
                $icmsStApi = $impostosEstaduais['icms_st'] ?? [];

                $icms['base'] = floatval($icmsApi['base_calculo'] ?? 0);
                $icms['aliquota'] = floatval($icmsApi['aliquota'] ?? 0);
                $icms['valor'] = floatval($icmsApi['valor'] ?? 0);

                $icmsSt['base'] = floatval($icmsStApi['base_calculo'] ?? 0);
                $icmsSt['aliquota'] = floatval($icmsStApi['aliquota'] ?? 0);
                $icmsSt['valor'] = floatval($icmsStApi['valor'] ?? 0);
            }

            $irrfBase = $cobrarIrrf ? $valorProduto : 0;
            $irrfValor = $cobrarIrrf ? round(($valorProduto * 4.8) / 100, 2) : 0;

            echo json_encode([
                'success' => true,
                'data' => [
                    'valor_produto' => $valorProduto,
                    'icms' => $icms,
                    'icms_st' => $icmsSt,
                    'pis' => $pis,
                    'cofins' => $cofins,
                    'irrf' => [
                        'base' => $irrfBase,
                        'valor' => $irrfValor
                    ],
                ],
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro ao pré-visualizar tributação: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Erro interno do servidor']);
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
            // Primeiro, buscar o pes_id do cliente
            $this->db->select('pes_id');
            $this->db->from('clientes');
            $this->db->where('cln_id', $clienteId);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $clienteQuery = $this->db->get();

            if ($clienteQuery->num_rows() == 0) {
                echo json_encode(['error' => 'Cliente ID ' . $clienteId . ' não encontrado']);
                return;
            }

            $cliente = $clienteQuery->row();
            $pesId = $cliente->pes_id;

            // Buscar contratos ativos do cliente
            $this->db->select('ctr_id, ctr_numero, ctr_data_inicio, ctr_data_fim, ctr_tipo_assinante, ctr_observacao');
            $this->db->from('contratos');
            $this->db->where('pes_id', $pesId);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $this->db->where('ctr_situacao', 1); // Apenas contratos ativos
            $this->db->order_by('ctr_data_inicio', 'desc');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $contratos = $query->result_array();
                
                // Formatar datas para o formato esperado pelo frontend
                foreach ($contratos as &$contrato) {
                    if ($contrato['ctr_data_inicio']) {
                        $contrato['ctr_data_inicio'] = date('Y-m-d', strtotime($contrato['ctr_data_inicio']));
                    }
                    if ($contrato['ctr_data_fim']) {
                        $contrato['ctr_data_fim'] = date('Y-m-d', strtotime($contrato['ctr_data_fim']));
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
     * Buscar contrato por código (autocomplete)
     * GET: /nfecom/buscarContratoPorCodigo?term={codigo}
     */
    public function buscarContratoPorCodigo()
    {
        $termo = $this->input->get('term');
        
        if (!$termo || strlen($termo) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        try {
            $this->load->model('Contratos_model');
            
            // Buscar contratos por número
            $this->db->select('c.ctr_id, c.ctr_numero, c.ctr_data_inicio, c.ctr_data_fim, c.ctr_tipo_assinante, c.ctr_observacao, c.pes_id, p.pes_nome, p.pes_razao_social, p.pes_cpfcnpj, cl.cln_id');
            $this->db->from('contratos c');
            $this->db->join('pessoas p', 'p.pes_id = c.pes_id', 'left');
            $this->db->join('clientes cl', 'cl.pes_id = p.pes_id AND cl.ten_id = c.ten_id', 'left');
            $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
            $this->db->where('c.ctr_situacao', 1); // Apenas contratos ativos
            $this->db->like('c.ctr_numero', $termo);
            $this->db->order_by('c.ctr_data_inicio', 'desc');
            $this->db->limit(20);
            
            $query = $this->db->get();
            $contratos = $query->result();
            
            $resultado = [];
            foreach ($contratos as $contrato) {
                $label = $contrato->ctr_numero;
                if ($contrato->ctr_data_inicio) {
                    $label .= ' - ' . date('d/m/Y', strtotime($contrato->ctr_data_inicio));
                }
                if ($contrato->pes_nome) {
                    $label .= ' (' . $contrato->pes_nome . ')';
                }
                
                $resultado[] = [
                    'id' => $contrato->ctr_id,
                    'label' => $label,
                    'value' => $contrato->ctr_numero,
                    'ctr_id' => $contrato->ctr_id,
                    'ctr_numero' => $contrato->ctr_numero,
                    'ctr_data_inicio' => $contrato->ctr_data_inicio ? date('Y-m-d', strtotime($contrato->ctr_data_inicio)) : null,
                    'ctr_data_fim' => $contrato->ctr_data_fim ? date('Y-m-d', strtotime($contrato->ctr_data_fim)) : null,
                    'ctr_tipo_assinante' => $contrato->ctr_tipo_assinante,
                    'ctr_observacao' => $contrato->ctr_observacao,
                    'cln_id' => $contrato->cln_id,
                    'pes_id' => $contrato->pes_id,
                    'pes_nome' => $contrato->pes_nome,
                    'pes_razao_social' => $contrato->pes_razao_social,
                    'pes_cpfcnpj' => $contrato->pes_cpfcnpj
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar contrato por código: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * Buscar serviços/itens de um contrato
     * GET: /nfecom/getServicosContrato/{contratoId}
     */
    public function getServicosContrato()
    {
        $contratoId = $this->uri->segment(3);

        if (!$contratoId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID do contrato não informado']);
            return;
        }

        try {
            $this->load->model('Contratos_model');
            
            // Buscar itens do contrato
            $itens = $this->Contratos_model->getItensByContratoId($contratoId);
            
            if (empty($itens)) {
                header('Content-Type: application/json');
                echo json_encode([]);
                return;
            }

            // Formatar resposta
            $servicos = [];
            foreach ($itens as $item) {
                // Descobrir a chave primária da tabela produtos
                $primary_key_query = $this->db->query("SHOW KEYS FROM produtos WHERE Key_name = 'PRIMARY'");
                $primary_key = 'idProdutos';
                if ($primary_key_query->num_rows() > 0) {
                    $key_info = $primary_key_query->row();
                    $primary_key = $key_info->Column_name;
                }

                $servicos[] = [
                    'cti_id' => $item->cti_id,
                    'pro_id' => $item->pro_id,
                    'idServicos' => $item->pro_id, // Para compatibilidade com o código existente
                    'nome' => $item->pro_descricao,
                    'pro_descricao' => $item->pro_descricao,
                    'preco' => number_format($item->cti_preco, 2, ',', '.'),
                    'cti_preco' => $item->cti_preco,
                    'quantidade' => $item->cti_quantidade,
                    'cti_quantidade' => $item->cti_quantidade,
                    'observacao' => $item->cti_observacao
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($servicos);
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar serviços do contrato: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * Endpoint AJAX para buscar classificação fiscal automaticamente
     * 
     * Parâmetros via POST:
     * - operacao_comercial_id: ID da operação comercial
     * - cliente_id: ID do cliente (cln_id)
     * - produto_id: ID do produto (pro_id) - opcional para serviços
     * 
     * Retorna JSON com CFOP, CST, CSOSN e cClassTrib
     */
    public function getClassificacaoFiscal()
    {
        header('Content-Type: application/json');

        try {
            $operacaoComercialId = $this->input->post('operacao_comercial_id');
            $clienteId = $this->input->post('cliente_id'); // cln_id
            $produtoId = $this->input->post('produto_id'); // pro_id (opcional)

            if (!$operacaoComercialId || !$clienteId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Operação comercial e cliente são obrigatórios'
                ]);
                return;
            }

            // Buscar pes_id do cliente
            $this->db->select('pes_id');
            $this->db->from('clientes');
            $this->db->where('cln_id', $clienteId);
            $clienteQuery = $this->db->get();
            
            if ($clienteQuery->num_rows() == 0) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Cliente não encontrado'
                ]);
                return;
            }
            
            $pesId = $clienteQuery->row()->pes_id;

            // Buscar emp_id da empresa logada
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
                $pesId, // pes_id do cliente
                $produtoId,
                $empresaId
            );

            if ($resultado) {
                $clfId = $resultado['clf_id'] ?? null;
                $cfop = $resultado['clf_cfop'] ?? null;
                $cst = $resultado['clf_cst'] ?? null;
                $csosn = $resultado['clf_csosn'] ?? null;
                $cClassTrib = $resultado['clf_cclasstrib'] ?? null;
                $tipoIcms = $resultado['clf_tipo_tributacao'] ?? null;
                $mensagemFiscal = $resultado['clf_mensagem'] ?? null;
                
                // Log detalhado para identificação
                log_message('info', '=== CLASSIFICAÇÃO FISCAL ENCONTRADA ===');
                log_message('info', 'clf_id: ' . ($clfId ?? 'N/A'));
                log_message('info', 'opc_id: ' . $operacaoComercialId);
                log_message('info', 'Cliente ID (cln_id): ' . $clienteId);
                log_message('info', 'Cliente pes_id: ' . $pesId);
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
                log_message('info', 'opc_id: ' . $operacaoComercialId);
                log_message('info', 'Cliente ID (cln_id): ' . $clienteId);
                log_message('info', 'Cliente pes_id: ' . $pesId);
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

            $this->db->select('pro_id, pro_descricao, pro_preco_venda, pro_cclass_serv, pro_unid_medida');
            $this->db->from('produtos');
            $this->db->where('pro_tipo', 2);
            $this->db->where('produtos.ten_id', $this->session->userdata('ten_id'));
            if ($q !== '') {
                $this->db->like('pro_descricao', $q);
            }
            $this->db->limit(25);
            $query = $this->db->get();

            $row_set = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $preco = $row['pro_preco_venda'] ?? 0;
                    $row_set[] = [
                        'label' => $row['pro_descricao'],
                        'id' => $row['pro_id'],
                        'preco' => $preco,
                        'cClass' => $row['pro_cclass_serv'],
                        'uMed' => $row['pro_unid_medida']
                    ];
                }
            }

            header('Content-Type: application/json');
            echo json_encode($row_set);
        }
    }
    private function gerarQRCodeNFCom($nfecom, $configFiscal = null)
    {
        $chave = preg_replace('/\D/', '', $nfecom->nfc_ch_nfcom);
        $tpAmb = (int) $nfecom->nfc_tipo_ambiente;

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
            'to' => '17'
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
        $this->db->select('c.*, p.*, e.end_logradouro, e.end_numero, e.end_complemento, e.end_cep, b.bai_nome, m.mun_nome, m.mun_ibge, es.est_uf, d.doc_numero as PES_IE, d.doc_natureza_contribuinte');
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.pes_id = c.pes_id');
        $this->db->join('enderecos e', 'e.pes_id = p.pes_id AND e.end_padrao = 1', 'left');
        $this->db->join('bairros b', 'b.bai_id = e.bai_id', 'left');
        $this->db->join('municipios m', 'm.mun_id = e.mun_id', 'left');
        $this->db->join('estados es', 'es.est_id = e.est_id', 'left');
        $this->db->join('documentos d', "d.pes_id = p.pes_id AND d.doc_tipo_documento = 'Inscrição Estadual'", 'left');
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));

        // Se cln_id estiver disponível, usar ele (mais preciso)
        if (!empty($nfecom->cln_id)) {
            $this->db->where('c.cln_id', $nfecom->cln_id);
        } else {
            // Fallback por CNPJ/CPF se for uma nota antiga sem cln_id
            $cnpjLimpo = preg_replace('/\D/', '', $nfecom->nfc_cnpj_dest);
            $this->db->where('p.pes_cpfcnpj', $cnpjLimpo);
        }

        $cliente = $this->db->get()->row();

        if (!$cliente)
            throw new Exception("Cliente não encontrado.");

        // Usar a UF do emitente para gerar o cUF correto
        $ufEmit = $emitente['enderEmit']['uf'] ?? $nfecom->nfc_uf_emit ?? 'GO';
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
        $temClfId = in_array('clf_id', $fields) || in_array('clf_id', $fields);
        
        foreach ($itens as $it) {
            log_message('info', 'Item NFCom - nfi_n_item: ' . $it->nfi_n_item . ', nfi_c_prod: ' . $it->nfi_c_prod . ', nfi_x_prod: ' . $it->nfi_x_prod);
            
            // Buscar mensagem fiscal se o item tiver clf_id
            if ($temClfId) {
                $clfId = isset($it->clf_id) ? $it->clf_id : (isset($it->clf_id) ? $it->clf_id : null);
                if (!empty($clfId)) {
                    $this->db->select('clf_mensagem');
                    $this->db->from('classificacao_fiscal');
                    $this->db->where('clf_id', $clfId);
                    $this->db->where('ten_id', $this->session->userdata('ten_id'));
                    $clfQuery = $this->db->get();
                    if ($clfQuery->num_rows() > 0) {
                        $clf = $clfQuery->row();
                        if (!empty($clf->clf_mensagem)) {
                            // Evitar duplicatas
                            if (!in_array($clf->clf_mensagem, $mensagensFiscais)) {
                                $mensagensFiscais[] = $clf->clf_mensagem;
                            }
                        }
                    }
                }
            }
            
            $listaItens[] = [
                'nItem' => $it->nfi_n_item,
                'codigo' => $it->nfi_c_prod,
                'descricao' => $it->nfi_x_prod,
                'classificacao_item' => $it->nfi_c_class,
                'cfop' => $it->nfi_cfop,
                'unidade' => $it->nfi_u_med,
                'quantidade' => $it->nfi_q_faturada,
                'valor_unitario' => $it->nfi_v_item,
                'valor_total' => $it->nfi_v_prod,
                'desconto' => $it->nfi_v_desc,
                'outros' => $it->nfi_v_outro,
                'imposto' => [
                    'icms' => [
                        'cst' => $it->nfi_cst_icms,
                        'csosn' => $it->nfi_csosn ?? null, // CSOSN para Simples Nacional
                        // Para CST isento/não tributado (40, 41, 50, 51, 60), zerar base, alíquota e valor
                        'vBC' => in_array($it->nfi_cst_icms, ['40', '41', '50', '51', '60']) ? 0 : floatval($it->nfi_v_bc_icms ?? 0),
                        'pICMS' => in_array($it->nfi_cst_icms, ['40', '41', '50', '51', '60']) ? 0 : floatval($it->nfi_p_icms ?? 0),
                        'vICMS' => in_array($it->nfi_cst_icms, ['40', '41', '50', '51', '60']) ? 0 : floatval($it->nfi_v_icms ?? 0),
                        'vICMSDeson' => floatval($it->nfi_v_icms_deson ?? 0), // Valor ICMS Desonerado
                        'motDesICMS' => $it->nfi_mot_des_icms ?? null // Motivo da Desoneração
                    ],
                    'icms_st' => [
                        'vBCST' => floatval($it->nfi_v_bc_icms_st ?? 0), // Base de cálculo ICMS ST
                        'pICMSST' => floatval($it->nfi_p_icms_st ?? 0), // Alíquota ICMS ST
                        'vICMSST' => floatval($it->nfi_v_icms_st ?? 0), // Valor ICMS ST
                        'vBCSTRet' => floatval($it->nfi_v_bc_st_ret ?? 0), // Base de Cálculo do ST Retido
                        'vICMSSTRet' => floatval($it->nfi_v_icms_st_ret ?? 0), // Valor do ICMS ST Retido
                        'pST' => floatval($it->nfi_p_st ?? 0), // Alíquota do ST
                        'vICMSSubstituto' => floatval($it->nfi_v_icms_subst ?? 0) // Valor do ICMS Próprio do Substituto
                    ],
                    'fcp' => [
                        'vBCFCP' => floatval($it->nfi_v_bc_fcp ?? 0), // Base de cálculo FCP
                        'pFCP' => floatval($it->nfi_p_fcp ?? 0), // Alíquota FCP
                        'vFCP' => floatval($it->nfi_v_fcp ?? 0), // Valor FCP
                        'vFCPST' => floatval($it->nfi_v_fcp_st ?? 0), // Valor do FCP ST
                        'vFCPSTRet' => floatval($it->nfi_v_fcp_st_ret ?? 0) // Valor do FCP ST Retido
                    ],
                    'pis' => [
                        'cst' => $it->nfi_cst_pis,
                        'vBC' => $it->nfi_v_bc_pis,
                        'pPIS' => $it->nfi_p_pis,
                        'vPIS' => $it->nfi_v_pis
                    ],
                    'cofins' => [
                        'cst' => $it->nfi_cst_cofins,
                        'vBC' => $it->nfi_v_bc_cofins,
                        'pCOFINS' => $it->nfi_p_cofins,
                        'vCOFINS' => $it->nfi_v_cofins
                    ],
                    'irrf' => [
                        'vRetPIS' => 0.00, // PIS retido (geralmente zero)
                        'vRetCofins' => 0.00, // COFINS retido (geralmente zero)
                        'vRetCSLL' => 0.00, // CSLL retido (geralmente zero)
                        'vBCIRRF' => floatval($it->nfi_v_bc_irrf ?? 0), // Base de cálculo do IRRF
                        'vIRRF' => floatval($it->nfi_v_irrf ?? 0) // Valor do IRRF retido - OBRIGATÓRIO quando há IRRF no total
                    ]
                ]
            ];
            
            // Log para debug: verificar se IRRF está sendo incluído
            $irrfItem = floatval($it->nfi_v_irrf ?? 0);
            if ($irrfItem > 0) {
                log_message('info', 'Item #' . $it->nfi_n_item . ' - IRRF incluído no array: R$ ' . number_format($irrfItem, 2, ',', '.') . ' | Base: R$ ' . number_format(floatval($it->nfi_v_bc_irrf ?? 0), 2, ',', '.'));
            } else {
                log_message('error', 'Item #' . $it->nfi_n_item . ' - IRRF ZERO ou não encontrado no banco! nfi_v_irrf: ' . ($it->nfi_v_irrf ?? 'NULL'));
            }
        }
        
        // Calcular totais somando os valores dos itens
        $totalVProd = 0.00;
        $totalVDesc = 0.00;
        $totalVOutro = 0.00;
        foreach ($itens as $item) {
            $totalVProd += floatval($item->nfi_v_prod ?? 0);
            $totalVDesc += floatval($item->nfi_v_desc ?? 0);
            $totalVOutro += floatval($item->nfi_v_outro ?? 0);
        }
        
        // Calcular total do IRRF somando os itens (conforme rejeição 685)
        // SEMPRE usar a soma dos itens, não o valor do banco
        // CRÍTICO: Isso é especialmente importante em reemissões
        $totalIrrfItens = 0;
        foreach ($itens as $item) {
            $totalIrrfItens += floatval($item->nfi_v_irrf ?? 0);
        }
        
        // Arredondar para 2 casas decimais para evitar problemas de precisão
        $totalIrrfItens = round($totalIrrfItens, 2);
        
        // Usar valores do banco para retenções
        $vRetPis = floatval($nfecom->nfc_v_ret_pis ?? 0);
        $vRetCofins = floatval($nfecom->nfc_v_ret_cofins ?? 0);
        $vRetCsll = floatval($nfecom->nfc_v_ret_csll ?? 0);
        $vIrrf = $totalIrrfItens; // SEMPRE usar soma dos itens como total do IRRF
        
        // SEMPRE atualizar o banco com a soma dos itens ANTES de gerar XML
        // Isso é crítico para reemissões onde o valor pode estar desatualizado
        $irrfBanco = floatval($nfecom->nfc_v_irrf ?? 0);
        if (abs($totalIrrfItens - $irrfBanco) > 0.001) {
            // Atualizar total do IRRF no banco com a soma dos itens
            $this->Nfecom_model->edit('nfecom_capa', ['nfc_v_irrf' => $totalIrrfItens], 'nfc_id', $id);
            log_message('info', 'Total do IRRF atualizado em prepararDadosEnvio: R$ ' . number_format($irrfBanco, 2, ',', '.') . ' → R$ ' . number_format($totalIrrfItens, 2, ',', '.') . ' (soma dos itens) - Status: ' . ($nfecom->nfc_status ?? 'N/A'));
            // Recarregar nfecom para ter o valor atualizado
            $nfecom = $this->Nfecom_model->getById($id);
        } else {
            log_message('debug', 'Total do IRRF OK em prepararDadosEnvio: R$ ' . number_format($totalIrrfItens, 2, ',', '.') . ' (soma dos itens = banco)');
        }
        
        // Regra G137: vNF = vProd + vOutro - vDesc - vRetPIS - vRetCofins - vRetCSLL - vIRRF
        $vNfCalculado = $totalVProd + $totalVOutro - $totalVDesc - $vRetPis - $vRetCofins - $vRetCsll - $vIrrf;
        
        // Atualizar nfc_inf_cpl com observação + mensagens fiscais se necessário
        // nfc_inf_cpl deve conter: Observação digitada pelo usuário + Mensagens fiscais (sem duplicatas)
        $infCplAtual = trim($nfecom->nfc_inf_cpl ?? '');
        
        // Buscar observação original da nota (se houver campo separado ou se estiver no infCpl)
        // Como a observação já deve estar no nfc_inf_cpl quando a nota foi salva,
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
                $this->Nfecom_model->edit('nfecom_capa', ['nfc_inf_cpl' => $infCplNovo], 'nfc_id', $id);
                // Recarregar nfecom com o infCpl atualizado
                $nfecom = $this->Nfecom_model->getById($id);
            }
        }

        // Garantir que a chave tenha exatamente 44 dígitos numéricos
        $chaveLimpa = preg_replace('/\D/', '', $nfecom->nfc_ch_nfcom);
        // Se a chave tiver mais de 44 dígitos, pegar apenas os últimos 44
        // Se tiver menos, completar com zeros à esquerda
        if (strlen($chaveLimpa) > 44) {
            $chaveLimpa = substr($chaveLimpa, -44);
        } elseif (strlen($chaveLimpa) < 44) {
            $chaveLimpa = str_pad($chaveLimpa, 44, '0', STR_PAD_LEFT);
        }
        
        return [
            'chave' => $chaveLimpa,
            'ide' => [
                'cUF' => $cUF,
                'tpAmb' => $configFiscal->cfg_ambiente,
                'serie' => $nfecom->nfc_serie ?: '1',
                'nNF' => $nfecom->nfc_nnf,
                'cNF' => $nfecom->nfc_cnf ?: str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'cDV' => $nfecom->nfc_cdv ?: 0,
                'dhEmi' => date('c', strtotime($nfecom->nfc_dhemi ?: 'now')),
                'tpEmis' => $nfecom->nfc_tp_emis ?: 1,
                'nSiteAutoriz' => 0,
                'cMunFG' => $emitente['enderEmit']['cMun'], // IBGE da Empresa
                'finNFCom' => 0,
                'tpFat' => 0,
                'verProc' => $this->config->item('app_version') ?: '1.0.0'
            ],
            'emitente' => [
                'cnpj' => $emitente['cnpj'],
                'ie' => $emitente['ie'],
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
                    'cep' => $emitente['enderEmit']['cep'],
                    'uf' => $emitente['enderEmit']['uf']
                ],
                'telefone' => $emitente['enderEmit']['fone']
            ],
            'destinatario' => [
                'nome' => $cliente->pes_nome,
                'cnpj' => preg_replace('/\D/', '', $cliente->pes_cpfcnpj),
                'indicador_ie' => ($cliente->doc_natureza_contribuinte == 'Contribuinte') ? '1' : '9',
                'ie' => $cliente->PES_IE,
                'endereco' => [
                    'logradouro' => $cliente->end_logradouro,
                    'numero' => $cliente->end_numero,
                    'complemento' => $cliente->end_complemento ?? '',
                    'bairro' => $cliente->bai_nome,
                    'codigo_municipio' => $cliente->mun_ibge,
                    'municipio' => $cliente->mun_nome,
                    'cep' => preg_replace('/\D/', '', $cliente->end_cep),
                    'uf' => $cliente->est_uf
                ]
            ],
            'assinante' => [
                'codigo' => $nfecom->nfc_i_cod_assinante ?: preg_replace('/\D/', '', $cliente->pes_cpfcnpj),
                'tipo' => $nfecom->nfc_tp_assinante ?: 3,
                'tipo_servico' => $nfecom->nfc_tp_serv_util ?: 6,
                'numero_contrato' => $nfecom->nfc_n_contrato,
                'data_inicio_contrato' => $nfecom->nfc_d_contrato_ini,
                'data_fim_contrato' => $nfecom->nfc_d_contrato_fim ?: null
            ],
            'itens' => $listaItens,
            'totais' => [
                'vProd' => $totalVProd,
                'icms' => [
                    'vBC' => floatval($nfecom->nfc_v_bc_icms ?? 0), // Base de cálculo ICMS - APENAS valor do banco
                    'vICMS' => floatval($nfecom->nfc_v_icms ?? 0), // Valor ICMS - APENAS valor do banco
                    'vICMSDeson' => floatval($nfecom->nfc_v_icms_deson ?? 0), // Valor ICMS Desonerado - APENAS valor do banco
                    'vFCP' => floatval($nfecom->nfc_v_fcp ?? 0) // Valor FCP - APENAS valor do banco
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
                    'vRetPIS' => $vRetPis,
                    'vRetCofins' => $vRetCofins,
                    'vRetCSLL' => $vRetCsll,
                    'vIRRF' => $vIrrf // IRRF: 4,8% sobre valor líquido
                ],
                'vDesc' => $totalVDesc,
                'vOutro' => $totalVOutro,
                // vNF conforme regra G137
                'vNF' => $vNfCalculado
            ],
            'faturamento' => [
                // Competência baseada no período fim (formato YYYYMM)
                // Exemplo: se período fim for 31-12-2025, competência será 202512
                'competencia' => !empty($nfecom->nfc_d_per_uso_fim) && empty($nfecom->nfc_compet_fat) 
                    ? date('Ym', strtotime($nfecom->nfc_d_per_uso_fim)) 
                    : (!empty($nfecom->nfc_d_contrato_ini) && empty($nfecom->nfc_compet_fat)
                        ? date('Ym', strtotime($nfecom->nfc_d_contrato_ini))
                        : (str_replace('-', '', $nfecom->nfc_compet_fat) ?: date('Ym'))),
                'vencimento' => $nfecom->nfc_d_venc_fat,
                'periodo_inicio' => $nfecom->nfc_d_per_uso_ini,
                'periodo_fim' => $nfecom->nfc_d_per_uso_fim,
                'cod_barras' => $nfecom->nfc_cod_barras ?? '1' // Código de barras padrão se não informado
            ],
            'informacoes_adicionais' => [
                'complementar' => $nfecom->nfc_inf_cpl
            ],
            'suplementar' => [
                'qrCode' => $this->gerarQRCodeNFCom($nfecom, $configFiscal)
            ]
        ];
    }

    private function getCodMunIBGE($cidade, $uf)
    {
        $this->db->select('m.mun_ibge');
        $this->db->from('municipios m');
        $this->db->join('estados e', 'e.est_id = m.est_id');
        $this->db->like('m.mun_nome', $cidade);
        $this->db->where('e.est_uf', $uf);
        $this->db->limit(1);
        $res = $this->db->get()->row();
        return $res ? $res->mun_ibge : '5218300'; // Fallback Posse-GO (7 dígitos)
    }

    /**
     * Retorna a base de cálculo correta para PIS/COFINS baseado no CST
     * Se o CST for isento/não tributado (03-09), retorna 0
     * Caso contrário, retorna o valor salvo ou o valor do produto se a base estiver incorreta
     * @param string $cst CST do PIS ou COFINS
     * @param float $baseSalva Base de cálculo salva no banco
     * @param float $valorProduto Valor do produto (para validação)
     * @return float Base de cálculo correta
     */
    private function getBaseCalculoPisCofins($cst, $baseSalva, $valorProduto = null)
    {
        // CSTs que NÃO calculam PIS/COFINS: 03-09, 49, 50-99 (isento, não tributado, suspenso, etc.)
        // CSTs que CALCULAM: 01, 02
        $cstsQueNaoCalculam = ['03', '04', '05', '06', '07', '08', '09', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'];
        
        if (in_array($cst, $cstsQueNaoCalculam)) {
            // CST isento/não tributado - base de cálculo deve ser 0
            return 0;
        }
        
        // Para CST 01 e 02 (tributável)
        $baseSalva = floatval($baseSalva);
        $valorProduto = $valorProduto !== null ? floatval($valorProduto) : null;
        
        // Se não temos valor do produto, usar a base salva (ou 0 se não houver)
        if ($valorProduto === null || $valorProduto == 0) {
            return $baseSalva > 0 ? $baseSalva : 0;
        }
        
        // Validar se a base salva está correta
        // A base de cálculo PIS/COFINS geralmente é igual ao valor do produto (ou valor do produto - desconto + outros)
        // Se a base salva for muito diferente do produto, provavelmente está errada
        
        // Se a base salva for mais de 50% maior que o produto, está claramente errada
        if ($baseSalva > $valorProduto * 1.5) {
            // Usar o valor do produto como base correta
            log_message('debug', "Base de cálculo PIS/COFINS corrigida: base salva ($baseSalva) muito maior que produto ($valorProduto). Usando produto como base.");
            return $valorProduto;
        }
        
        // Se a base salva for 0 ou muito pequena, usar o valor do produto
        if ($baseSalva == 0 || $baseSalva < 0.01) {
            return $valorProduto;
        }
        
        // Se a base salva estiver dentro de uma faixa razoável (até 50% maior que o produto), usar a base salva
        // Isso permite casos onde há acréscimos legais na base
        return $baseSalva;
    }

    /**
     * Calcula tributação usando a API CalculoTributacaoApi
     * @param int $produtoId ID do produto/serviço
     * @param int $clienteId ID do cliente
     * @param int $operacaoId ID da operação comercial
     * @param float $valor Valor unitário do produto
     * @param int $quantidade Quantidade
     * @param string $tipoOperacao 'entrada' ou 'saida' (padrão: 'saida')
     * @param int|null $enderecoId ID do endereço selecionado (opcional)
     * @return array|null Dados da tributação calculada ou null em caso de erro
     */
    private function calcularTributacao($produtoId, $clienteId, $operacaoId, $valor, $quantidade = 1, $tipoOperacao = 'saida', $enderecoId = null)
    {
        try {
            $tenId = $this->session->userdata('ten_id');
            
            // Validar ten_id - não pode ser 0, null ou vazio
            if (empty($tenId) || $tenId == 0) {
                log_message('error', 'Tenant ID inválido na sessão ao calcular tributação. ten_id: ' . var_export($tenId, true));
                return null;
            }
            
            // Construir URL da API - usar base_url para evitar redirecionamentos
            $baseUrl = rtrim(base_url(), '/');
            $url = $baseUrl . '/index.php/calculotributacaoapi/calcular';
            $params = [
                'ten_id' => (int)$tenId,
                'produto_id' => $produtoId,
                'cliente_id' => $clienteId,
                'operacao_id' => $operacaoId,
                'valor' => number_format($valor, 2, '.', ''),
                'quantidade' => $quantidade,
                'tipo_operacao' => $tipoOperacao
            ];
            
            // Adicionar endereco_id se fornecido
            if (!empty($enderecoId)) {
                $params['endereco_id'] = (int)$enderecoId;
            }
            
            $url .= '?' . http_build_query($params);
            
            log_message('debug', 'Chamando API de tributação: ' . $url);
            
            // Fazer requisição HTTP
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirecionamentos
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5); // Máximo de 5 redirecionamentos
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                log_message('error', 'Erro cURL ao calcular tributação: ' . $curlError . ' | URL: ' . $url);
                return null;
            }
            
            if ($httpCode !== 200 || !$response) {
                log_message('error', 'Erro ao calcular tributação - HTTP Code: ' . $httpCode . ' | URL: ' . $url . ' | Response: ' . substr($response, 0, 1000));
                return null;
            }
            
            // Verificar se a resposta é HTML (erro do CodeIgniter)
            if (strpos($response, '<!DOCTYPE') !== false || strpos($response, '<html') !== false) {
                log_message('error', 'API retornou HTML ao invés de JSON - URL: ' . $url . ' | Response: ' . substr($response, 0, 1000));
                return null;
            }
            
            $result = json_decode($response, true);
            $jsonError = json_last_error();
            
            if ($jsonError !== JSON_ERROR_NONE) {
                log_message('error', 'Erro ao decodificar JSON da API - JSON Error: ' . $jsonError . ' | URL: ' . $url . ' | Response: ' . substr($response, 0, 1000));
                return null;
            }
            
            if (!$result) {
                log_message('error', 'Resposta JSON vazia ou inválida - URL: ' . $url . ' | Response: ' . substr($response, 0, 500));
                return null;
            }
            
            if (!isset($result['sucesso'])) {
                log_message('error', 'Resposta da API sem campo "sucesso" - URL: ' . $url . ' | Response: ' . json_encode($result));
                return null;
            }
            
            if (!$result['sucesso']) {
                $mensagem = $result['mensagem'] ?? 'Erro desconhecido na API';
                log_message('error', 'API retornou erro: ' . $mensagem . ' | URL: ' . $url . ' | Response completa: ' . json_encode($result));
                return null;
            }
            
            if (!isset($result['dados'])) {
                log_message('error', 'API retornou sucesso mas sem campo "dados" - URL: ' . $url . ' | Response: ' . json_encode($result));
                return null;
            }
            
            log_message('debug', 'Tributação calculada com sucesso para Produto ID: ' . $produtoId . ' | Cliente ID: ' . $clienteId);
            
            return $result['dados'];
            
        } catch (Exception $e) {
            log_message('error', 'Exceção ao calcular tributação: ' . $e->getMessage());
            return null;
        }
    }
}
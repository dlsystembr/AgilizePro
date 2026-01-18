<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Nfecom extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('Nfecom_model');
        $this->load->model('Clientes_model');
        $this->data['menuNfecom'] = 'NFECom';
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
            $this->data['configuration']['first_url'] = base_url("index.php/nfecom/gerenciar")."?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
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
        $this->db->order_by('nomeCliente', 'asc');
        $this->db->limit(1000);
        $query_clientes = $this->db->get();
        $this->data['clientes'] = $query_clientes ? $query_clientes->result() : [];

        // Serviços ficam na tabela produtos
        $this->db->select('idProdutos as idServicos, descricao as nome');
        $this->db->from('produtos');
        $this->db->where('tipo', 'servico'); // Filtrar apenas serviços
        $this->db->order_by('descricao', 'asc');
        $this->db->limit(1000);
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

        // Regras de validação
        $this->form_validation->set_rules('clientes_id', 'Cliente', 'trim|required');
        $this->form_validation->set_rules('enderecoClienteSelect', 'Endereço do Cliente', 'trim|required');
        $this->form_validation->set_rules('observacoes', 'Observações', 'trim|required');
        $this->form_validation->set_rules('numeroContrato', 'Número do Contrato', 'trim|required');
        $this->form_validation->set_rules('dataContratoIni', 'Data Início Contrato', 'trim|required');
        $this->form_validation->set_rules('serie', 'Série', 'trim|required|numeric|min_length[1]|max_length[3]');
        $this->form_validation->set_rules('dataEmissao', 'Data Emissão', 'trim|required');
        $this->form_validation->set_rules('valorBruto', 'Valor Bruto', 'trim|required|numeric');
        $this->form_validation->set_rules('comissaoAgencia', 'Comissão Agência', 'trim|numeric');
        $this->form_validation->set_rules('dataVencimento', 'Data Vencimento', 'trim|required');
        $this->form_validation->set_rules('dataPeriodoIni', 'Data Período Início', 'trim|required');
        $this->form_validation->set_rules('dataPeriodoFim', 'Data Período Fim', 'trim|required');

        // Carregar alguns clientes iniciais para melhor UX (mais recentes primeiro)
        $this->db->select("c.CLN_ID as id,
                          CASE
                            WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME
                            ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME)
                          END as text,
                          p.PES_CPFCNPJ as cpf_cnpj");
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->order_by('text', 'asc'); // Ordem alfabética para melhor UX
        $this->db->limit(50); // Limitar a 50 para não sobrecarregar
        $query_clientes = $this->db->get();
        $this->data['clientes_iniciais'] = $query_clientes ? $query_clientes->result() : [];

        // Serviços ficam na tabela produtos - pro_tipo = 2 significa serviço
        $this->db->select('idProdutos as idServicos, descricao as nome, pro_tipo as tipo');
        $this->db->from('produtos');
        $this->db->where('pro_tipo', 2); // 2 = serviço
        $this->db->order_by('descricao', 'asc');
        $query_servicos = $this->db->get();
        $this->data['servicos'] = $query_servicos ? $query_servicos->result() : [];

        if ($this->form_validation->run('nfecom') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $data = $this->input->post();

            // Processar data de emissão
            if ($data['dataEmissao']) {
                try {
                    $dataEmissao = explode('/', $data['dataEmissao']);
                    $data['dataEmissao'] = $dataEmissao[2] . '-' . $dataEmissao[1] . '-' . $dataEmissao[0];
                } catch (Exception $e) {
                    $data['dataEmissao'] = date('Y-m-d');
                }
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

            // Processar múltiplos serviços
            $servicos = isset($data['servicos']) ? $data['servicos'] : [];
            $totalValorBruto = 0;
            $nomesServicos = [];

            foreach ($servicos as $servico) {
                if (!empty($servico['id']) && !empty($servico['quantidade']) && !empty($servico['valorUnitario'])) {
                    $quantidade = floatval($servico['quantidade']);
                    $valorUnitario = floatval($servico['valorUnitario']);
                    $valorTotal = $quantidade * $valorUnitario;
                    $totalValorBruto += $valorTotal;

                    // Buscar nome do serviço
                    $this->db->select('descricao');
                    $this->db->from('produtos');
                    $this->db->where('idProdutos', $servico['id']);
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
            $pis = $valorLiquido * 0.0065; // 0.65%
            $cofins = $valorLiquido * 0.03; // 3.0%
            $irrf = $valorLiquido * 0.024; // 2.4% (IRRF)
            $valorNF = $valorLiquido - $pis - $cofins - $irrf;

            $nomeServico = implode('; ', $nomesServicos);

            // Buscar dados completos do cliente incluindo endereço
            $this->db->select('p.*, e.END_LOGRADOURO as logradouro, e.END_NUMERO as numero, e.END_COMPLEMENTO as complemento, e.END_CEP as cep, m.nome as municipio_nome, m.codigo_ibge, es.uf as estado_uf');
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID');
            $this->db->join('enderecos e', 'e.PES_ID = p.PES_ID AND e.END_PADRAO = 1', 'left'); // Endereço padrão
            $this->db->join('municipios m', 'm.id = e.MUN_ID', 'left');
            $this->db->join('estados es', 'es.id = e.EST_ID', 'left');
            $this->db->where('c.CLN_ID', $data['clientes_id']);
            $cliente_query = $this->db->get();
            $cliente = $cliente_query->row();

            if ($cliente) {
                $data['nomeCliente'] = $cliente->PES_FISICO_JURIDICO == 'F' ? $cliente->PES_NOME : ($cliente->PES_RAZAO_SOCIAL ?: $cliente->PES_NOME);
                $data['cnpjCliente'] = $cliente->PES_CPFCNPJ;
                $data['logradouroCliente'] = $cliente->logradouro ?? '';
                $data['numeroCliente'] = $cliente->numero ?? '';
                $data['bairroCliente'] = ''; // Bairro não disponível sem tabela bairros
                $data['municipioCliente'] = $cliente->municipio_nome ?? '';
                $data['codMunCliente'] = $cliente->codigo_ibge ?? '';
                $data['cepCliente'] = $cliente->cep ?? '';
                $data['ufCliente'] = $cliente->estado_uf ?? '';
            }

            // Dados da NFCom
            $nfecomData = [
                'NFC_CUF' => $this->config->item('cod_uf'),
                'NFC_TIPO_AMBIENTE' => $this->data['configuration']['ambiente'],
                'NFC_MOD' => '62',
                'NFC_SERIE' => $data['serie'],
                'NFC_NNF' => $this->Nfecom_model->getNextNumero(),
                'NFC_CNF' => str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                'NFC_DHEMI' => $data['dataEmissao'] . ' ' . date('H:i:s'),
                'NFC_TP_EMIS' => 1,
                'NFC_N_SITE_AUTORIZ' => 0,
                'NFC_C_MUN_FG' => $this->config->item('cod_mun'),
                'NFC_FIN_NFCOM' => 0,
                'NFC_TP_FAT' => 0,
                'NFC_VER_PROC' => '1.0.0',
                'NFC_CNPJ_EMIT' => $this->config->item('cnpj'),
                'NFC_IE_EMIT' => $this->config->item('ie'),
                'NFC_CRT_EMIT' => 3,
                'NFC_X_NOME_EMIT' => $this->config->item('nome'),
                'NFC_X_FANT_EMIT' => $this->config->item('nome_fantasia'),
                'NFC_X_LGR_EMIT' => $this->config->item('logradouro'),
                'NFC_NRO_EMIT' => $this->config->item('numero'),
                'NFC_X_CPL_EMIT' => $this->config->item('complemento'),
                'NFC_X_BAIRRO_EMIT' => $this->config->item('bairro'),
                'NFC_C_MUN_EMIT' => $this->config->item('cod_mun'),
                'NFC_X_MUN_EMIT' => $this->config->item('municipio'),
                'NFC_CEP_EMIT' => $this->config->item('cep'),
                'NFC_UF_EMIT' => $this->config->item('uf'),
                'NFC_FONE_EMIT' => $this->config->item('telefone'),
                'NFC_X_NOME_DEST' => $data['nomeCliente'],
                'NFC_CNPJ_DEST' => $data['cnpjCliente'],
                'NFC_IND_IE_DEST' => 9,
                'NFC_X_LGR_DEST' => $data['logradouroCliente'],
                'NFC_NRO_DEST' => $data['numeroCliente'],
                'NFC_X_BAIRRO_DEST' => $data['bairroCliente'],
                'NFC_C_MUN_DEST' => $data['codMunCliente'],
                'NFC_X_MUN_DEST' => $data['municipioCliente'],
                'NFC_CEP_DEST' => $data['cepCliente'],
                'NFC_UF_DEST' => $data['ufCliente'],
                'NFC_I_COD_ASSINANTE' => $data['cnpjCliente'],
                'NFC_TP_ASSINANTE' => 3,
                'NFC_TP_SERV_UTIL' => 6,
                'NFC_N_CONTRATO' => $data['numeroContrato'],
                'NFC_D_CONTRATO_INI' => $data['dataContratoIni'],
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
                'NFC_INF_CPL' => $this->buildInfoComplementar($data, $valorBruto, $comissaoAgencia, $valorLiquido),
                'NFC_STATUS' => 1 // Salvo
            ];

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
                        $valorTotal = $quantidade * $valorUnitario;

                        // Buscar nome do serviço
                        $this->db->select('descricao');
                        $this->db->from('produtos');
                        $this->db->where('idProdutos', $servico['id']);
                        $servico_query = $this->db->get();
                        $servico_info = $servico_query->row();
                        $nomeServicoItem = $servico_info ? $servico_info->descricao : 'Serviço não encontrado';

                        // Calcular tributos proporcionais para este item
                        $proporcao = $valorTotal / $valorBruto;
                        $pisItem = $pis * $proporcao;
                        $cofinsItem = $cofins * $proporcao;
                        $irrfItem = $irrf * $proporcao;

                        $itemData = [
                            'NFC_ID' => $idNfecom,
                            'NFI_N_ITEM' => $itemNumero,
                            'NFI_C_PROD' => $servico['id'],
                            'NFI_X_PROD' => $nomeServicoItem . ' - Qtd: ' . $quantidade . ' - ' . $data['observacoes'],
                            'NFI_C_CLASS' => '0600402',
                            'NFI_CFOP' => '5307',
                            'NFI_U_MED' => '4',
                            'NFI_Q_FATURADA' => $quantidade,
                            'NFI_V_ITEM' => $valorTotal,
                            'NFI_V_DESC' => 0.00,
                            'NFI_V_OUTRO' => 0.00,
                            'NFI_V_PROD' => $valorTotal,
                            'NFI_CST_ICMS' => '41',
                            'NFI_CST_PIS' => '01',
                            'NFI_V_BC_PIS' => $valorTotal,
                            'NFI_P_PIS' => 0.65,
                            'NFI_V_PIS' => $pisItem,
                            'NFI_CST_COFINS' => '01',
                            'NFI_V_BC_COFINS' => $valorTotal,
                            'NFI_P_COFINS' => 3.00,
                            'NFI_V_COFINS' => $cofinsItem,
                            'NFI_V_BC_FUST' => 0.00,
                            'NFI_P_FUST' => 0.00,
                            'NFI_V_FUST' => 0.00,
                            'NFI_V_BC_FUNTEL' => 0.00,
                            'NFI_P_FUNTEL' => 0.00,
                            'NFI_V_FUNTEL' => 0.00,
                            'NFI_V_BC_IRRF' => $valorTotal,
                            'NFI_V_IRRF' => $irrfItem,
                            'NFI_DATA_CADASTRO' => date('Y-m-d H:i:s'),
                            'NFI_DATA_ATUALIZACAO' => date('Y-m-d H:i:s')
                        ];

                        $this->Nfecom_model->add('nfecom_itens', $itemData);
                        $itemNumero++;
                    }
                }

                // Se não há serviços válidos, criar um item genérico
                if ($itemNumero == 1) {
                    $itemData = [
                        'NFC_ID' => $idNfecom,
                        'NFI_N_ITEM' => 1,
                        'NFI_C_PROD' => '1',
                        'NFI_X_PROD' => 'Serviços diversos - ' . $data['observacoes'],
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
        }

        $this->data['view'] = 'nfecom/adicionarNfecom';
        return $this->layout();
    }

    public function visualizar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFECom.');
            redirect(base_url());
        }

        $this->data['result'] = $this->Nfecom_model->getById($this->uri->segment(3));
        $this->data['itens'] = $this->Nfecom_model->getItens($this->uri->segment(3));

        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        $this->data['view'] = 'nfecom/visualizar';
        return $this->layout();
    }

    public function gerarXml()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar XML da NFECom.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);
        $itens = $this->Nfecom_model->getItens($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // Gerar XML
        $xml = $this->generateNfecomXml($nfecom, $itens);

        // Salvar XML no banco
        $this->Nfecom_model->updateStatus($id, ['NFC_XML' => $xml, 'NFC_STATUS' => 2]); // 2 = Enviado

        // Download do XML
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="nfecom_' . $nfecom->NFC_CH_NFCOM . '.xml"');
        echo $xml;
        exit;
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

        // Gerar DANFE (por enquanto apenas mostrar dados)
        $this->data['nfecom'] = $nfecom;
        $this->data['itens'] = $itens;
        $this->data['view'] = 'nfecom/danfe';
        return $this->layout();
    }

    public function autorizar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para autorizar NFECom.');
            redirect(base_url());
        }

        $id = $this->uri->segment(3);
        $nfecom = $this->Nfecom_model->getById($id);

        if ($nfecom == null) {
            $this->session->set_flashdata('error', 'NFECom não encontrada.');
            redirect(site_url('nfecom'));
        }

        // Aqui seria implementada a integração com a SEFAZ
        // Por enquanto, apenas simular autorização
        $updateData = [
            'NFC_STATUS' => 3, // 3 = Autorizado
            'NFC_N_PROT' => '3522600005524308',
            'NFC_DH_RECBTO' => date('Y-m-d H:i:s'),
            'NFC_C_STAT' => '100',
            'NFC_X_MOTIVO' => 'Autorizado o uso da NFCom'
        ];

        $this->Nfecom_model->updateStatus($id, $updateData);

        $this->session->set_flashdata('success', 'NFECom autorizada com sucesso!');
        redirect(site_url('nfecom/visualizar/' . $id));
    }

    private function buildInfoComplementar($data, $valorBruto, $comissaoAgencia, $valorLiquido)
    {
        $info = "VEICULAÇÃO COMERCIAL NA RÁDIO " . strtoupper($this->config->item('nome_fantasia')) . ", " .
                strtoupper($this->config->item('municipio')) . "-" . $this->config->item('uf') . ", " .
                "DA CAMPANHA " . $data['observacoes'] . ", " .
                "VALOR BRUTO: R$ " . number_format($valorBruto, 2, ',', '.') . "\n" .
                "COMISSÃO AGÊNCIA: R$ " . number_format($comissaoAgencia, 2, ',', '.') . "\n" .
                "VALOR LÍQUIDO: R$ " . number_format($valorLiquido, 2, ',', '.') . "\n" .
                "DADOS BANCÁRIOS " . $data['dadosBancarios'] . "\n" .
                "Não tributação de ICMS conforme art. 155, §2º, X, 'd' da CRFB/1988. Imunidade de IBS/CBS conforme Artigo 9º, inciso VI da Lei Complementar nº 214/2025.";

        return $info;
    }

    private function calculateDV($data)
    {
        // Implementação simplificada do cálculo do DV
        // Em produção, deve seguir as regras oficiais da SEFAZ
        $chave = $data['NFC_CUF'] . date('ym', strtotime($data['NFC_DHEMI'])) .
                $data['NFC_CNPJ_EMIT'] . $data['NFC_MOD'] . $data['NFC_SERIE'] .
                str_pad($data['NFC_NNF'], 9, '0', STR_PAD_LEFT) .
                $data['NFC_TP_EMIS'] . $data['NFC_CNF'];

        // Cálculo simples para exemplo
        $soma = 0;
        for ($i = 0; $i < strlen($chave); $i++) {
            $soma += intval($chave[$i]);
        }

        return $soma % 10;
    }

    private function generateChave($data)
    {
        // Gerar chave da NFCom
        $chave = $data['NFC_CUF'] .
                date('ym', strtotime($data['NFC_DHEMI'])) .
                $data['NFC_CNPJ_EMIT'] .
                $data['NFC_MOD'] .
                str_pad($data['NFC_SERIE'], 3, '0', STR_PAD_LEFT) .
                str_pad($data['NFC_NNF'], 9, '0', STR_PAD_LEFT) .
                $data['NFC_TP_EMIS'] .
                str_pad($data['NFC_CNF'], 8, '0', STR_PAD_LEFT) .
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

        $this->db->select('p.*, e.END_LOGRADOURO as logradouro, e.END_NUMERO as numero, e.END_COMPLEMENTO as complemento, e.END_CEP as cep, m.nome as municipio_nome, m.codigo_ibge, es.uf as estado_uf');
        $this->db->from('clientes c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID');
        $this->db->join('enderecos e', 'e.PES_ID = p.PES_ID AND e.END_PADRAO = 1', 'left'); // Endereço padrão
        $this->db->join('municipios m', 'm.id = e.MUN_ID', 'left');
        $this->db->join('estados es', 'es.id = e.EST_ID', 'left');
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

            $this->db->order_by('text', 'asc');
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
}
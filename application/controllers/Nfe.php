<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!defined('SOAP_1_2')) {
    define('SOAP_1_2', 2);
}

require_once FCPATH . 'application/vendor/autoload.php';

use NFePHP\NFe\Common\Certificate;
use NFePHP\Common\Exception\CertificateException;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Eventos\Event;
use NFePHP\Common\Standardize;
use NFePHP\DA\NFe\Danfe;
use NFePHP\Extras\Danfe as ExtrasDanfe;
use stdClass as stdObject;

class Nfe extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar o emissor de notas.');
            redirect(base_url());
        }

        $this->load->model('Nfe_model', 'nfe');
        $this->load->model('Mapos_model');
        $this->load->model('Vendas_model');
        $this->load->library('CertificateHandler');
        
        $this->data['menuNfe'] = true;
        
        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            $this->session->set_flashdata('error', 'Dados do emitente não encontrados. Configure o emitente antes de emitir notas fiscais.');
            redirect(base_url() . 'index.php/mapos/emitente');
        }

        // Verifica se todos os campos necessários estão preenchidos
        $requiredFields = ['nome', 'cnpj', 'ie', 'rua', 'numero', 'bairro', 'cidade', 'uf', 'ibge', 'cep', 'telefone', 'email'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($emitente->$field)) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            $this->session->set_flashdata('error', 'Os seguintes campos do emitente estão vazios: ' . implode(', ', $missingFields) . '. Configure-os antes de emitir notas fiscais.');
            redirect(base_url() . 'index.php/mapos/emitente');
        }

        // Converte para objeto se for array
        if (is_array($emitente)) {
            $emitente = (object)$emitente;
        }

        // Garante que a UF está no formato correto (2 letras)
        if (isset($emitente->uf)) {
            $emitente->estado = strtoupper($emitente->uf);
        }

        // Adiciona o código IBGE se não existir
        if (!isset($emitente->ibge)) {
            $emitente->ibge = $this->get_cUF($emitente->estado);
        }

        $this->data['emitente'] = $emitente;
    }

    private function getTools()
    {
        try {
            $certificate = $this->nfe->getCertificate();
            if (!$certificate) {
                throw new Exception("Certificado digital não configurado. Configure o certificado nas configurações do sistema.");
            }

            // Verifica se o certificado está vencido
            $dataValidade = new DateTime($certificate->data_validade);
            $hoje = new DateTime();
            if ($hoje > $dataValidade) {
                throw new Exception("O certificado digital está vencido. Por favor, atualize o certificado nas configurações do sistema.");
            }

            // Tenta ler o certificado
            try {
                $cert = \NFePHP\Common\Certificate::readPfx($certificate->certificado_digital, $certificate->senha_certificado);
            } catch (Exception $e) {
                throw new Exception("Erro ao ler o certificado digital: " . $e->getMessage());
            }

            // Configura o ambiente
            $this->db->where('idConfiguracao', 1);
            $configNFe = $this->db->get('configuracoes_nfe')->row();
            
            if (!$configNFe) {
                throw new Exception("Configurações de NFe não encontradas. Configure as configurações de NFe primeiro.");
            }

            $ambiente = $configNFe->ambiente == 1 ? 'producao' : 'homologacao';
            $uf = $this->data['emitente']->uf;

            // Configura o JSON com as URLs do webservice baseado no estado
            $config = [
                "atualizacao" => date('Y-m-d H:i:s'),
                "tpAmb" => (int)$configNFe->ambiente,
                "razaosocial" => $this->data['emitente']->nome,
                "siglaUF" => $uf,
                "cUF" => $this->get_cUF($uf),
                "cnpj" => preg_replace('/[^0-9]/', '', $this->data['emitente']->cnpj),
                "schemes" => "PL_009_V4",
                "versao" => $configNFe->versao_nfe,
                "tokenIBPT" => "",
                "csc" => "",
                "CSCid" => "",
                "aProxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                ]
            ];

            // Configura os autorizadores baseado no estado
            switch ($uf) {
                case 'AC':
                case 'AL':
                case 'AP':
                case 'DF':
                case 'ES':
                case 'PB':
                case 'PI':
                case 'RJ':
                case 'RN':
                case 'RO':
                case 'RR':
                case 'SC':
                case 'SE':
                case 'to':
                    // SVRS - Estados que usam o webservice do RS
                    $config["autorizadores"] = [
                        "SVRS" => [
                            "uf" => ["AC", "AL", "AP", "DF", "ES", "PB", "PI", "RJ", "RN", "RO", "RR", "SC", "SE", "to"],
                            "homologacao" => [
                                "url" => "https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx",
                                "wsdl" => "https://homologacao.nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx?wsdl"
                            ],
                            "producao" => [
                                "url" => "https://nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx",
                                "wsdl" => "https://nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx?wsdl"
                            ]
                        ]
                    ];
                    break;
                case 'AM':
                case 'BA':
                case 'CE':
                case 'GO':
                case 'MA':
                case 'MS':
                case 'MT':
                case 'PA':
                case 'PE':
                case 'PR':
                case 'RS':
                    // SVC-AN - Estados que usam o webservice da AN
                    $config["autorizadores"] = [
                        "SVC-AN" => [
                            "uf" => ["AM", "BA", "CE", "GO", "MA", "MS", "MT", "PA", "PE", "PR", "RS"],
                            "homologacao" => [
                                "url" => "https://hom.nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx",
                                "wsdl" => "https://hom.nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx?wsdl"
                            ],
                            "producao" => [
                                "url" => "https://nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx",
                                "wsdl" => "https://nfe.sefazvirtual.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx?wsdl"
                            ]
                        ]
                    ];
                    break;
                case 'MG':
                    // MG - Minas Gerais tem seu próprio webservice
                    $config["autorizadores"] = [
                        "MG" => [
                            "uf" => ["MG"],
                            "homologacao" => [
                                "url" => "https://hnfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento",
                                "wsdl" => "https://hnfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento?wsdl"
                            ],
                            "producao" => [
                                "url" => "https://nfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento",
                                "wsdl" => "https://nfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento?wsdl"
                            ]
                        ]
                    ];
                    break;
                case 'SP':
                    // SP - São Paulo tem seu próprio webservice
                    $config["autorizadores"] = [
                        "SP" => [
                            "uf" => ["SP"],
                            "homologacao" => [
                                "url" => "https://homologacao.nfe.fazenda.sp.gov.br/ws/recepcaoevento.asmx",
                                "wsdl" => "https://homologacao.nfe.fazenda.sp.gov.br/ws/recepcaoevento.asmx?wsdl"
                            ],
                            "producao" => [
                                "url" => "https://nfe.fazenda.sp.gov.br/ws/recepcaoevento.asmx",
                                "wsdl" => "https://nfe.fazenda.sp.gov.br/ws/recepcaoevento.asmx?wsdl"
                            ]
                        ]
                    ];
                    break;
                default:
                    throw new Exception("Estado não suportado: " . $uf);
            }

            // Tenta ler o certificado
            $cert = \NFePHP\Common\Certificate::readPfx($certificate->certificado_digital, $certificate->senha_certificado);

            // Cria o objeto Tools com o certificado e configuração
            $tools = new Tools(json_encode($config), $cert);
            
            return $tools;
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar certificado: " . $e->getMessage());
        }
    }

    private function gera_json()
    {
        $config = [
            "atualizacao" => date('Y-m-d H:i:s'),
            "tpAmb" => 2, // Ambiente de homologação
            "razaosocial" => $this->data['emitente']->nome,
            "siglaUF" => $this->data['emitente']->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $this->data['emitente']->cnpj),
            "schemes" => "PL_008i2",
            "versao" => "4.00",
            "tokenIBPT" => "",
            "csc" => "",
            "CSCid" => "",
            "aProxyConf" => [
                "proxyIp" => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];

        return json_encode($config);
    }

    private function get_cUF($uf)
    {
        $ufs = [
            'AC' => '12', 'AL' => '27', 'AP' => '16', 'AM' => '13', 'BA' => '29',
            'CE' => '23', 'DF' => '53', 'ES' => '32', 'GO' => '52', 'MA' => '21',
            'MT' => '51', 'MS' => '50', 'MG' => '31', 'PA' => '15', 'PB' => '25',
            'PR' => '41', 'PE' => '26', 'PI' => '22', 'RJ' => '33', 'RN' => '24',
            'RS' => '43', 'RO' => '11', 'RR' => '14', 'SC' => '42', 'SP' => '35',
            'SE' => '28', 'to' => '17'
        ];
        return $ufs[$uf] ?? '43';
    }

    public function index()
    {
        $this->data['view'] = 'nfe/index';
        
        // Recupera dados do modal da sessão
        $nfe_modal = $this->session->flashdata('nfe_modal');
        if ($nfe_modal) {
            $this->data['nfe_modal'] = $nfe_modal;
        }
        
        $this->layout();
    }

    public function configuracoes()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar configurações de NFE.');
            redirect(base_url());
        }

        $this->data['view'] = 'nfe/configuracoes';
        $this->layout();
    }

    public function configuracoesNFe()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar configurações de NFE.');
            redirect(base_url());
        }

        // Carrega as configurações do NFe da tabela específica
        $this->db->where('idConfiguracao', 1);
        $config = $this->db->get('configuracoes_nfe')->row();
        
        // Se não existir configuração, usa os valores padrão
        if (!$config) {
            $config = (object)[
                'tipo_documento' => 'NFe',
                'ambiente' => 2,
                'versao_nfe' => '4.00',
                'tipo_impressao_danfe' => 1,
                'orientacao_danfe' => 'P',
                'sequencia_nota' => 1,
                'sequencia_nfce' => 1,
                'csc' => null,
                'csc_id' => null
            ];
        }

        $this->data['config'] = $config;
        $this->data['emitente'] = $this->data['emitente'];
        $this->data['view'] = 'nfe/configuracoesNFe';
        $this->layout();
    }

    public function configuracoesNFCe()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar configurações de NFC-e.');
            redirect(base_url());
        }

        // Verifica se a tabela configuracoes_nfce existe, se não, cria
        if (!$this->db->table_exists('configuracoes_nfce')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `configuracoes_nfce` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ambiente` int(1) NOT NULL DEFAULT 2 COMMENT '1-Produção, 2-Homologação',
                `versao_nfce` varchar(10) NOT NULL DEFAULT '4.00',
                `tipo_impressao_danfe` int(1) NOT NULL DEFAULT 4 COMMENT '4-NFC-e',
                `sequencia_nfce` int(11) NOT NULL DEFAULT 1,
                `csc` varchar(100) DEFAULT NULL,
                `csc_id` varchar(100) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            // Insere configuração padrão
            $this->db->insert('configuracoes_nfce', [
                'ambiente' => 2,
                'versao_nfce' => '4.00',
                'tipo_impressao_danfe' => 4,
                'sequencia_nfce' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Carrega as configurações do NFC-e
        $this->db->where('id', 1);
        $config = $this->db->get('configuracoes_nfce')->row();
        
        // Se não existir configuração, usa os valores padrão
        if (!$config) {
            $config = (object)[
                'ambiente' => 2,
                'versao_nfce' => '4.00',
                'tipo_impressao_danfe' => 4,
                'sequencia_nfce' => 1,
                'csc' => null,
                'csc_id' => null
            ];
        }

        $this->data['config'] = $config;
        $this->data['emitente'] = $this->data['emitente'];
        $this->data['view'] = 'nfe/configuracoesNFCe';
        $this->layout();
    }

    public function saveConfiguracoesNFe()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar configurações de NFE.');
            redirect(base_url());
        }

        $data = [
            'tipo_documento' => 'NFe',
            'ambiente' => $this->input->post('ambiente'),
            'versao_nfe' => $this->input->post('versao_nfe'),
            'tipo_impressao_danfe' => $this->input->post('tipo_impressao_danfe'),
            'orientacao_danfe' => $this->input->post('orientacao_danfe'),
            'sequencia_nota' => $this->input->post('sequencia_nota'),
            'imprimir_logo_nfe' => $this->input->post('imprimir_logo_nfe') ? 1 : 0,
            'preview_nfe' => $this->input->post('preview_nfe') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Verifica se todos os campos necessários estão presentes
        if (empty($data['ambiente']) || empty($data['versao_nfe']) || empty($data['tipo_impressao_danfe']) || empty($data['orientacao_danfe']) || empty($data['sequencia_nota'])) {
            $this->session->set_flashdata('error', 'Todos os campos são obrigatórios.');
            redirect(base_url() . 'index.php/nfe/configuracoesNFe');
        }

        // Verifica se já existe uma configuração
        $this->db->where('idConfiguracao', 1);
        $exists = $this->db->get('configuracoes_nfe')->row();
        
        if ($exists) {
            // Atualiza a configuração existente
            $this->db->where('idConfiguracao', 1);
            $result = $this->db->update('configuracoes_nfe', $data);
        } else {
            // Cria uma nova configuração
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->db->insert('configuracoes_nfe', $data);
        }

        if ($result) {
            $this->session->set_flashdata('success', 'Configurações de NFe atualizadas com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao atualizar configurações de NFe. Por favor, tente novamente.');
        }

        redirect(base_url() . 'index.php/nfe/configuracoesNFe');
    }

    public function saveConfiguracoesNFCe()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar configurações de NFC-e.');
            redirect(base_url());
        }

        $data = [
            'ambiente' => $this->input->post('ambiente'),
            'csc' => $this->input->post('csc'),
            'csc_id' => $this->input->post('csc_id'),
            'sequencia_nfce' => $this->input->post('sequencia_nfce'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Verifica se todos os campos necessários estão presentes
        if (empty($data['ambiente']) || empty($data['sequencia_nfce'])) {
            $this->session->set_flashdata('error', 'Ambiente e Sequência de Nota são campos obrigatórios.');
            redirect(base_url() . 'index.php/nfe/configuracoesNFCe');
        }

        // Verifica se já existe uma configuração
        $this->db->where('id', 1);
        $exists = $this->db->get('configuracoes_nfce')->row();
        
        if ($exists) {
            // Atualiza a configuração existente
            $this->db->where('id', 1);
            $result = $this->db->update('configuracoes_nfce', $data);
        } else {
            // Cria uma nova configuração
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['versao_nfce'] = '4.00';
            $data['tipo_impressao_danfe'] = 4;
            $result = $this->db->insert('configuracoes_nfce', $data);
        }

        if ($result) {
            $this->session->set_flashdata('success', 'Configurações de NFC-e atualizadas com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao atualizar configurações de NFC-e. Por favor, tente novamente.');
        }

        redirect(base_url() . 'index.php/nfe/configuracoesNFCe');
    }

    public function certificado()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar certificado NFe.');
            redirect(base_url());
        }

        $this->data['config'] = $this->data['configuration'];
        $this->data['emitente'] = $this->data['emitente'];
        $this->data['certificado'] = $this->nfe->getCertificate();

        $this->data['view'] = 'nfe/certificado';
        $this->layout();
    }

    public function emitir($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
        }

        $this->load->model('Mapos_model');
        $this->load->model('Vendas_model');
        $this->load->model('Clientes_model');
        $this->load->model('Produtos_model');
        $this->load->model('ClassificacaoFiscal_model');
        $this->load->model('OperacaoComercial_model');
        $this->load->model('TributacaoProduto_model');

        // Get tax regime from configuration
        $configuracao = $this->Mapos_model->getConfiguracao();
        $regime_tributario = $configuracao['regime_tributario'];
        $crt = ($regime_tributario === 'Simples Nacional') ? '1' : '3';

        $venda = $this->Vendas_model->getById($id);
        if (!$venda) {
            $this->session->set_flashdata('error', 'Venda não encontrada.');
            redirect(base_url('vendas'));
        }

        $cliente = $this->Clientes_model->getById($venda->clientes_id);
        if (!$cliente) {
            $this->session->set_flashdata('error', 'Cliente não encontrado.');
            redirect(base_url('vendas'));
        }

        $produtos = $this->Vendas_model->getProdutos($id);
        if (!$produtos) {
            $this->session->set_flashdata('error', 'Produtos não encontrados.');
            redirect(base_url('vendas'));
        }

        // Get operation name
        $operacao = $this->OperacaoComercial_model->getById($venda->operacao_comercial_id);
        if (!$operacao) {
            $this->session->set_flashdata('error', 'Operação comercial não encontrada.');
            redirect(base_url('vendas'));
        }

        // Debug logs
        log_message('debug', 'Venda ID: ' . $id);
        log_message('debug', 'Operação Comercial ID: ' . $venda->operacao_comercial_id);
        log_message('debug', 'Natureza Contribuinte: ' . $cliente->natureza_contribuinte);
        log_message('debug', 'Objetivo Comercial: ' . $cliente->objetivo_comercial);

        foreach ($produtos as $p) {
            $emitente = $this->Mapos_model->getEmitente();
            $cliente = $this->Clientes_model->getById($venda->clientes_id);
            // Buscar tributação usando os parâmetros corretos
            $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
            $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                $venda->operacao_comercial_id,
                $cliente->natureza_contribuinte,
                $destinacao, // destinação: Estadual ou Interestadual
                $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
            );

            if (!$tributacao) {
                $this->session->set_flashdata('error', 'Não foi encontrada tributação para a operação comercial selecionada na venda. Por favor, verifique se existe uma classificação fiscal cadastrada com os parâmetros: Operação Comercial ID: ' . $venda->operacao_comercial_id . ', Natureza Contribuinte: ' . $cliente->natureza_contribuinte . ', Destinação: ' . $destinacao . ', Objetivo Comercial: ' . $cliente->objetivo_comercial);
                redirect(base_url('vendas'));
            }
            $venda_id = $this->input->post('venda_id');
            $produtos = $this->Vendas_model->getProdutos($venda_id);

            // Buscar tributação do produto
            foreach ($produtos as $produto) {
                if (isset($produto->tributacao_produto_id) && $produto->tributacao_produto_id) {
                    $tributacao_produto = $this->TributacaoProduto_model->getById($produto->tributacao_produto_id);
                    if ($tributacao_produto) {
                        // Adicionar informações de impostos do produto
                        $produto->cst_ipi = $tributacao_produto->cst_ipi_saida;
                        $produto->aliq_ipi = $tributacao_produto->aliq_ipi_saida;
                        $produto->cst_pis = $tributacao_produto->cst_pis_saida;
                        $produto->aliq_pis = $tributacao_produto->aliq_pis_saida;
                        $produto->cst_cofins = $tributacao_produto->cst_cofins_saida;
                        $produto->aliq_cofins = $tributacao_produto->aliq_cofins_saida;
                        $produto->regime_fiscal = $tributacao_produto->regime_fiscal_tributario;

                        // Calcular valores dos impostos
                        $valor_unitario = $produto->subTotal / $produto->quantidade;
                        $produto->valor_ipi = ($valor_unitario * $produto->aliq_ipi) / 100;
                        $produto->valor_pis = ($valor_unitario * $produto->aliq_pis) / 100;
                        $produto->valor_cofins = ($valor_unitario * $produto->aliq_cofins) / 100;
                    }
                }
            }
        }

        $this->data['venda'] = $venda;
        $this->data['cliente'] = $cliente;
        $this->data['produtos'] = $produtos;
        $this->data['crt'] = $crt;
        $this->data['operacao'] = $operacao;
        $this->data['view'] = 'nfe/emitir';
        return $this->layout();
    }

    public function saveCertificate()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar certificado NFe.');
            redirect(base_url());
        }

        if ($this->input->post() == null) {
            $this->session->set_flashdata('error', 'Nenhum dado foi enviado.');
            redirect(base_url() . 'nfe/certificado');
        }

        // Check if file was uploaded
        if (!isset($_FILES['certificado']) || $_FILES['certificado']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'Por favor, selecione um certificado digital válido.');
            redirect(base_url() . 'nfe/certificado');
        }

        $senha = $this->input->post('senha');
        if (empty($senha)) {
            $this->session->set_flashdata('error', 'A senha do certificado é obrigatória.');
            redirect(base_url() . 'nfe/certificado');
        }

        try {
            // Read certificate content
            $certificadoContent = file_get_contents($_FILES['certificado']['tmp_name']);
            if ($certificadoContent === false) {
                throw new Exception('Erro ao ler o conteúdo do certificado.');
            }

            // Validate certificate and get information
            $certHandler = new CertificateHandler();
            $certHandler->loadCertificate($_FILES['certificado']['tmp_name'], $senha);
            
            if (!$certHandler->isValid()) {
                throw new Exception('Certificado inválido');
            }

            $certInfo = $certHandler->getCertificateInfo();
            
            // Check if certificate is valid
            $now = new DateTime();
            $validToDate = new DateTime($certInfo['validTo']);
            
            if ($now > $validToDate) {
                throw new Exception('O certificado digital está vencido.');
            }

            $data = [
                'certificado_digital' => $certificadoContent,
                'senha_certificado' => $senha,
                'data_validade' => $certInfo['validTo'],
                'nome_certificado' => $_FILES['certificado']['name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->nfe->updateCertificate($data)) {
                throw new Exception('Erro ao salvar certificado no banco de dados');
            }

            $this->session->set_flashdata('success', 'Certificado salvo com sucesso!');
            redirect(base_url() . 'nfe/certificado');

        } catch (Exception $e) {
            log_message('error', 'Erro ao processar certificado: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Erro ao processar certificado: ' . $e->getMessage());
            redirect(base_url() . 'nfe/certificado');
        }
    }

    public function saveConfigurations()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cConfiguracao')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar as configurações.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('tipo_documento', 'Tipo de Documento', 'required|trim');
        $this->form_validation->set_rules('ambiente', 'Ambiente', 'required|trim');
        $this->form_validation->set_rules('versao_nfe', 'Versão NFe', 'required|trim');
        $this->form_validation->set_rules('tipo_impressao_danfe', 'Tipo de Impressão DANFE', 'required|trim');
        $this->form_validation->set_rules('orientacao_danfe', 'Orientação DANFE', 'required|trim');
        $this->form_validation->set_rules('sequencia_nota', 'Sequência de Número de Nota', 'required|trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('sequencia_nfce', 'Sequência de Número de Nota NFC-e', 'required|trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('csc', 'csc', 'trim');
        $this->form_validation->set_rules('csc_id', 'CSC ID', 'trim');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url() . 'index.php/nfe/configuracoes');
        }

        $data = [
            'tipo_documento' => $this->input->post('tipo_documento'),
            'ambiente' => $this->input->post('ambiente'),
            'versao_nfe' => $this->input->post('versao_nfe'),
            'tipo_impressao_danfe' => $this->input->post('tipo_impressao_danfe'),
            'orientacao_danfe' => $this->input->post('orientacao_danfe'),
            'sequencia_nota' => $this->input->post('sequencia_nota'),
            'sequencia_nfce' => $this->input->post('sequencia_nfce'),
            'csc' => $this->input->post('csc'),
            'csc_id' => $this->input->post('csc_id')
        ];

        $configurations = $this->nfe->getConfigurations();
        if ($configurations) {
            $this->nfe->updateConfigurations($configurations->id, $data);
        } else {
            $this->nfe->saveConfigurations($data);
        }

        $this->session->set_flashdata('success', 'Configurações salvas com sucesso!');
        redirect(base_url() . 'index.php/nfe/configuracoes');
    }

    public function buscarVendas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NF-e.');
            redirect(base_url());
        }

        $this->load->model('vendas_model');
        
        $this->db->select('vendas.*, clientes.nomeCliente, operacao_comercial.nome as nome_operacao,
            (SELECT SUM(quantidade * preco) FROM itens_de_vendas WHERE vendas_id = vendas.idVendas) as valor_total');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
        $this->db->join('operacao_comercial', 'operacao_comercial.id = vendas.operacao_comercial_id', 'left');
        $this->db->where('vendas.status', 'Faturado');
        $this->db->where('vendas.emitida_nfe', 0);
        $this->db->order_by('vendas.idVendas', 'desc');
        
        $this->data['vendas'] = $this->db->get()->result();
        
        $this->data['view'] = 'nfe/buscarVendas';
        return $this->layout();
    }

    public function emitirNota()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
        }

        $venda_id = $this->input->post('venda_id');
        if (empty($venda_id)) {
            $this->session->set_flashdata('error', 'Venda não informada.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Carrega dados da venda
        $venda = $this->Vendas_model->getById($venda_id);
        if (!$venda) {
            $this->session->set_flashdata('error', 'Venda não encontrada.');
            redirect(base_url() . 'index.php/nfe/buscarVendas');
        }

        // Carrega dados do cliente
        $cliente = $this->Vendas_model->getCliente($venda_id);
        if (!$cliente) {
            $this->session->set_flashdata('error', 'Cliente não encontrado.');
            redirect(base_url() . 'index.php/nfe/buscarVendas');
        }

        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            $this->session->set_flashdata('error', 'Emitente não configurado.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Carrega produtos da venda
        $produtos = $this->Vendas_model->getProdutos($venda_id);
        if (empty($produtos)) {
            $this->session->set_flashdata('error', 'Venda sem produtos.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Carrega configurações da NFe
        $this->db->where('idConfiguracao', 1);
        $configNFe = $this->db->get('configuracoes_nfe')->row();
        if (!$configNFe) {
            $this->session->set_flashdata('error', 'Configurações de NFe não encontradas. Configure as configurações de NFe primeiro.');
            redirect(base_url() . 'index.php/nfe/configuracoesNFe');
        }

        try {
            // Cria objeto NFe
            $nfe = new Make();

            $this->load->model('Mapos_model');
            $this->load->model('Vendas_model');
            $this->load->model('Clientes_model');
            $this->load->model('Produtos_model');
            $this->load->model('ClassificacaoFiscal_model');
            $this->load->model('OperacaoComercial_model');
            $this->load->model('TributacaoProduto_model');
    
            // [infNFe]
            $std = new \stdClass();
            $std->versao = '4.00';
            $nfe->taginfNFe($std);
            if (!isset($operacao) || !$operacao) {
                $operacao = $this->OperacaoComercial_model->getById($venda->operacao_comercial_id);
                if (!$operacao) {
                    $this->session->set_flashdata('error', 'Operação comercial não encontrada.');
                    redirect(base_url('vendas'));
                }
            }
     

            // [ide]
            $std = new \stdClass();
            $std->cUF = $this->get_cUF($emitente->uf); // Código IBGE da UF
            $std->cNF = rand(10000000, 99999999);
            $std->natOp = $operacao->nome_operacao;
            $std->mod = 55;
            $std->serie = 1;
            $std->nNF = $configNFe->sequencia_nota;
            $std->dhEmi = date('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
            $std->tpNF = 1;
            $std->idDest = ($cliente->estado != $emitente->uf) ? '2' : '1'; // 1=Interna, 
            $std->cMunFG = $emitente->ibge;
            $std->tpImp = $configNFe->tipo_impressao_danfe;
            $std->tpEmis = 1;
            $std->cDV = 0;
            $std->tpAmb = $configNFe->ambiente;
            $std->finNFe = 1;
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = $configNFe->versao_nfe;

            // Gera o código numérico da NFe
            $cUF = $this->get_cUF($emitente->uf);
            $ano = date('y');
            $mes = date('m');
            $cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $mod = '55';
            $serie = str_pad('1', 3, '0', STR_PAD_LEFT);
            $numero = str_pad($configNFe->sequencia_nota, 9, '0', STR_PAD_LEFT);
            $tpEmis = '1';
            $codigo = $cUF . $ano . $mes . $cnpj . $mod . $serie . $numero . $tpEmis;
            
            // Calcula o DV
            $dv = $this->calculaDV($codigo);
            $codigo .= $dv;
            
            // Define o código numérico
            $std->cNF = substr($codigo, -8);
            $std->cDV = $dv;
            
            $nfe->tagide($std);

            $this->db->select('valor');
            $this->db->from('configuracoes');
            $this->db->where('config', 'regime_tributario');
            $this->db->limit(1);
            $regime = $this->db->get()->row();
            $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;

            // [emit]
            $std = new \stdClass();
            $std->xNome = $emitente->nome;
            $std->xFant = $emitente->nome;
            $std->ie = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
            $std->CRT = $this->crt;
            $std->cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $nfe->tagemit($std);

            // [enderEmit]
            $std = new \stdClass();
            $std->xLgr = $emitente->rua;
            $std->nro = $emitente->numero;
            if (!empty($emitente->complemento)) {
                $std->xCpl = $emitente->complemento;
            }
            $std->xBairro = $emitente->bairro;
            $std->cMun = $emitente->ibge;
            $std->xMun = $emitente->cidade;
            $std->uf = $emitente->uf;
            $std->cep = preg_replace('/[^0-9]/', '', $emitente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
            $nfe->tagenderEmit($std);

            // [dest]
            $std = new \stdClass();
            $std->xNome = $cliente->nomeCliente;
            if (strlen(preg_replace('/[^0-9]/', '', $cliente->documento)) == 11) {
                $std->cpf = preg_replace('/[^0-9]/', '', $cliente->documento);
                $std->indIEDest = 9; // Contribuinte Isento
            } else {
                $std->cnpj = preg_replace('/[^0-9]/', '', $cliente->documento);
                if (!empty($cliente->inscricao_estadual)) {
                    $std->ie = $cliente->inscricao_estadual;
                    $std->indIEDest = 1; // Contribuinte
                } else {
                    $std->indIEDest = 9; // Contribuinte Isento
                }
            }
            $nfe->tagdest($std);

            // [enderDest]
            $std = new \stdClass();
            $std->xLgr = $cliente->rua;
            $std->nro = $cliente->numero;
            if (!empty($cliente->complemento)) {
            $std->xCpl = $cliente->complemento;
            }
            $std->xBairro = $cliente->bairro;
            
            // Garante que o código do município (IBGE) está presente
            if (empty($cliente->ibge)) {
                throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
            }
            
            $std->cMun = $cliente->ibge;
            $std->xMun = $cliente->cidade;
            $std->uf = $cliente->estado;
            $std->cep = preg_replace('/[^0-9]/', '', $cliente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            if (!empty($cliente->telefone)) {
            $std->fone = preg_replace('/[^0-9]/', '', $cliente->telefone);
            }
            $nfe->tagenderDest($std);

           // Produtos
           $i = 1;
           $totalProdutos = 0;
           $totalICMS = 0; // Inicializa o total de ICMS
           
           foreach ($produtos as $p) {
               $emitente = $this->Mapos_model->getEmitente();
               $cliente = $this->Clientes_model->getById($venda->clientes_id);
               // Buscar tributação usando os parâmetros corretos
               $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
               $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                   $venda->operacao_comercial_id,
                   $cliente->natureza_contribuinte,
                   $destinacao, // destinação: Estadual ou Interestadual
                   $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
               );

               if (!$tributacao) {
                   $this->session->set_flashdata('error', 'Não foi encontrada tributação para a operação comercial selecionada na venda. Por favor, verifique se existe uma classificação fiscal cadastrada com os parâmetros: Operação Comercial ID: ' . $venda->operacao_comercial_id . ', Natureza Contribuinte: ' . $cliente->natureza_contribuinte . ', Destinação: ' . $destinacao . ', Objetivo Comercial: ' . $cliente->objetivo_comercial);
                   redirect(base_url('vendas'));
               }

               // Definir CFOP e CST/CSOSN baseado na tributação encontrada;
               if (strtolower($this->crt) === 'simples nacional') {
                   $p->csosn = $tributacao->csosn;
                   $p->cst = null;
               } else {
                   $p->cst = $tributacao->cst;
                   $p->csosn = null;
               }

               // Buscar tributação do produto
               if ($p->tributacao_produto_id) {
                   $tributacao_produto = $this->TributacaoProduto_model->getById($p->tributacao_produto_id);
                   if ($tributacao_produto) {
                       // Adicionar informações de impostos do produto
                       $p->cst_ipi = $tributacao_produto->cst_ipi_saida;
                       $p->aliq_ipi = $tributacao_produto->aliq_ipi_saida;
                       $p->cst_pis = $tributacao_produto->cst_pis_saida;
                       $p->aliq_pis = $tributacao_produto->aliq_pis_saida;
                       $p->cst_cofins = $tributacao_produto->cst_cofins_saida;
                       $p->aliq_cofins = $tributacao_produto->aliq_cofins_saida;
                       $p->regime_fiscal = $tributacao_produto->regime_fiscal_tributario;
                   }
               }

               // Calcula o valor do ICMS
               $valor_icms = 0;
               if ($this->crt == 3) { // Lucro Real
                   // Carrega o model de Alíquotas se ainda não foi carregado
                   $this->load->model('Aliquotas_model');
                   
              
                   $is_estadual = ($cliente->estado == $emitente->uf);
                   
                   
                   // Busca a alíquota correspondente
                   $aliquota = $this->Aliquotas_model->getAliquota($emitente->uf, $cliente->estado);
                   
                   if (!$aliquota) {
                       $this->session->set_flashdata('error', 'Alíquotas não configuradas no sistema para a operação entre ' . $emitente->uf . ' e ' . $cliente->estado);
                       redirect(base_url() . 'index.php/vendas');
                       return;
                   }
                   
                   // Define a alíquota baseado no tipo de operação
                   $aliq = $is_estadual ? $aliquota->aliquota_origem : $aliquota->aliquota_destino;
                   $valor_icms = ($p->preco * $p->quantidade * $aliq) / 100;
                    if(!$valor_icms) {
                        $this->session->set_flashdata('error', 'Alíquotas não configuradas' . $emitente->uf . ' e ' . $cliente->estado);
                        redirect(base_url() . 'index.php/vendas');
                        return;
                    }
                   // Se for operação interestadual e cliente não inscrito, calcula o DIFAL
                   if (!$is_estadual && $cliente->natureza_contribuinte === 'nao_inscrito') {
                       $vBCUFDest = $p->preco * $p->quantidade; // Base de cálculo
                       
                       // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                       if ($p->origem == 1 || $p->origem == 2 || $p->origem == 3 || $p->origem == 8) {
                           $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                           $pICMSInter = 4.00;
                       } else {
                           $pICMSInter = $aliq; // Alíquota interestadual normal
                       }
                       
                       // Busca a alíquota do estado de destino (estado do cliente)
                       $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                       if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                           $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                           redirect(base_url() . 'index.php/vendas');
                           return;
                       }
                       
                       $pICMSUFDest = $aliquota_destino->aliquota_origem; // Alíquota interna UF destino
                       
                       // Calcula o DIFAL - primeiro a diferença das alíquotas, depois aplica na base
                       $difAliquotas = $pICMSUFDest - $pICMSInter; // Exemplo: 19% - 12% = 7%
                       $difal = ($difAliquotas * $vBCUFDest) / 100; // Aplica a diferença na base
                       
                       // Adiciona a tag ICMSUFDest
                       $std = new \stdClass();
                       $std->item = $i;
                       $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                       $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                       $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                       $std->pICMSInterPart = 100.00;
                       $std->vICMSUFDest = number_format($difal, 2, '.', '');
                       $std->vICMSUFRemet = '0.00';
                       $nfe->tagICMSUFDest($std);

                       // Adiciona o DIFAL no obsCont e infAdic se for maior que 0
                       if ($difal > 0) {
                           $mensagemDIFAL = "DIFAL: R$ " . number_format($difal, 2, ',', '.') . 
                                          " (Diferença entre alíquotas: " . number_format($difAliquotas, 2, ',', '.') . "%)";

                           // Escapar o texto para garantir que o XML seja válido
                           $mensagemDIFAL = htmlspecialchars($mensagemDIFAL, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                           
                           // Armazena a mensagem DIFAL para ser usada depois no infCpl geral
                           if (!isset($this->mensagensDIFAL)) {
                               $this->mensagensDIFAL = [];
                           }
                           $this->mensagensDIFAL[] = $mensagemDIFAL;
                       }
                   } else if (!$is_estadual) {
                       // Se for operação interestadual mas cliente é inscrito, ainda precisa gerar ICMSUFDest
                       $vBCUFDest = $p->preco * $p->quantidade;
                       
                       // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                       if ($p->origem == 1 || $p->origem == 2 || $p->origem == 3 || $p->origem == 8) {
                           $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                           $pICMSInter = 4.00;
                       } else {
                           $pICMSInter = $aliq; // Alíquota interestadual normal
                       }
                       
                       $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                       if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                           $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                           redirect(base_url() . 'index.php/vendas');
                           return;
                       }
                       
                       $pICMSUFDest = $aliquota_destino->aliquota_origem;
                       
                       $std = new \stdClass();
                       $std->item = $i;
                       $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                       $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                       $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                       $std->pICMSInterPart = 100.00;
                       $std->vICMSUFDest = '0.00';
                       $std->vICMSUFRemet = '0.00';
                       $nfe->tagICMSUFDest($std);
                   }
               }
               
               $natureza_contribuinte = $this->input->post('natureza_contribuinte');
               $emitente = $this->Mapos_model->getEmitente();
               $cliente = $this->Clientes_model->getById($venda->clientes_id);
                  // Buscar tributação usando os parâmetros corretos
                  $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
                  $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                      $venda->operacao_comercial_id,
                      $cliente->natureza_contribuinte,
                      $destinacao, // destinação: Estadual ou Interestadual
                      $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
                  );
               // [prod]
               $std = new \stdClass();
               $std->item = $i;
               $std->cProd = $p->idProdutos;
               $std->cEAN = 'SEM GTIN';
               $std->cEANTrib = 'SEM GTIN'; // Adicionado o campo cEANTrib
               $std->xProd = $p->descricao;
               $std->NCM = $p->NCMs;
               $std->cfop = $tributacao->cfop;
               $std->uCom = 'UN';
               $std->qCom = $p->quantidade;
               $std->vUnCom = $p->preco;
               $std->vProd = $p->preco * $p->quantidade;
               // Campos obrigatórios adicionais
               $std->uTrib = 'UN'; // Unidade Tributável
               $std->qTrib = $p->quantidade; // Quantidade Tributável
               $std->vUnTrib = $p->preco; // Valor Unitário de tributação
               $std->indTot = 1; // Indica se valor do Item entra no valor total da NF-e
               $std->cEAN = 'SEM GTIN'; // Código de barras
               $std->cEANTrib = 'SEM GTIN'; // Código de barras da unidade tributável
               $nfe->tagprod($std);

               // [imposto]
               $std = new \stdClass();
               $std->item = $i;
             $nfe->tagimposto($std); // Sempre necessário abrir o bloco com tagimposto()

               // ICMS
               $std = new \stdClass();
               $std->item = $i;
             $std->orig = $p->origem;
             
             if ($this->crt == 1) {
                 // Simples Nacional
                   $std->CSOSN = isset($p->csosn) ? $p->csosn : '102';
                 
                 // Calcula o valor do crédito ICMS para qualquer CSOSN
                 $vBC = number_format($p->preco * $p->quantidade, 2, '.', '');
                 
                 // Busca a alíquota de crédito ICMS das configurações
                 $this->db->select('valor');
                 $this->db->from('configuracoes');
                 $this->db->where('config', 'aliq_cred_icms');
                 $this->db->limit(1);
                 $aliq_cred = $this->db->get()->row();
                 $pCredSN = $aliq_cred ? str_replace(',', '.', $aliq_cred->valor) : 3.00;
                 
                 $std->pCredSN = number_format($pCredSN, 2, '.', '');
                 $std->vCredICMSSN = number_format(($vBC * $pCredSN) / 100, 2, '.', '');
                 
                   $nfe->tagICMSSN($std);
               } else {
                 // Lucro Real ou Presumido
                   if (!isset($p->cst)) {
                       throw new Exception('CST não configurado na classificação fiscal');
                   }
                   $std->cst = isset($p->cst) ? $p->cst : '00';
                   $std->modBC = 3;
                   $std->vBC = $p->preco * $p->quantidade;
                   $std->pICMS = $aliq;   
                   $std->vICMS = ($std->vBC * $std->pICMS) / 100;
                   $nfe->tagICMS($std);
                 $valor_icms = $std->vICMS;
               }

               // PIS
               $std = new \stdClass();
               $std->item = $i;
               $std->cst = isset($p->cst_pis) ? $p->cst_pis : '01';
               $base_calculo = $p->preco * $p->quantidade;
               $std->vBC = number_format($base_calculo, 2, '.', '');
               $std->pPIS = isset($p->aliq_pis) ? $p->aliq_pis : 0;
               $std->vPIS = number_format(($base_calculo * $std->pPIS) / 100, 2, '.', '');
               $nfe->tagPIS($std);

               // COFINS
               $std = new \stdClass();
               $std->item = $i;
               $std->cst = isset($p->cst_cofins) ? $p->cst_cofins : '01';
               $std->vBC = number_format($base_calculo, 2, '.', '');
               $std->pCOFINS = isset($p->aliq_cofins) ? $p->aliq_cofins : 0;
               $std->vCOFINS = number_format(($base_calculo * $std->pCOFINS) / 100, 2, '.', '');
               $nfe->tagCOFINS($std);

               // Atualiza totais
               $totalProdutos += floatval($p->quantidade) * floatval($p->preco);
             if (!isset($valor_icms)) $valor_icms = 0;
             $totalICMS += $valor_icms;
               $i++;
           }

            // Adiciona todas as mensagens DIFAL no infCpl geral da nota
            if (!empty($this->mensagensDIFAL)) {
            $std = new \stdClass();
                $std->infAdFisco = '';
                $std->infCpl = "DADOS ADICIONAIS\n\n" . implode("\n", $this->mensagensDIFAL);
                $nfe->taginfAdic($std);
            } else {
                // Adiciona mensagem padrão sobre crédito ICMS para Simples Nacional apenas se for contribuinte
                if ($this->crt == 1 && isset($dest->indIEDest) && $dest->indIEDest == 1) {
                    // Busca a mensagem do Simples Nacional das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'mensagem_simples_nacional');
                    $this->db->limit(1);
                    $mensagem = $this->db->get()->row();
                    
                    $std = new \stdClass();
                    $std->infAdFisco = '';
                    
                    // Calcula o valor do crédito ICMS usando o total
                    $total = number_format($p->preco * $p->quantidade, 2, '.', '');
                    $vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                    
                    $mensagemTexto = $mensagem ? $mensagem->valor : "CRÉDITO ICMS SIMPLES NACIONAL\nConforme Art. 23 da LC 123/2006, o valor do crédito de ICMS corresponde a [percentual]% da base de cálculo do Simples Nacional.";
                    
                    // Substitui os placeholders na mensagem
                    $mensagemTexto = str_replace('[percentual]', number_format($pCredSN, 2, ',', '.'), $mensagemTexto);
                    $mensagemTexto = str_replace('[valor]', number_format($vCredICMSSN, 2, ',', '.'), $mensagemTexto);
                    
                    $std->infCpl = "DADOS ADICIONAIS\n\n" . $mensagemTexto;
                $nfe->taginfAdic($std);
                }
           }

            // [total]
            $std = new \stdClass();
            if ($this->crt == 1) {
            $std->vBC = number_format(0, 2, '.', '');
            $std->vICMS = number_format(0, 2, '.', '');
            } else {
            $std->vBC = number_format($totalProdutos, 2, '.', '');
            $std->vICMS = number_format($totalICMS, 2, '.', '');
            }
            $std->vICMSDeson = 0;
            $std->vBCST = 0;
            $std->vST = 0;
            $std->vProd = number_format($totalProdutos, 2, '.', '');
            $std->vFrete = 0;
            $std->vSeg = 0;
            $std->vDesc = 0;
            $std->vII = 0;
            $std->vIPI = 0;
            $std->vPIS = 0;
            $std->vCOFINS = 0;
            $std->vOutro = 0;
            $std->vNF = number_format($totalProdutos, 2, '.', '');
            $std->vTotTrib = 0;
            $nfe->tagICMSTot($std);

            // [transp]
            $std = new \stdClass();
            $std->modFrete = 9;
            
            // Add transportadora information if available
            if (isset($transportadora) && !empty($transportadora)) {
                $std->transporta = new \stdClass();
                $std->transporta->cnpj = preg_replace('/[^0-9]/', '', $transportadora->documento);
                $std->transporta->xNome = $transportadora->nomeCliente;
                $std->transporta->ie = !empty($transportadora->ie) ? $transportadora->ie : 'ISENTO';
                $std->transporta->xEnder = $transportadora->rua;
                $std->transporta->xMun = $transportadora->cidade;
                $std->transporta->uf = $transportadora->estado;
            }
            
            $nfe->tagtransp($std);

            // [pag]
            $std = new \stdClass();
            $std->indPag = 0; // 0 = pagamento à vista
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagpag($std);

            // [detPag]
            $std = new \stdClass();
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagdetPag($std);

            try {
                $xml = $nfe->getXML();
                
                // Obtém o objeto Tools com o certificado
                $tools = $this->getTools();

                // Verifica se há erros nas tags antes de prosseguir
                $errors = $nfe->getErrors();
                if (!empty($errors)) {
                    $errorMessage = "Erros encontrados no XML da NFC-e:\n";
                    foreach ($errors as $error) {
                        $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                    }
                    throw new Exception($errorMessage);
                }

                // Assina o XML
                $signed = $tools->signNFe($xml);

                // Envia para a SEFAZ
                $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
                $response = $tools->sefazEnviaLote([$signed], $idLote);
                $st = new \NFePHP\NFe\Common\Standardize($response);
                $std = $st->toStd();
                
                if ($std->cStat != 103) {
                    throw new Exception("Erro ao enviar NFe: " . $std->xMotivo);
                }

                $recibo = $std->infRec->nRec;
                
                // Aguarda o processamento do lote
                $tentativas = 0;
                $maxTentativas = 10;
                $status = 0;
                $chave_retorno_evento = '';
                
                while ($tentativas < $maxTentativas) {
                    // Consulta recibo
                    $protocolo = $tools->sefazConsultaRecibo($recibo);
                    
                    // Log da resposta bruta
                    log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                    
                    $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                    $std = $st->toStd();
                    
                    // Log do objeto padronizado
                    log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                    
                    // Verifica se o lote ainda está em processamento
                    if (isset($std->cStat) && $std->cStat == '105') {
                        // Lote em processamento, aguarda e tenta novamente
                        sleep(2);
                        $tentativas++;
                        continue;
                    }
                    
                    // Extrai o status real da NFe do protocolo
                    $dom = new \DOMDocument();
                    $dom->loadXML($protocolo);
                    
                    // Procura por diferentes tags possíveis
                    $infProt = $dom->getElementsByTagName('infProt')->item(0);
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                    }
                    
                    if (!$infProt) {
                        // Tenta extrair informações diretamente do objeto padronizado
                        if (isset($std->cStat)) {
                            $status = ($std->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsReciNFe->cStat)) {
                            $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsSitNFe->cStat)) {
                            $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                        } else {
                            throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                        }
                    } else {
                        $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                        $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                        
                        if ($cStat && $cStat->nodeValue == '100') {
                            $status = 1;
                            $chave_retorno_evento = "Autorizado o uso da NF-e";
                        } else {
                            $status = 0;
                            $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                        }
                    }
                    
                    // Se encontrou o status, sai do loop
                    if ($status != 0 || $chave_retorno_evento != '') {
                        break;
                    }
                    
                    // Aguarda antes da próxima tentativa
                    sleep(2);
                    $tentativas++;
                }
                
                if ($tentativas >= $maxTentativas) {
                    throw new Exception("Tempo limite excedido ao aguardar processamento do lote");
                }

                // Log da resposta bruta
                log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                
                $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                $std = $st->toStd();
                
                // Log do objeto padronizado
                log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                
                // Extrai o status real da NFe do protocolo
                $dom = new \DOMDocument();
                $dom->loadXML($protocolo);
                
                // Procura por diferentes tags possíveis
                $infProt = $dom->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                }
                
                if (!$infProt) {
                    // Tenta extrair informações diretamente do objeto padronizado
                    if (isset($std->cStat)) {
                        $status = ($std->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsReciNFe->cStat)) {
                        $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsSitNFe->cStat)) {
                        $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                    } else {
                        throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                    }
                } else {
                    $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                    $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                    
                    if ($cStat && $cStat->nodeValue == '100') {
                        $status = 1;
                        $chave_retorno_evento = "Autorizado o uso da NF-e";
                    } else {
                        $status = 0;
                        $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                    }
                }

                // Extrai o número da nota e chave do XML
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                if (!$infNFe) {
                    throw new Exception("Erro ao extrair informações da NFe: tag infNFe não encontrada");
                }
                
                $ide = $infNFe->getElementsByTagName('ide')->item(0);
                if (!$ide) {
                    throw new Exception("Erro ao extrair informações da NFe: tag ide não encontrada");
                }
                
                $nNF = $ide->getElementsByTagName('nNF')->item(0);
                if (!$nNF) {
                    throw new Exception("Erro ao extrair informações da NFe: tag nNF não encontrada");
                }
                $numero_nfe = $nNF->nodeValue;

                // Extrai a chave da NFe
                $chNFe = $infNFe->getAttribute('Id');
                if ($chNFe) {
                    $chNFe = str_replace('NFe', '', $chNFe); // Remove o prefixo 'NFe'
                }

                // Mostra a resposta da SEFAZ para análise
                log_message('debug', 'Resposta da SEFAZ: ' . $protocolo);

                // Salva NFe emitida
                $nfeData = [
                    'venda_id' => (int)$venda_id,
                    'modelo' => 55, // Modelo NFe
                    'numero_nfe' => (string)$numero_nfe,
                    'chave_nfe' => (string)$chNFe,
                    'xml' => (string)$signed,
                    'xml_protocolo' => (string)$protocolo,
                    'status' => $status,
                    'chave_retorno_evento' => $chave_retorno_evento,
                    'protocolo' => '', // Será preenchido posteriormente
                    'valor_total' => $totalProdutos, // Adiciona o valor total dos itens devolvidos
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Insere na tabela nfe_emitidas
                $this->db->insert('nfe_emitidas', $nfeData);
                
                if ($this->db->affected_rows() == 0) {
                    throw new Exception('Erro ao salvar NFe no banco de dados');
                }

                // Atualiza o campo emitida_nfe na tabela vendas
                $this->db->where('idVendas', $venda_id);
                $this->db->update('vendas', ['emitida_nfe' => true]);

                // Atualiza a sequência da nota
                $this->db->where('idConfiguracao', 1);
                $this->db->update('configuracoes_nfe', [
                    'sequencia_nota' => $configNFe->sequencia_nota + 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Prepara dados para o modal
                $modalData = [
                    'status' => ($status == 1) ? 'Autorizada' : 'Rejeitada',
                    'motivo' => $chave_retorno_evento,
                    'protocolo' => $protocolo,
                    'xml' => $signed,
                    'chave_nfe' => $chNFe,
                    'numero_nfe' => $numero_nfe,
                    'modelo' => 55 // Modelo NFe
                ];
                
                $configNFe = $this->db->get('configuracoes_nfe')->row();
                if ($configNFe && isset($configNFe->preview_nfe) && $configNFe->preview_nfe) {
                    $nfe_emitida = $this->db->order_by('id', 'desc')->get('nfe_emitidas')->row();
                    if ($nfe_emitida) {
                        $this->session->set_flashdata('preview_nfe_id', $nfe_emitida->id);
                    }
                } else {
                    $this->session->set_flashdata('nfe_modal', $modalData);
                    $this->session->set_flashdata('success', 'Nota fiscal emitida com sucesso!');
                }
                redirect(base_url() . 'index.php/nfe/gerenciar');

            } catch (Exception $e) {
                $errors = $nfe->getErrors();
                $errorMessage = $e->getMessage();
                
                if (!empty($errors)) {
                    $errorMessage .= " - Erros nas tags: " . implode(", ", array_map(function($error) {
                        return str_replace('"', '', $error);
                    }, $errors));
                }
                
                // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
                $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
                $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
                $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
                redirect(base_url() . 'index.php/nfe');
            }

        } catch (Exception $e) {
            // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
            $errorMessage = $e->getMessage();
            $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
            $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
            $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
            redirect(base_url() . 'index.php/nfe');
        }
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFe.');
            redirect(base_url());
        }

        // Aplica filtros
        $where = array();
        
        // Filtro de data
        if ($this->input->get('data_inicio') && $this->input->get('data_fim')) {
            $where['created_at >='] = $this->input->get('data_inicio') . ' 00:00:00';
            $where['created_at <='] = $this->input->get('data_fim') . ' 23:59:59';
        }
        
        // Filtro de chave
        if ($this->input->get('chave')) {
            $where['chave_nfe LIKE'] = '%' . $this->input->get('chave') . '%';
        }
        
        // Filtro de cliente
        if ($this->input->get('cliente')) {
            $this->db->like('nomeCliente', $this->input->get('cliente'));
        }
        
        // Filtro de número
        if ($this->input->get('numero')) {
            $where['numero_nfe'] = $this->input->get('numero');
        }
        
        // Filtro de modelo
        if ($this->input->get('modelo')) {
            $where['modelo'] = $this->input->get('modelo');
        }
        
        // Filtro de status
        if ($this->input->get('status') !== null && $this->input->get('status') !== '') {
            $where['status'] = (int)$this->input->get('status');
        }
        
        $this->data['nfe'] = $this->nfe->getNfe($where);
        $this->data['view'] = 'nfe/gerenciar';
        $this->layout();
    }

    public function visualizar($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFe.');
            redirect(base_url());
        }

        $this->data['nfe'] = $this->nfe->getNfe($id);
        if (!$this->data['nfe']) {
            $this->session->set_flashdata('error', 'NFe não encontrada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        $this->data['view'] = 'nfe/visualizar';
        $this->layout();
    }

    public function cancelar()
    {
        try {
            // Inicia o buffer de saída
            ob_start();
            
            // Verifica se é uma requisição POST
            if ($this->input->method() !== 'post') {
                throw new Exception('Método de requisição inválido. Use POST.');
            }

            // Verifica permissão
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
                throw new Exception('Você não tem permissão para cancelar NFe.');
            }

            // Verifica certificado digital
            $certificate = $this->nfe->getCertificate();
            if (!$certificate) {
                throw new Exception('Certificado digital não configurado. Configure o certificado nas configurações do sistema.');
            }

            // Verifica se o certificado está vencido
            $dataValidade = new DateTime($certificate->data_validade);
            $hoje = new DateTime();
            if ($hoje > $dataValidade) {
                throw new Exception('O certificado digital está vencido. Por favor, atualize o certificado nas configurações do sistema.');
            }

            // Obtém dados do POST
            $nfe_id = $this->input->post('nfe_id');
            $justificativa = $this->input->post('justificativa');

            // Validações básicas
            if (empty($nfe_id)) {
                throw new Exception('ID da NFe não informado');
            }

            if (empty($justificativa)) {
                throw new Exception('Justificativa não informada');
            }

            if (strlen($justificativa) < 15) {
                throw new Exception('A justificativa deve ter no mínimo 15 caracteres');
            }

            // Busca NFe
            $nfe = $this->nfe->getNfe($nfe_id);
            if (!$nfe) {
                throw new Exception('NFe não encontrada');
            }

            if ($nfe->status != 1) {
                throw new Exception('Apenas NFe autorizadas podem ser canceladas. Status atual: ' . $nfe->status);
            }

            if (empty($nfe->chave_nfe)) {
                throw new Exception('Chave da NFe não encontrada');
            }

            // Verifica se tem protocolo, se não tiver, tenta consultar a SEFAZ
            if (empty($nfe->protocolo)) {
                try {
                    $tools = $this->getTools();
                    $response = $tools->sefazConsultaChave($nfe->chave_nfe);
                    
                    if (empty($response)) {
                        throw new Exception('Resposta vazia da SEFAZ ao consultar protocolo');
                    }
                    
                    $st = new \NFePHP\NFe\Common\Standardize($response);
                    $std = $st->toStd();
                    
                    if (isset($std->protNFe->infProt->nProt)) {
                        $nfe->protocolo = $std->protNFe->infProt->nProt;
                    } else {
                        throw new Exception('Não foi possível obter o protocolo da NFe na SEFAZ');
                    }
                } catch (Exception $e) {
                    throw new Exception('Erro ao consultar protocolo na SEFAZ: ' . $e->getMessage());
                }
            }

            // Carrega ferramentas
            try {
                // Obtém o objeto Tools com o certificado
                $tools = $this->getTools();

                // Envia o evento de cancelamento usando a biblioteca NFePHP
                $response = $tools->sefazCancela(
                    $nfe->chave_nfe,
                    $justificativa,
                    $nfe->protocolo
                );

                // Log da resposta do cancelamento
                log_message('debug', 'Resposta do cancelamento: ' . $response);

                if (empty($response)) {
                    throw new Exception('Resposta vazia da SEFAZ');
                }

                // Processa resposta
                $st = new \NFePHP\NFe\Common\Standardize($response);
                $std = $st->toStd();

                // Log da resposta processada
                log_message('debug', 'Resposta processada: ' . json_encode($std));

                // Verifica se é uma resposta de consulta
                if (isset($std->retConsSitNFe)) {
                    // É uma resposta de consulta, não de evento
                    throw new Exception('Resposta inválida: Recebida resposta de consulta em vez de evento de cancelamento');
                }

                // Verifica resposta do evento
                if (!isset($std->retEvento) || !isset($std->retEvento->infEvento)) {
                    throw new Exception('Resposta inválida da SEFAZ: ' . $response);
                }

                // Verifica o status do evento de cancelamento
                if ($std->retEvento->infEvento->cStat != 135) {
                    throw new Exception('Erro na SEFAZ: ' . (isset($std->retEvento->infEvento->xMotivo) ? $std->retEvento->infEvento->xMotivo : 'Status ' . $std->retEvento->infEvento->cStat));
                }

                // Verifica se é uma NFe de devolução
                $xml = $nfe->xml;
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                $isDevolucao = false;
                if ($infNFe) {
                    $ide = $infNFe->getElementsByTagName('ide')->item(0);
                    if ($ide) {
                        $finNFe = $ide->getElementsByTagName('finNFe')->item(0);
                        if ($finNFe && $finNFe->nodeValue == '4') {
                            $isDevolucao = true;
                        }
                    }
                }

                // Atualiza NFe
                $data = [
                    'status' => 2,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'chave_retorno_evento' => $std->retEvento->infEvento->chNFe,
                    'protocolo' => $std->retEvento->infEvento->nProt
                ];
                
                if (!$this->nfe->update($nfe_id, $data)) {
                    throw new Exception('Erro ao atualizar status da NFe no banco de dados');
                }

                // Cria o documento de cancelamento
                $documento_cancelamento = [
                    'nfe_id' => $nfe_id,
                    'tipo' => 'cancelamento',
                    'justificativa' => $justificativa,
                    'protocolo' => $std->retEvento->infEvento->nProt,
                    'data_evento' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'xml' => $response,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Inicia transação para garantir a integridade dos dados
                $this->db->trans_begin();

                // Insere o documento de cancelamento no banco de dados
                if (!$this->db->insert('nfe_documentos', $documento_cancelamento)) {
                    $this->db->trans_rollback();
                    throw new Exception('Erro ao salvar documento de cancelamento no banco de dados: ' . $this->db->error()['message']);
                }

                // Verifica se a transação foi bem sucedida
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    throw new Exception('Erro na transação do banco de dados');
                }

                // Confirma a transação
                $this->db->trans_commit();

                // Log do documento de cancelamento
                log_message('debug', 'Documento de cancelamento criado com sucesso: ' . json_encode($documento_cancelamento));

                // Limpa o buffer de saída
                ob_clean();

                // Retorna sucesso
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'NFe cancelada com sucesso!',
                        'data' => [
                            'nfe' => $data,
                            'documento_cancelamento' => $documento_cancelamento,
                            'is_devolucao' => $isDevolucao
                        ]
                    ]));
            } catch (Exception $e) {
                // Limpa o buffer de saída
                ob_clean();

                // Retorna erro
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Erro ao enviar evento de cancelamento: ' . $e->getMessage(),
                        'error' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]
                    ]));
            }
        } catch (Exception $e) {
            // Limpa o buffer de saída
            ob_clean();

            // Retorna erro
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Erro ao cancelar NFe: ' . $e->getMessage(),
                    'error' => [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]
                ]));
        }
    }

    public function atualizarStatusVenda()
    {
        // Adicionar headers para evitar cache
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        $this->output->set_content_type('application/json');

        try {
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
                throw new Exception('Você não tem permissão para atualizar vendas.');
            }

            $nfe_id = $this->input->post('nfe_id');
            $cancelar_venda = $this->input->post('cancelar_venda');

            if (empty($nfe_id)) {
                throw new Exception('ID da NFe não informado.');
            }

            $nfe = $this->nfe->getNfe($nfe_id);
            if (!$nfe) {
                throw new Exception('NFe não encontrada.');
            }

            if (empty($nfe->venda_id)) {
                throw new Exception('ID da venda não encontrado na NFe.');
            }

            // Inicia transação para garantir a integridade dos dados
            $this->db->trans_start();

            // Atualiza o status da venda
            // Se cancelar_venda for true, mantém como Orçamento
            // Se cancelar_venda for false, marca como Cancelada
            $status_venda = $cancelar_venda ? 'Orçamento' : 'Cancelada';
            
            // Prepara os dados para atualização
            $dados_atualizacao = ['status' => $status_venda];
            
            // Se estiver reabrindo a venda (cancelar_venda = true), reseta os campos de faturamento
            if ($cancelar_venda) {
                $dados_atualizacao['emitida_nfe'] = false;
                $dados_atualizacao['faturado'] = false;
            }
            
            $this->db->where('idVendas', $nfe->venda_id);
            $result = $this->db->update('vendas', $dados_atualizacao);

            if (!$result) {
                throw new Exception('Erro ao atualizar status da venda: ' . $this->db->error()['message']);
            }

            // Busca informações da venda para o log
            $this->db->select('vendas.*, usuarios.nome as nome_usuario, clientes.nomeCliente');
            $this->db->from('vendas');
            $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id');
            $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
            $this->db->where('vendas.idVendas', $nfe->venda_id);
            $venda_info = $this->db->get()->row();

            if (!$venda_info) {
                throw new Exception('Informações da venda não encontradas.');
            }

            // Verifica se a tabela de logs existe
            if (!$this->db->table_exists('logs_alteracao_venda')) {
                // Cria a tabela se não existir
                $this->db->query("CREATE TABLE IF NOT EXISTS `logs_alteracao_venda` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `venda_id` int(11) NOT NULL,
                    `status_anterior` varchar(50) NOT NULL,
                    `status_novo` varchar(50) NOT NULL,
                    `usuario` varchar(255) NOT NULL,
                    `cliente` varchar(255) NOT NULL,
                    `valor_total` decimal(10,2) NOT NULL,
                    `data_alteracao` datetime NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            }

            // Prepara dados para o log
            $log_data = [
                'venda_id' => $nfe->venda_id,
                'status_anterior' => $venda_info->status,
                'status_novo' => $status_venda,
                'usuario' => $venda_info->nome_usuario,
                'cliente' => $venda_info->nomeCliente,
                'valor_total' => $venda_info->valorTotal,
                'data_alteracao' => date('Y-m-d H:i:s')
            ];

            // Insere log da alteração
            $result = $this->db->insert('logs_alteracao_venda', $log_data);
            if (!$result) {
                throw new Exception('Erro ao inserir log: ' . $this->db->error()['message']);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception('Erro na transação do banco de dados.');
            }

            $this->db->trans_commit();

            $response = [
                'success' => true,
                'message' => 'Status da venda atualizado com sucesso!',
                'status' => $status_venda,
                'log_data' => $log_data
            ];

        } catch (Exception $e) {
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            }
            
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            
            // Log do erro
            log_message('error', 'Erro ao atualizar status da venda: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    public function reemitir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
        }

        $nfe_id = $this->input->post('nfe_id');
        if (empty($nfe_id)) {
            $this->session->set_flashdata('error', 'NFe não informada.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Busca a NFe original
        $this->db->where('id', $nfe_id);
        $nfe_original = $this->db->get('nfe_emitidas')->row();
        if (!$nfe_original) {
            $this->session->set_flashdata('error', 'NFe original não encontrada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Se for NFCe (modelo 65), usa o método reemitirNFCe
        if ($nfe_original->modelo == 65) {
            return $this->reemitirNFCe();
        }

        // Continua com o código existente para NFe (modelo 55)
        $venda_id = $nfe_original->venda_id;

        // Carrega dados da venda
        $venda = $this->Vendas_model->getById($venda_id);
        if (!$venda) {
            $this->session->set_flashdata('error', 'Venda não encontrada.');
            redirect(base_url() . 'index.php/nfe/buscarVendas');
        }

        // Carrega dados do cliente
        $cliente = $this->Vendas_model->getCliente($venda_id);
        if (!$cliente) {
            $this->session->set_flashdata('error', 'Cliente não encontrado.');
            redirect(base_url() . 'index.php/nfe/buscarVendas');
        }

        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            $this->session->set_flashdata('error', 'Emitente não configurado.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Carrega produtos da venda
        $produtos = $this->Vendas_model->getProdutos($venda_id);
        if (empty($produtos)) {
            $this->session->set_flashdata('error', 'Venda sem produtos.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Carrega configurações da NFe
        $this->db->where('idConfiguracao', 1);
        $configNFe = $this->db->get('configuracoes_nfe')->row();
        if (!$configNFe) {
            $this->session->set_flashdata('error', 'Configurações de NFe não encontradas. Configure as configurações de NFe primeiro.');
            redirect(base_url() . 'index.php/nfe/configuracoesNFe');
        }

        try {
            // Cria objeto NFe
            $nfe = new Make();

            $this->load->model('Mapos_model');
            $this->load->model('Vendas_model');
            $this->load->model('Clientes_model');
            $this->load->model('Produtos_model');
            $this->load->model('ClassificacaoFiscal_model');
            $this->load->model('OperacaoComercial_model');
            $this->load->model('TributacaoProduto_model');
    
            // [infNFe]
            $std = new \stdClass();
            $std->versao = '4.00';
            $nfe->taginfNFe($std);
            if (!isset($operacao) || !$operacao) {
                $operacao = $this->OperacaoComercial_model->getById($venda->operacao_comercial_id);
                if (!$operacao) {
                    $this->session->set_flashdata('error', 'Operação comercial não encontrada.');
                    redirect(base_url('vendas'));
                }
            }
     

            // [ide]
            $std = new \stdClass();
            $std->cUF = $this->get_cUF($emitente->uf); // Código IBGE da UF
            $std->cNF = substr($nfe_original->chave_nfe, 25, 8); // Usa o código numérico da NFe original
            $std->natOp = $operacao->nome_operacao;
            $std->mod = 55;
            $std->serie = 1;
            $std->nNF = $nfe_original->numero_nfe; // Usa o número da NFe original
            $std->dhEmi = date('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
            $std->tpNF = 1;
            $std->idDest = ($cliente->estado != $emitente->uf) ? '2' : '1'; // 1=Interna, 
            $std->cMunFG = $emitente->ibge;
            $std->tpImp = $configNFe->tipo_impressao_danfe;
            $std->tpEmis = 1;
            $std->cDV = substr($nfe_original->chave_nfe, -1); // Usa o DV da NFe original
            $std->tpAmb = $configNFe->ambiente;
            $std->finNFe = 1;
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = $configNFe->versao_nfe;

            // Usa a chave da NFe original
            $chNFe = $nfe_original->chave_nfe;
            
            $nfe->tagide($std);

            $this->db->select('valor');
            $this->db->from('configuracoes');
            $this->db->where('config', 'regime_tributario');
            $this->db->limit(1);
            $regime = $this->db->get()->row();
            $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;

            // [emit]
            $std = new \stdClass();
            $std->xNome = $emitente->nome;
            $std->xFant = $emitente->nome;
            $std->ie = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
            $std->CRT = $this->crt;
            $std->cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $nfe->tagemit($std);

            // [enderEmit]
            $std = new \stdClass();
            $std->xLgr = $emitente->rua;
            $std->nro = $emitente->numero;
            if (!empty($emitente->complemento)) {
                $std->xCpl = $emitente->complemento;
            }
            $std->xBairro = $emitente->bairro;
            $std->cMun = $emitente->ibge;
            $std->xMun = $emitente->cidade;
            $std->uf = $emitente->uf;
            $std->cep = preg_replace('/[^0-9]/', '', $emitente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
            $nfe->tagenderEmit($std);

            // [dest]
            $std = new \stdClass();
            $std->xNome = $cliente->nomeCliente;
            if (strlen(preg_replace('/[^0-9]/', '', $cliente->documento)) == 11) {
                $std->cpf = preg_replace('/[^0-9]/', '', $cliente->documento);
                $std->indIEDest = 9; // Contribuinte Isento
            } else {
                $std->cnpj = preg_replace('/[^0-9]/', '', $cliente->documento);
                if (!empty($cliente->inscricao_estadual)) {
                    $std->ie = $cliente->inscricao_estadual;
                    $std->indIEDest = 1; // Contribuinte
                } else {
                    $std->indIEDest = 9; // Contribuinte Isento
                }
            }
            $nfe->tagdest($std);

            // [enderDest]
            $std = new \stdClass();
            $std->xLgr = $cliente->rua;
            $std->nro = $cliente->numero;
            if (!empty($cliente->complemento)) {
            $std->xCpl = $cliente->complemento;
            }
            $std->xBairro = $cliente->bairro;
            
            // Garante que o código do município (IBGE) está presente
            if (empty($cliente->ibge)) {
                throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
            }
            
            $std->cMun = $cliente->ibge;
            $std->xMun = $cliente->cidade;
            $std->uf = $cliente->estado;
            $std->cep = preg_replace('/[^0-9]/', '', $cliente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            if (!empty($cliente->telefone)) {
            $std->fone = preg_replace('/[^0-9]/', '', $cliente->telefone);
            }
            $nfe->tagenderDest($std);

           // Produtos
           $i = 1;
           $totalProdutos = 0;
           $totalICMS = 0; // Inicializa o total de ICMS
           
           foreach ($produtos as $p) {
            $emitente = $this->Mapos_model->getEmitente();
            $cliente = $this->Clientes_model->getById($venda->clientes_id);
               // Buscar tributação usando os parâmetros corretos
               $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
               $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                   $venda->operacao_comercial_id,
                   $cliente->natureza_contribuinte,
                   $destinacao, // destinação: Estadual ou Interestadual
                   $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
               );

               if (!$tributacao) {
                   $this->session->set_flashdata('error', 'Não foi encontrada tributação para a operação comercial selecionada na venda. Por favor, verifique se existe uma classificação fiscal cadastrada com os parâmetros: Operação Comercial ID: ' . $venda->operacao_comercial_id . ', Natureza Contribuinte: ' . $cliente->natureza_contribuinte . ', Destinação: ' . $destinacao . ', Objetivo Comercial: ' . $cliente->objetivo_comercial);
                   redirect(base_url('vendas'));
               }

               // Definir CFOP e CST/CSOSN baseado na tributação encontrada;
               if (strtolower($this->crt) === 'simples nacional') {
                   $p->csosn = $tributacao->csosn;
                   $p->cst = null;
               } else {
                   $p->cst = $tributacao->cst;
                   $p->csosn = null;
               }

               // Buscar tributação do produto
               if ($p->tributacao_produto_id) {
                   $tributacao_produto = $this->TributacaoProduto_model->getById($p->tributacao_produto_id);
                   if ($tributacao_produto) {
                       // Adicionar informações de impostos do produto
                       $p->cst_ipi = $tributacao_produto->cst_ipi_saida;
                       $p->aliq_ipi = $tributacao_produto->aliq_ipi_saida;
                       $p->cst_pis = $tributacao_produto->cst_pis_saida;
                       $p->aliq_pis = $tributacao_produto->aliq_pis_saida;
                       $p->cst_cofins = $tributacao_produto->cst_cofins_saida;
                       $p->aliq_cofins = $tributacao_produto->aliq_cofins_saida;
                       $p->regime_fiscal = $tributacao_produto->regime_fiscal_tributario;
                   }
               }

               // Calcula o valor do ICMS
               $valor_icms = 0;
               if ($this->crt == 3) { // Lucro Real
                   // Carrega o model de Alíquotas se ainda não foi carregado
                   $this->load->model('Aliquotas_model');
                   
              
                   $is_estadual = ($cliente->estado == $emitente->uf);
                   
                   
                   // Busca a alíquota correspondente
                   $aliquota = $this->Aliquotas_model->getAliquota($emitente->uf, $cliente->estado);
                   
                   if (!$aliquota) {
                       $this->session->set_flashdata('error', 'Alíquotas não configuradas no sistema para a operação entre ' . $emitente->uf . ' e ' . $cliente->estado);
                       redirect(base_url() . 'index.php/vendas');
                       return;
                   }
                   
                   // Define a alíquota baseado no tipo de operação
                   $aliq = $is_estadual ? $aliquota->aliquota_origem : $aliquota->aliquota_destino;
                   $valor_icms = ($p->preco * $p->quantidade * $aliq) / 100;
                    if(!$valor_icms) {
                        $this->session->set_flashdata('error', 'Alíquotas não configuradas' . $emitente->uf . ' e ' . $cliente->estado);
                        redirect(base_url() . 'index.php/vendas');
                        return;
                    }
                   // Se for operação interestadual e cliente não inscrito, calcula o DIFAL
                   if (!$is_estadual && $cliente->natureza_contribuinte === 'nao_inscrito') {
                       $vBCUFDest = $p->preco * $p->quantidade; // Base de cálculo
                       
                       // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                       if ($p->origem == 1 || $p->origem == 2 || $p->origem == 3 || $p->origem == 8) {
                           $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                           $pICMSInter = 4.00;
                       } else {
                           $pICMSInter = $aliq; // Alíquota interestadual normal
                       }
                       
                       // Busca a alíquota do estado de destino (estado do cliente)
                       $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                       if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                           $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                           redirect(base_url() . 'index.php/vendas');
                           return;
                       }
                       
                       $pICMSUFDest = $aliquota_destino->aliquota_origem; // Alíquota interna UF destino
                       
                       // Calcula o DIFAL - primeiro a diferença das alíquotas, depois aplica na base
                       $difAliquotas = $pICMSUFDest - $pICMSInter; // Exemplo: 19% - 12% = 7%
                       $difal = ($difAliquotas * $vBCUFDest) / 100; // Aplica a diferença na base
                       
                       // Adiciona a tag ICMSUFDest
                       $std = new \stdClass();
                       $std->item = $i;
                       $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                       $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                       $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                       $std->pICMSInterPart = 100.00;
                       $std->vICMSUFDest = number_format($difal, 2, '.', '');
                       $std->vICMSUFRemet = '0.00';
                       $nfe->tagICMSUFDest($std);

                       // Adiciona o DIFAL no obsCont e infAdic se for maior que 0
                       if ($difal > 0) {
                           $mensagemDIFAL = "DIFAL: R$ " . number_format($difal, 2, ',', '.') . 
                                          " (Diferença entre alíquotas: " . number_format($difAliquotas, 2, ',', '.') . "%)";

                           // Escapar o texto para garantir que o XML seja válido
                           $mensagemDIFAL = htmlspecialchars($mensagemDIFAL, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                           
                           // Armazena a mensagem DIFAL para ser usada depois no infCpl geral
                           if (!isset($this->mensagensDIFAL)) {
                               $this->mensagensDIFAL = [];
                           }
                           $this->mensagensDIFAL[] = $mensagemDIFAL;
                       }
                   } else if (!$is_estadual) {
                       // Se for operação interestadual mas cliente é inscrito, ainda precisa gerar ICMSUFDest
                       $vBCUFDest = $p->preco * $p->quantidade;
                       
                       // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                       if ($p->origem == 1 || $p->origem == 2 || $p->origem == 3 || $p->origem == 8) {
                           $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                           $pICMSInter = 4.00;
                       } else {
                           $pICMSInter = $aliq; // Alíquota interestadual normal
                       }
                       
                       $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                       if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                           $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                           redirect(base_url() . 'index.php/vendas');
                           return;
                       }
                       
                       $pICMSUFDest = $aliquota_destino->aliquota_origem;
                       
                       $std = new \stdClass();
                       $std->item = $i;
                       $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                       $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                       $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                       $std->pICMSInterPart = 100.00;
                       $std->vICMSUFDest = '0.00';
                       $std->vICMSUFRemet = '0.00';
                       $nfe->tagICMSUFDest($std);
                   }
               }
               
               $natureza_contribuinte = $this->input->post('natureza_contribuinte');
               $emitente = $this->Mapos_model->getEmitente();
               $cliente = $this->Clientes_model->getById($venda->clientes_id);
                  // Buscar tributação usando os parâmetros corretos
                  $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
                  $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                      $venda->operacao_comercial_id,
                      $cliente->natureza_contribuinte,
                      $destinacao, // destinação: Estadual ou Interestadual
                      $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
                  );
               // [prod]
               $std = new \stdClass();
               $std->item = $i;
               $std->cProd = $p->idProdutos;
               $std->cEAN = 'SEM GTIN';
               $std->cEANTrib = 'SEM GTIN'; // Adicionado o campo cEANTrib
               $std->xProd = $p->descricao;
               $std->NCM = $p->NCMs;
               $std->cfop = $tributacao->cfop;
               $std->uCom = 'UN';
               $std->qCom = $p->quantidade;
               $std->vUnCom = $p->preco;
               $std->vProd = $p->preco * $p->quantidade;
               // Campos obrigatórios adicionais
               $std->uTrib = 'UN'; // Unidade Tributável
               $std->qTrib = $p->quantidade; // Quantidade Tributável
               $std->vUnTrib = $p->preco; // Valor Unitário de tributação
               $std->indTot = 1; // Indica se valor do Item entra no valor total da NF-e
               $std->cEAN = 'SEM GTIN'; // Código de barras
               $std->cEANTrib = 'SEM GTIN'; // Código de barras da unidade tributável
               $nfe->tagprod($std);

             // [imposto]
             $std = new \stdClass();
             $std->item = $i;
             $nfe->tagimposto($std); // Sempre necessário abrir o bloco com tagimposto()
             
             // ICMS
             $std = new \stdClass();
             $std->item = $i;
             $std->orig = $p->origem;
             
             if ($this->crt == 1) {
                 // Simples Nacional
                 $std->CSOSN = isset($p->csosn) ? $p->csosn : '102';
                 
                 // Calcula o valor do crédito ICMS para qualquer CSOSN
                 $vBC = number_format($p->preco * $p->quantidade, 2, '.', '');
                 
                 // Busca a alíquota de crédito ICMS das configurações
                 $this->db->select('valor');
                 $this->db->from('configuracoes');
                 $this->db->where('config', 'aliq_cred_icms');
                 $this->db->limit(1);
                 $aliq_cred = $this->db->get()->row();
                 $pCredSN = $aliq_cred ? str_replace(',', '.', $aliq_cred->valor) : 3.00;
                 
                 $std->pCredSN = number_format($pCredSN, 2, '.', '');
                 $std->vCredICMSSN = number_format(($vBC * $pCredSN) / 100, 2, '.', '');
                 
                 $nfe->tagICMSSN($std);
             } else {
                 // Lucro Real ou Presumido
                 if (!isset($p->cst)) {
                    throw new Exception('CST não configurado na classificação fiscal');
                 }
                 $std->cst = isset($p->cst) ? $p->cst : '00';
                 $std->modBC = 3;
                 $std->vBC = $p->preco * $p->quantidade;
                 $std->pICMS = $aliq;   
                 $std->vICMS = ($std->vBC * $std->pICMS) / 100;
                 $nfe->tagICMS($std);
                 $valor_icms = $std->vICMS;
             }
             
             // PIS
             $std = new \stdClass();
             $std->item = $i;
             $std->cst = isset($p->cst_pis) ? $p->cst_pis : '01';
             $base_calculo = $p->preco * $p->quantidade;
             $std->vBC = number_format($base_calculo, 2, '.', '');
             $std->pPIS = isset($p->aliq_pis) ? $p->aliq_pis : 0;
             $std->vPIS = number_format(($base_calculo * $std->pPIS) / 100, 2, '.', '');
             $nfe->tagPIS($std);
             
             // COFINS
             $std = new \stdClass();
             $std->item = $i;
             $std->cst = isset($p->cst_cofins) ? $p->cst_cofins : '01';
             $std->vBC = number_format($base_calculo, 2, '.', '');
             $std->pCOFINS = isset($p->aliq_cofins) ? $p->aliq_cofins : 0;
             $std->vCOFINS = number_format(($base_calculo * $std->pCOFINS) / 100, 2, '.', '');
             $nfe->tagCOFINS($std);
             
             // Atualiza totais
             $totalProdutos += floatval($p->quantidade) * floatval($p->preco);
             if (!isset($valor_icms)) $valor_icms = 0;
             $totalICMS += $valor_icms;
             $i++;
           }

            // Adiciona todas as mensagens DIFAL no infCpl geral da nota
            if (!empty($this->mensagensDIFAL)) {
                $std = new \stdClass();
                $std->infAdFisco = '';
                $std->infCpl = "DADOS ADICIONAIS\n\n" . implode("\n", $this->mensagensDIFAL);
                $nfe->taginfAdic($std);
            } else {
                // Adiciona mensagem padrão sobre crédito ICMS para Simples Nacional apenas se for contribuinte
                if ($this->crt == 1 && isset($dest->indIEDest) && $dest->indIEDest == 1) {
                    // Busca a mensagem do Simples Nacional das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'mensagem_simples_nacional');
                    $this->db->limit(1);
                    $mensagem = $this->db->get()->row();
                    
                    $std = new \stdClass();
                    $std->infAdFisco = '';
                    
                    // Calcula o valor do crédito ICMS usando o total
                    $total = number_format($p->preco * $p->quantidade, 2, '.', '');
                    $vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                    
                    $mensagemTexto = $mensagem ? $mensagem->valor : "CRÉDITO ICMS SIMPLES NACIONAL\nConforme Art. 23 da LC 123/2006, o valor do crédito de ICMS corresponde a [percentual]% da base de cálculo do Simples Nacional.";
                    
                    // Substitui os placeholders na mensagem
                    $mensagemTexto = str_replace('[percentual]', number_format($pCredSN, 2, ',', '.'), $mensagemTexto);
                    $mensagemTexto = str_replace('[valor]', number_format($vCredICMSSN, 2, ',', '.'), $mensagemTexto);
                    
                    $std->infCpl = "DADOS ADICIONAIS\n\n" . $mensagemTexto;
                $nfe->taginfAdic($std);
                }
           }

            // [total]
            $std = new \stdClass();
            if ($this->crt == 1) {
            $std->vBC = number_format(0, 2, '.', '');
            $std->vICMS = number_format(0, 2, '.', '');
            } else {
            $std->vBC = number_format($totalProdutos, 2, '.', '');
            $std->vICMS = number_format($totalICMS, 2, '.', '');
            }
            $std->vICMSDeson = 0;
            $std->vBCST = 0;
            $std->vST = 0;
            $std->vProd = number_format($totalProdutos, 2, '.', '');
            $std->vFrete = 0;
            $std->vSeg = 0;
            $std->vDesc = 0;
            $std->vII = 0;
            $std->vIPI = 0;
            $std->vPIS = 0;
            $std->vCOFINS = 0;
            $std->vOutro = 0;
            $std->vNF = number_format($totalProdutos, 2, '.', '');
            $std->vTotTrib = 0;
            $nfe->tagICMSTot($std);

            // [transp]
            $std = new \stdClass();
            $std->modFrete = 9;
            
            // Add transportadora information if available
            if (isset($transportadora) && !empty($transportadora)) {
                $std->transporta = new \stdClass();
                $std->transporta->cnpj = preg_replace('/[^0-9]/', '', $transportadora->documento);
                $std->transporta->xNome = $transportadora->nomeCliente;
                $std->transporta->ie = !empty($transportadora->ie) ? $transportadora->ie : 'ISENTO';
                $std->transporta->xEnder = $transportadora->rua;
                $std->transporta->xMun = $transportadora->cidade;
                $std->transporta->uf = $transportadora->estado;
            }
            
            $nfe->tagtransp($std);

            // [pag]
            $std = new \stdClass();
            $std->indPag = 0; // 0 = pagamento à vista
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagpag($std);

            // [detPag]
            $std = new \stdClass();
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagdetPag($std);

            try {
                $xml = $nfe->getXML();
                
                // Obtém o objeto Tools com o certificado
                $tools = $this->getTools();

                // Verifica se há erros nas tags antes de prosseguir
                $errors = $nfe->getErrors();
                if (!empty($errors)) {
                    $errorMessage = "Erros encontrados no XML da NFC-e:\n";
                    foreach ($errors as $error) {
                        $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                    }
                    throw new Exception($errorMessage);
                }

                // Assina o XML
                $signed = $tools->signNFe($xml);

                // Envia para a SEFAZ
                $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
                $response = $tools->sefazEnviaLote([$signed], $idLote);
                $st = new \NFePHP\NFe\Common\Standardize($response);
                $std = $st->toStd();
                
                if ($std->cStat != 103) {
                    throw new Exception("Erro ao enviar NFe: " . $std->xMotivo);
                }

                $recibo = $std->infRec->nRec;
                
                // Aguarda o processamento do lote
                $tentativas = 0;
                $maxTentativas = 10;
                $status = 0;
                $chave_retorno_evento = '';
                
                while ($tentativas < $maxTentativas) {
                    // Consulta recibo
                    $protocolo = $tools->sefazConsultaRecibo($recibo);
                    
                    // Log da resposta bruta
                    log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                    
                    $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                    $std = $st->toStd();
                    
                    // Log do objeto padronizado
                    log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                    
                    // Verifica se o lote ainda está em processamento
                    if (isset($std->cStat) && $std->cStat == '105') {
                        // Lote em processamento, aguarda e tenta novamente
                        sleep(2);
                        $tentativas++;
                       continue;
                   }
                   
                    // Extrai o status real da NFe do protocolo
                    $dom = new \DOMDocument();
                    $dom->loadXML($protocolo);
                    
                    // Procura por diferentes tags possíveis
                    $infProt = $dom->getElementsByTagName('infProt')->item(0);
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                    }
                    
                    if (!$infProt) {
                        // Tenta extrair informações diretamente do objeto padronizado
                        if (isset($std->cStat)) {
                            $status = ($std->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsReciNFe->cStat)) {
                            $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsSitNFe->cStat)) {
                            $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                        } else {
                            throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                        }
                    } else {
                        $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                        $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                        
                        if ($cStat && $cStat->nodeValue == '100') {
                            $status = 1;
                            $chave_retorno_evento = "Autorizado o uso da NF-e";
                        } else {
                            $status = 0;
                            $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                        }
                    }
                    
                    // Se encontrou o status, sai do loop
                    if ($status != 0 || $chave_retorno_evento != '') {
                        break;
                    }
                    
                    // Aguarda antes da próxima tentativa
                    sleep(2);
                    $tentativas++;
                }
                
                if ($tentativas >= $maxTentativas) {
                    throw new Exception("Tempo limite excedido ao aguardar processamento do lote");
                }

                // Log da resposta bruta
                log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                
                $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                $std = $st->toStd();
                
                // Log do objeto padronizado
                log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                
                // Extrai o status real da NFe do protocolo
                $dom = new \DOMDocument();
                $dom->loadXML($protocolo);
                
                // Procura por diferentes tags possíveis
                $infProt = $dom->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                }
                
                if (!$infProt) {
                    // Tenta extrair informações diretamente do objeto padronizado
                    if (isset($std->cStat)) {
                        $status = ($std->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsReciNFe->cStat)) {
                        $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsSitNFe->cStat)) {
                        $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                    } else {
                        throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                    }
                } else {
                    $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                    $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                    
                    if ($cStat && $cStat->nodeValue == '100') {
                        $status = 1;
                        $chave_retorno_evento = "Autorizado o uso da NF-e";
                    } else {
                        $status = 0;
                        $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                    }
                }

                // Extrai o número da nota e chave do XML
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                if (!$infNFe) {
                    throw new Exception("Erro ao extrair informações da NFe: tag infNFe não encontrada");
                }
                
                $ide = $infNFe->getElementsByTagName('ide')->item(0);
                if (!$ide) {
                    throw new Exception("Erro ao extrair informações da NFe: tag ide não encontrada");
                }
                
                $nNF = $ide->getElementsByTagName('nNF')->item(0);
                if (!$nNF) {
                    throw new Exception("Erro ao extrair informações da NFe: tag nNF não encontrada");
                }
                $numero_nfe = $nNF->nodeValue;

                // Extrai a chave da NFe
                $chNFe = $infNFe->getAttribute('Id');
                if ($chNFe) {
                    $chNFe = str_replace('NFe', '', $chNFe); // Remove o prefixo 'NFe'
                }

                // Mostra a resposta da SEFAZ para análise
                log_message('debug', 'Resposta da SEFAZ: ' . $protocolo);

                // Atualiza NFe existente
                $nfeData = [
                    'venda_id' => (int)$nfe_original->venda_id,
                    'modelo' => 55, // Modelo NFe
                    'numero_nfe' => (string)$numero_nfe,
                    'chave_nfe' => (string)$chNFe,
                    'xml' => (string)$signed,
                    'xml_protocolo' => (string)$protocolo,
                    'status' => $status,
                    'chave_retorno_evento' => $chave_retorno_evento,
                    'protocolo' => '', // Será preenchido posteriormente
                    'valor_total' => $totalProdutos, // Adiciona o valor total dos itens devolvidos
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Atualiza o registro existente na tabela nfe_emitidas
                $this->db->where('id', $nfe_id);
                $this->db->update('nfe_emitidas', $nfeData);
                
                if ($this->db->affected_rows() == 0) {
                    throw new Exception('Erro ao atualizar NFe no banco de dados');
                }

                // Prepara dados para o modal
                $modalData = [
                    'status' => ($status == 1) ? 'Autorizada' : 'Rejeitada',
                    'motivo' => $chave_retorno_evento,
                    'protocolo' => $protocolo,
                    'xml' => $signed,
                    'chave_nfe' => $chNFe,
                    'numero_nfe' => $numero_nfe,
                    'modelo' => 55 // Modelo NFe
                ];
                
                $configNFe = $this->db->get('configuracoes_nfe')->row();
                if ($configNFe && isset($configNFe->preview_nfe) && $configNFe->preview_nfe) {
                    $nfe_emitida = $this->db->order_by('id', 'desc')->get('nfe_emitidas')->row();
                    if ($nfe_emitida) {
                        $this->session->set_flashdata('preview_nfe_id', $nfe_emitida->id);
                    }
                } else {
                    $this->session->set_flashdata('nfe_modal', $modalData);
                    $this->session->set_flashdata('success', 'Nota fiscal emitida com sucesso!');
                }
                redirect(base_url() . 'index.php/nfe/gerenciar');

            } catch (Exception $e) {
                $errors = $nfe->getErrors();
                $errorMessage = $e->getMessage();
                
                if (!empty($errors)) {
                    $errorMessage .= " - Erros nas tags: " . implode(", ", array_map(function($error) {
                        return str_replace('"', '', $error);
                    }, $errors));
                }
                
                // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
                $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
                $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
                $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
                redirect(base_url() . 'index.php/nfe');
            }

        } catch (Exception $e) {
            // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
            $errorMessage = $e->getMessage();
            $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
            $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
            $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
            redirect(base_url() . 'index.php/nfe');
        }
    }

    public function consultar($id = null) {
        try {
            if (!$id) {
                throw new Exception('ID da NFe não informado');
            }

            $nfe = $this->nfe->getNfe($id);
            if (!$nfe) {
                throw new Exception('NFe não encontrada');
            }

            // Obtém o objeto Tools com o certificado
            $tools = $this->getTools();

            $chave = $nfe->chave_nfe;
            $response = $tools->sefazConsultaChave($chave);
            
            // Processa a resposta da SEFAZ
            $st = new \NFePHP\NFe\Common\Standardize($response);
            $std = $st->toStd();
            
            // Verifica se a resposta contém erro
            if (isset($std->cStat) && $std->cStat != '100') {
                $status = 'Rejeitada';
                $motivo = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
            } else {
                // Tenta extrair informações do protocolo
                $dom = new \DOMDocument();
                $dom->loadXML($response);
                
                // Procura por diferentes tags possíveis
                $infProt = $dom->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                }
                
                if (!$infProt) {
                    throw new Exception("Não foi possível processar a resposta da SEFAZ");
                }
                
                $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                
                if ($cStat && $cStat->nodeValue == '100') {
                    $status = 'Autorizada';
                    $motivo = "Autorizado o uso da NF-e";
                    
                    // Se a NFe estava rejeitada e agora está autorizada, atualiza o status
                    if ($nfe->status == 0) {
                        $this->db->where('id', $id);
                        $this->db->update('nfe_emitidas', [
                            'status' => 1,
                            'chave_retorno_evento' => $motivo,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    $status = 'Rejeitada';
                    $motivo = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                }
            }

            // Verifica se o motivo contém "Cancelamento de NF-e homologado"
            if (strpos($motivo, 'Cancelamento de NF-e homologado') !== false) {
                $status = 'Cancelada';
                // Atualiza o status no banco de dados para cancelado (status 2)
                $this->db->where('id', $id);
                $this->db->update('nfe_emitidas', [
                    'status' => 2,
                    'chave_retorno_evento' => $motivo,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $modalData = [
                'numero_nfe' => $nfe->numero_nfe,
                'chave_nfe' => $nfe->chave_nfe,
                'status' => $status,
                'motivo' => $motivo,
                'protocolo' => $response
            ];

            $this->session->set_flashdata('nfe_modal', $modalData);
            $this->session->set_flashdata('is_consulta', true);
            redirect(base_url() . 'index.php/nfe/gerenciar');

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Erro ao consultar NFe: ' . $e->getMessage());
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }
    }

    public function imprimir($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFe.');
            redirect(base_url());
        }

        $nfe = $this->nfe->getNfe($id);

        if (!$nfe) {
            $this->session->set_flashdata('error', 'NFe não encontrada.');
            redirect(site_url('nfe/gerenciar'));
        }

        if ($nfe->status != 1) {
            $this->session->set_flashdata('error', 'NFe não autorizada.');
            redirect(site_url('nfe/gerenciar'));
        }

        // Verifica se é NFCe (modelo 65)
        if ($nfe->modelo == 65) {
            $this->imprimirNFCe($id);
            return;
        }

        try {
            $xml = $nfe->xml;
            if (empty($xml)) {
                throw new Exception('XML não encontrado para esta NFe.');
            }

            // Gera o DANFE
            $danfe = new Danfe($xml);
            $danfe->exibirTextoFatura = false;
            $danfe->exibirPIS = false;
            $danfe->exibirIcmsInterestadual = false;
            $danfe->exibirValorTributos = false;
            $danfe->descProdInfoComplemento = false;
            $danfe->exibirNumeroItemPedido = false;
            $danfe->setOcultarUnidadeTributavel(true);
            $danfe->obsContShow(false);
            $danfe->printParameters(
                $orientacao = 'P',
                $papel = 'A4',
                $margSup = 2,
                $margEsq = 2
            );
            
            // Configura o logo apenas se existir e for válido
            $configNFe = $this->db->get('configuracoes_nfe')->row();
            if ($configNFe && isset($configNFe->imprimir_logo_nfe) && $configNFe->imprimir_logo_nfe) {
                $emitente = $this->Mapos_model->getEmitente();
                $logoPath = '';
                if (!empty($emitente->url_logo)) {
                    // Se for URL absoluta
                    if (strpos($emitente->url_logo, 'http') === 0) {
                        $logoPath = str_replace(base_url(), FCPATH, $emitente->url_logo);
                    } else {
                        $logoPath = FCPATH . ltrim($emitente->url_logo, '/');
                    }
                }
                // Se não houver logo personalizada, usa a padrão
                if (empty($logoPath) || !file_exists($logoPath) || !is_readable($logoPath)) {
                    $logoPath = FCPATH . 'assets/img/logo.png';
                }
                if (file_exists($logoPath) && is_readable($logoPath)) {
                    try {
                        $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents($logoPath));
                        $danfe->logoParameters($logo, $logoAlign = 'C', $mode_bw = false);
                    } catch (Exception $e) {
                        log_message('error', 'Erro ao processar logo: ' . $e->getMessage());
                    }
                }
            }
            
            $danfe->setDefaultFont($font = 'times');
            $danfe->setDefaultDecimalPlaces(4);
            $danfe->debugMode(false);
            $danfe->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webenf.com.br');

            // Gera o PDF
            $pdf = $danfe->render();
            
            // Força o download do PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename=DANFE_' . $nfe->chave_nfe . '.pdf');
            echo $pdf;
            exit;

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Erro ao gerar DANFE: ' . $e->getMessage());
            redirect(site_url('nfe/gerenciar'));
        }
    }

    public function imprimirNFCe($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFC-e.');
            redirect(base_url());
        }

        $this->load->model('Clientes_model');
        $this->load->model('Mapos_model');
        $this->load->model('Vendas_model');

        $nfe = $this->nfe->getNfe($id);
        if (!$nfe) {
            $this->session->set_flashdata('error', 'NFC-e não encontrada.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Obtém os dados da venda
        $venda = $this->Vendas_model->getById($nfe->venda_id);
        if (!$venda) {
            $this->session->set_flashdata('error', 'Venda não encontrada.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Obtém os dados do cliente
        $cliente = $this->Clientes_model->getById($venda->clientes_id);
        if (!$cliente) {
            $this->session->set_flashdata('error', 'Cliente não encontrado.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Obtém os dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            $this->session->set_flashdata('error', 'Dados do emitente não encontrados.');
            redirect(base_url() . 'index.php/nfe');
        }

        try {
            // Carrega o XML
            $dom = new DOMDocument();
            $dom->loadXML($nfe->xml);

            // Extrai informações do XML
            $emitente = $this->Mapos_model->getEmitente();
            $cliente = $this->Clientes_model->getById($venda->clientes_id);
            // Carrega as configurações da NFCe
            $this->load->model('Nfe_model');
            $config = $this->db->get('configuracoes_nfce')->row();
            if (!$config) {
                throw new Exception('Configurações da NFCe não encontradas.');
            }
            
            // Carrega a biblioteca QRCode
            $this->load->library('QRCodeGenerator');
            
            // Extrai a URL do QR Code do XML
            $qrcode_url = $this->extrairQRCodeURL($dom);
            if (empty($qrcode_url)) {
                // Se não encontrar no XML, gera uma nova
                $total = $this->extrairTotalXML($dom);
                $qrcode_url = $this->gerarQRCode($emitente, $config, $total['total']);
            }
            
            // Prepara os dados para a view
            $data = [
                'nfe' => $nfe,
                'emitente' => $emitente,
                'produtos' => $this->extrairProdutosXML($dom),
                'pagamentos' => $this->extrairPagamentosXML($dom),
                'total' => $this->extrairTotalXML($dom),
                'qrcode_url' => $this->qrcodegenerator->generate($qrcode_url)
            ];

            // Carrega a view do DANFE NFCe
            $this->load->view('nfe/danfe_nfce', $data);

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Erro ao gerar DANFE NFCe: ' . $e->getMessage());
            redirect(site_url('nfe/gerenciar'));
        }
    }

    private function extrairProdutosXML($dom) {
        $produtos = [];
        $items = $dom->getElementsByTagName('det');
        
        foreach ($items as $item) {
            $prod = $item->getElementsByTagName('prod')->item(0);
            $produtos[] = [
                'codigo' => $prod->getElementsByTagName('cProd')->item(0)->nodeValue,
                'descricao' => $prod->getElementsByTagName('xProd')->item(0)->nodeValue,
                'quantidade' => $prod->getElementsByTagName('qCom')->item(0)->nodeValue,
                'valorUnitario' => $prod->getElementsByTagName('vUnCom')->item(0)->nodeValue,
                'valorTotal' => $prod->getElementsByTagName('vProd')->item(0)->nodeValue
            ];
        }
        
        return $produtos;
    }

    private function extrairPagamentosXML($dom) {
        $pagamentos = [];
        $pag = $dom->getElementsByTagName('pag')->item(0);
        
        if ($pag) {
            $detPag = $pag->getElementsByTagName('detPag');
            foreach ($detPag as $det) {
                $tPag = $det->getElementsByTagName('tPag')->item(0)->nodeValue;
                $vPag = $det->getElementsByTagName('vPag')->item(0)->nodeValue;
                
                $descricao = $this->getDescricaoPagamento($tPag);
                
                $pagamentos[] = [
                    'tipo' => $descricao,
                    'valor' => $vPag
                ];
            }
        }
        
        return $pagamentos;
    }

    private function getDescricaoPagamento($tPag) {
        $tipos = [
            '01' => 'Dinheiro',
            '02' => 'Cheque',
            '03' => 'Cartão de Crédito',
            '04' => 'Cartão de Débito',
            '05' => 'Crédito Loja',
            '10' => 'Vale Alimentação',
            '11' => 'Vale Refeição',
            '12' => 'Vale Presente',
            '13' => 'Vale Combustível',
            '15' => 'Boleto Bancário',
            '90' => 'Sem Pagamento',
            '99' => 'Outros'
        ];
        
        return isset($tipos[$tPag]) ? $tipos[$tPag] : 'Outros';
    }

    private function extrairTotalXML($dom) {
        $total = $dom->getElementsByTagName('ICMSTot')->item(0);
        return [
            'produtos' => $total->getElementsByTagName('vProd')->item(0)->nodeValue,
            'desconto' => $total->getElementsByTagName('vDesc')->item(0)->nodeValue,
            'total' => $total->getElementsByTagName('vNF')->item(0)->nodeValue
        ];
    }

    private function extrairQRCodeURL($dom) {
        try {
            $qrcode = $dom->getElementsByTagName('qrCode')->item(0);
            return $qrcode ? $qrcode->nodeValue : '';
        } catch (Exception $e) {
            log_message('error', 'Erro ao extrair QR Code do XML: ' . $e->getMessage());
            return '';
        }
    }

    public function visualizarCancelamento($nfe_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFe.');
            redirect(base_url());
        }

        // Busca a NFe
        $nfe = $this->nfe->getNfe($nfe_id);
        if (!$nfe) {
            $this->session->set_flashdata('error', 'NFe não encontrada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Verifica se a NFe está cancelada
        if ($nfe->status != 2) {
            $this->session->set_flashdata('error', 'Esta NFe não está cancelada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Busca o documento de cancelamento
        $this->db->where('nfe_id', $nfe_id);
        $this->db->where('tipo', 'cancelamento');
        $documento = $this->db->get('nfe_documentos')->row();

        if (!$documento) {
            $this->session->set_flashdata('error', 'Documento de cancelamento não encontrado.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();

        // Passa os dados para a view
        $this->data['nfe'] = $nfe;
        $this->data['documento'] = $documento;
        $this->data['emitente'] = $emitente;

        // Carrega a view
        $this->load->view('nfe/cancelamento_preview', $this->data);
    }

    public function imprimirCancelamento($nfe_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NFe.');
            redirect(base_url());
        }

        // Busca a NFe
        $nfe = $this->nfe->getNfe($nfe_id);
        if (!$nfe) {
            $this->session->set_flashdata('error', 'NFe não encontrada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Verifica se a NFe está cancelada
        if ($nfe->status != 2) {
            $this->session->set_flashdata('error', 'Apenas NFe\'s canceladas podem ter o comprovante impresso.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Busca o documento de cancelamento
        $this->db->where('nfe_id', $nfe_id);
        $this->db->where('tipo', 'cancelamento');
        $documento = $this->db->get('nfe_documentos')->row();

        if (!$documento) {
            $this->session->set_flashdata('error', 'Documento de cancelamento não encontrado.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Carrega a view do comprovante
        $this->data['nfe'] = $nfe;
        $this->data['documento'] = $documento;
        $this->data['emitente'] = $this->data['emitente'];
        
        $this->load->view('nfe/cancelamento_preview', $this->data);
    }

    private function calculaDV($chave)
    {
        $soma = 0;
        $peso = 2;
        for ($i = strlen($chave) - 1; $i >= 0; $i--) {
            $soma += $chave[$i] * $peso;
            $peso++;
            if ($peso > 9) {
                $peso = 2;
            }
        }
        $resto = $soma % 11;
        if ($resto == 0 || $resto == 1) {
            return 0;
        }
        return 11 - $resto;
    }

    public function emitirNFCe($venda_id = null)
    {
        try {
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
                throw new Exception('Você não tem permissão para emitir NFC-e.');
            }

            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
                throw new Exception('Você não tem permissão para emitir NFC-e.');
            }

            // Carrega os dados da venda
            $venda = $this->Vendas_model->getById($venda_id);
            if (!$venda) {
                throw new Exception('Venda não encontrada.');
            }

            // Carrega os dados do cliente
            $cliente = $this->Vendas_model->getCliente($venda_id);
            if (!$cliente) {
                throw new Exception('Cliente não encontrado para esta venda.');
            }

            // Carrega os dados do emitente
            $emitente = $this->Mapos_model->getEmitente();
            if (!$emitente) {
                throw new Exception('Dados do emitente não encontrados.');
            }

            // Carrega os produtos da venda
            $produtos = $this->Vendas_model->getProdutos($venda_id);
            if (empty($produtos)) {
                throw new Exception('Nenhum produto encontrado para esta venda.');
            }

            // Carrega as configurações do NFC-e
            $this->db->where('id', 1);
            $config = $this->db->get('configuracoes_nfce')->row();
            if (!$config) {
                throw new Exception('Configurações do NFC-e não encontradas.');
            }

            // Obtém o certificado
            $certificate = $this->nfe->getCertificate();
            if (!$certificate) {
                throw new Exception('Certificado digital não encontrado');
            }

            // Verifica se o certificado está vencido
            $dataValidade = new DateTime($certificate->data_validade);
            $hoje = new DateTime();
            if ($hoje > $dataValidade) {
                throw new Exception('O certificado digital está vencido. Por favor, atualize o certificado nas configurações do sistema.');
            }

            // Tenta ler o certificado
            try {
                // Lê o certificado diretamente do conteúdo binário
                $cert = \NFePHP\Common\Certificate::readPfx($certificate->certificado_digital, $certificate->senha_certificado);
            } catch (Exception $e) {
                throw new Exception('Erro ao ler o certificado digital: ' . $e->getMessage());
            }

            // Prepara a configuração em JSON
            $configJson = json_encode([
                "atualizacao" => date('Y-m-d H:i:s'),
                "tpAmb" => (int)$config->ambiente, // 1 = Produção, 2 = Homologação
                "razaosocial" => $emitente->nome,
                "siglaUF" => $emitente->uf,
                "cnpj" => preg_replace('/[^0-9]/', '', $emitente->cnpj),
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
                "tokenIBPT" => "",
                "csc" => $config->csc ?? "",
                "CSCid" => $config->csc_id ?? "",
                "aProxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                ]
            ]);

            // Cria o objeto Tools com a configuração e o certificado
            $tools = new \NFePHP\NFe\Tools($configJson, $cert);

            // Define o modelo como NFCe (65)
            $tools->model(65);

            // Cria objeto NFe
            $nfe = new Make();

            $this->load->model('Mapos_model');
            $this->load->model('Vendas_model');
            $this->load->model('Clientes_model');
            $this->load->model('Produtos_model');
            $this->load->model('ClassificacaoFiscal_model');
            $this->load->model('OperacaoComercial_model');
            $this->load->model('TributacaoProduto_model');
            $operacao_comercial_id = $this->input->post('operacao_comercial_id');
            $natureza_contribuinte = $this->input->post('natureza_contribuinte');
            $destinacao = $this->input->post('destinacao');
            $objetivo_comercial = $this->input->post('objetivo_comercial');
            $emitente = $this->Mapos_model->getEmitente();
            $tributacao = $this->ClassificacaoFiscal_model->getTributacao($operacao_comercial_id, $natureza_contribuinte, $destinacao, $objetivo_comercial);
    
            $operacao = $this->OperacaoComercial_model->getById($venda->operacao_comercial_id);

            // [infNFe]
            $std = new \stdClass();
            $std->versao = '4.00';
            $nfe->taginfNFe($std);

            // [ide]
            $std = new \stdClass();
            $std->cUF = $this->get_cUF($emitente->uf);
            $std->cNF = rand(10000000, 99999999);
            $std->natOp = $operacao->nome_operacao;
            $std->mod = 65; // Modelo NFC-e
            $std->serie = 1;
            $std->nNF = $config->sequencia_nfce;
            $std->dhEmi = date('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
            $std->tpNF = 1;
            $std->idDest = 1;
            $std->cMunFG = $emitente->ibge;
            $std->tpImp = 4; // NFC-e
            $std->tpEmis = 1;
            $std->cDV = 0;
            $std->tpAmb = $config->ambiente;
            $std->finNFe = 1;
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = '4.00';
            $nfe->tagide($std);

            $this->db->select('valor');
            $this->db->from('configuracoes');
            $this->db->where('config', 'regime_tributario');
            $this->db->limit(1);
            $regime = $this->db->get()->row();
            $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;

            // [emit]
            $std = new \stdClass();
            $std->xNome = $emitente->nome;
            $std->xFant = $emitente->nome;
            $std->ie = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
            $std->CRT = $this->crt;
            $std->cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $nfe->tagemit($std);

            // [enderEmit]
            $std = new \stdClass();
            $std->xLgr = $emitente->rua;
            $std->nro = $emitente->numero;
            if (!empty($emitente->complemento)) {
                $std->xCpl = $emitente->complemento;
            }
            $std->xBairro = $emitente->bairro;
            $std->cMun = $emitente->ibge;
            $std->xMun = $emitente->cidade;
            $std->uf = $emitente->uf;
            $std->cep = preg_replace('/[^0-9]/', '', $emitente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
            $nfe->tagenderEmit($std);

            // [dest]
            $std = new \stdClass();
            $std->xNome = $cliente->nomeCliente;
            if (strlen(preg_replace('/[^0-9]/', '', $cliente->documento)) == 11) {
                $std->cpf = preg_replace('/[^0-9]/', '', $cliente->documento);
                $std->indIEDest = 9; // Contribuinte Isento
            } else {
                    $std->cnpj = preg_replace('/[^0-9]/', '', $cliente->documento);
                if (!empty($cliente->inscricao_estadual)) {
                    $std->ie = $cliente->inscricao_estadual;
                    $std->indIEDest = 1; // Contribuinte
                } else {
                    $std->indIEDest = 9; // Contribuinte Isento
                }
            }
            $nfe->tagdest($std);

            // [enderDest]
            $std = new \stdClass();
            $std->xLgr = $cliente->rua;
            $std->nro = $cliente->numero;
            if (!empty($cliente->complemento)) {
            $std->xCpl = $cliente->complemento;
            }
            $std->xBairro = $cliente->bairro;
            
            // Garante que o código do município (IBGE) está presente
            if (empty($cliente->ibge)) {
                throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
            }
            
            $std->cMun = $cliente->ibge;
            $std->xMun = $cliente->cidade;
            $std->uf = $cliente->estado;
            $std->cep = preg_replace('/[^0-9]/', '', $cliente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            if (!empty($cliente->telefone)) {
            $std->fone = preg_replace('/[^0-9]/', '', $cliente->telefone);
            }
            $nfe->tagenderDest($std);

            // Adiciona infCpl (informação complementar) conforme solicitado
            $std = new \stdClass();
            $std->infAdFisco = '';
            $std->infCpl = 'Devolução de Venda referente a nota ' . $nfe_original->numero_nfe . ' com chave: ' . $nfe_original->chave_nfe;
            $nfe->taginfAdic($std);

            // Produtos
            $i = 1; // Inicializa o contador em 1
            $totalProdutos = 0;
            $totalICMS = 0;
            foreach ($produtos as $p) {
                
               $emitente = $this->Mapos_model->getEmitente();
               $cliente = $this->Clientes_model->getById($venda->clientes_id);
               // Buscar tributação usando os parâmetros corretos
               $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
               $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                   $venda->operacao_comercial_id,
                   $cliente->natureza_contribuinte,
                    $destinacao, // destinação: Estadual ou Interestadual
                    $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
               );

               // Buscar tributação do produto
               if ($p->tributacao_produto_id) {
                   $tributacao_produto = $this->TributacaoProduto_model->getById($p->tributacao_produto_id);
                   if ($tributacao_produto) {
                       // Adicionar informações de impostos do produto
                       $p->cst_ipi = $tributacao_produto->cst_ipi_saida;
                       $p->aliq_ipi = $tributacao_produto->aliq_ipi_saida;
                       $p->cst_pis = $tributacao_produto->cst_pis_saida;
                       $p->aliq_pis = $tributacao_produto->aliq_pis_saida;
                       $p->cst_cofins = $tributacao_produto->cst_cofins_saida;
                       $p->aliq_cofins = $tributacao_produto->aliq_cofins_saida;
                       $p->regime_fiscal = $tributacao_produto->regime_fiscal_tributario;
                   }
               }
               // [prod]
               $std = new \stdClass();
                $std->item = $i; // Usa o contador que começa em 1
               $std->cProd = $p->idProdutos;
               $std->cEAN = 'SEM GTIN';
                $std->cEANTrib = 'SEM GTIN'; // Adicionado o campo cEANTrib
               $std->xProd = $p->descricao;
               $std->NCM = $p->NCMs;
               $std->cfop = $tributacao->cfop;
               $std->uCom = $p->unidade;
               $std->qCom = $p->quantidade;
               $std->vUnCom = $p->preco;
               $std->vProd = number_format($p->quantidade * $p->preco, 2, '.', '');
               $std->uTrib = $p->unidade;
               $std->qTrib = $p->quantidade;
               $std->vUnTrib = $p->preco;
               $std->indTot = 1;
               $nfe->tagprod($std);

               // [imposto]
                $std = new \stdClass();
             $std->item = $i; // Usa o mesmo contador
             $nfe->tagimposto($std); // Sempre necessário abrir o bloco com tagimposto()
             
             // ICMS
            $std = new \stdClass();
             $std->item = $i; // Usa o mesmo contador
             $std->orig = isset($p->origem) ? $p->origem : 0;
             
             if ($this->crt == 1) {
                 // Simples Nacional
                 $std->CSOSN = isset($p->csosn) ? $p->csosn : '102';
                 
                 // Calcula o valor do crédito ICMS para qualquer CSOSN usando o valor total
                 $total = number_format($p->preco * $p->quantidade, 2, '.', '');
                 
                 // Busca a alíquota de crédito ICMS das configurações
                 $this->db->select('valor');
                 $this->db->from('configuracoes');
                 $this->db->where('config', 'aliq_cred_icms');
                 $this->db->limit(1);
                 $aliq_cred = $this->db->get()->row();
                 $pCredSN = $aliq_cred ? str_replace(',', '.', $aliq_cred->valor) : 3.00;
                 
                 $std->pCredSN = number_format($pCredSN, 2, '.', '');
                 $std->vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                 
                 $nfe->tagICMSSN($std);
             } else {
                 // Lucro Real ou Presumido
                 if (!isset($tributacao->cst)) {
                     throw new Exception('CST não configurado na classificação fiscal');
                 }
                 $std->cst = $tributacao->cst;
                 $std->modBC = 3;
                 $std->vBC = $p->preco * $p->quantidade;
                 
                 // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                 if ($p->origem == 1 || $p->origem == 2 || $p->origem == 3 || $p->origem == 8) {
                     $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                 } else {
                     // Carrega o model de Alíquotas se ainda não foi carregado
                     $this->load->model('Aliquotas_model');
                     
                     // Busca a alíquota correspondente
                     $aliquota = $this->Aliquotas_model->getAliquota($emitente->uf, $cliente->estado);
                     
                     if (!$aliquota) {
                         $this->session->set_flashdata('error', 'Alíquotas não configuradas no sistema para a operação entre ' . $emitente->uf . ' e ' . $cliente->estado);
                         redirect(base_url() . 'index.php/vendas');
                         return;
                     }
                     
                     // Define a alíquota baseado no tipo de operação
                     $is_estadual = ($cliente->estado == $emitente->uf);
                     $aliq = $is_estadual ? $aliquota->aliquota_origem : $aliquota->aliquota_destino;
                 }
                 
                 $std->pICMS = $aliq;
                 $std->vICMS = ($std->vBC * $std->pICMS) / 100;
                 $nfe->tagICMS($std);
                 $valor_icms = $std->vICMS;
             }
             
            // PIS
               $std = new \stdClass();
               $std->item = $i;
            $std->cst = isset($p->cst_pis) ? $p->cst_pis : '01';
            $base_calculo = $p->preco * $p->quantidade;
            $std->vBC = number_format($base_calculo, 2, '.', '');
            $std->pPIS = isset($p->aliq_pis) ? $p->aliq_pis : 0;
            $std->vPIS = number_format(($base_calculo * $std->pPIS) / 100, 2, '.', '');
            $nfe->tagPIS($std);
            
            // COFINS
            $std = new \stdClass();
            $std->item = $i;
            $std->cst = isset($p->cst_cofins) ? $p->cst_cofins : '01';
            $std->vBC = number_format($base_calculo, 2, '.', '');
            $std->pCOFINS = isset($p->aliq_cofins) ? $p->aliq_cofins : 0;
            $std->vCOFINS = number_format(($base_calculo * $std->pCOFINS) / 100, 2, '.', '');
            $nfe->tagCOFINS($std);
            
            // Atualiza totais
            $totalProdutos += floatval($p->quantidade) * floatval($p->preco);
            if (!isset($valor_icms)) $valor_icms = 0;
            $totalICMS += $valor_icms;
            $i++;
           }

            // Adiciona todas as mensagens DIFAL no infCpl geral da nota
            if (!empty($this->mensagensDIFAL)) {
            $std = new \stdClass();
                $std->infAdFisco = '';
                $std->infCpl = "DADOS ADICIONAIS\n\n" . implode("\n", $this->mensagensDIFAL);
            $nfe->taginfAdic($std);
            } else {
                // Adiciona mensagem padrão sobre crédito ICMS para Simples Nacional apenas se for contribuinte
                if ($this->crt == 1 && isset($dest->indIEDest) && $dest->indIEDest == 1) {
                    // Busca a mensagem do Simples Nacional das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'mensagem_simples_nacional');
                    $this->db->limit(1);
                    $mensagem = $this->db->get()->row();
                    
                    $std = new \stdClass();
                    $std->infAdFisco = '';
                    
                    // Calcula o valor do crédito ICMS usando o total
                    $total = number_format($p->preco * $p->quantidade, 2, '.', '');
                    $vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                    
                    $mensagemTexto = $mensagem ? $mensagem->valor : "CRÉDITO ICMS SIMPLES NACIONAL\nConforme Art. 23 da LC 123/2006, o valor do crédito de ICMS corresponde a [percentual]% da base de cálculo do Simples Nacional.";
                    
                    // Substitui os placeholders na mensagem
                    $mensagemTexto = str_replace('[percentual]', number_format($pCredSN, 2, ',', '.'), $mensagemTexto);
                    $mensagemTexto = str_replace('[valor]', number_format($vCredICMSSN, 2, ',', '.'), $mensagemTexto);
                    
                    $std->infCpl = "DADOS ADICIONAIS\n\n" . $mensagemTexto;
                $nfe->taginfAdic($std);
                }
           }

            // [total]
            $std = new \stdClass();
            $std->vBC = ($this->crt == 1) ? 0 : $totalProdutos; // Base zero para CRT 1
            $std->vICMS = ($this->crt == 1) ? 0 : $totalICMS; // ICMS zero para CRT 1
            $std->vICMSDeson = 0;
            $std->vBCST = 0;
            $std->vST = 0;
            $std->vProd = number_format($totalProdutos, 2, '.', '');
            $std->vFrete = 0;
            $std->vSeg = 0;
            $std->vDesc = 0;
            $std->vII = 0;
            $std->vIPI = 0;
            $std->vPIS = 0;
            $std->vCOFINS = 0;
            $std->vOutro = 0;
            $std->vNF = number_format($totalProdutos, 2, '.', '');
               $std->vTotTrib = 0;
            $nfe->tagICMSTot($std);

            // [transp]
            $std = new \stdClass();
            $std->modFrete = 9;
            
            // Add transportadora information if available
            if (isset($transportadora) && !empty($transportadora)) {
                $std->transporta = new \stdClass();
                $std->transporta->cnpj = preg_replace('/[^0-9]/', '', $transportadora->documento);
                $std->transporta->xNome = $transportadora->nomeCliente;
                $std->transporta->ie = !empty($transportadora->ie) ? $transportadora->ie : 'ISENTO';
                $std->transporta->xEnder = $transportadora->rua;
                $std->transporta->xMun = $transportadora->cidade;
                $std->transporta->uf = $transportadora->estado;
            }
            
            $nfe->tagtransp($std);

            // [pag]
            $std = new \stdClass();
            $std->indPag = 0; // 0 = pagamento à vista
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagpag($std);

            // [detPag]
            $std = new \stdClass();
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagdetPag($std);

            // [infNFeSupl] - Informações suplementares para NFC-e
            $std = new \stdClass();
            $std->qrCode = $this->gerarQRCode($emitente, $config, $totalProdutos);
            $std->urlChave = $this->getUrlConsulta($emitente->uf, $config->ambiente);
            $nfe->taginfNFeSupl($std);

            // Verifica se há erros nas tags antes de gerar o XML
            $errors = $nfe->getErrors();
            if (!empty($errors)) {
                $errorMessage = "Erros encontrados no XML da NFC-e:\n";
                foreach ($errors as $error) {
                    $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                }
                log_message('error', 'Erros na NFC-e: ' . $errorMessage);
                throw new Exception($errorMessage);
            }

            // Gera o XML
            $xml = $nfe->getXML();

            // Verifica novamente após gerar o XML
            $errors = $nfe->getErrors();
            if (!empty($errors)) {
                $errorMessage = "Erros encontrados no XML da NFC-e após geração:\n";
                foreach ($errors as $error) {
                    $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                }
                log_message('error', 'Erros na NFC-e após geração: ' . $errorMessage);
                throw new Exception($errorMessage);
            }

            // Assina o XML
            $signed = $tools->signNFe($xml);

            // Obtém a URL do webservice
            $url = $this->getWebserviceUrl($config->ambiente, $emitente->uf);
            if (!$url) {
                throw new Exception('URL do webservice não encontrada para a UF ' . $emitente->uf);
            }

            // Obtém a URL específica para autorização
            $urlAutorizacao = $url['NfeAutorizacao'];

            // Gera o ID do lote
            $idLote = str_pad(1, 15, '0', STR_PAD_LEFT);

            // Envia a NFC-e para autorização (modo síncrono para uma única NFC-e)
            $response = $tools->sefazEnviaLote([$signed], $idLote, 1); // 1 = modo síncrono

            // Processa a resposta do envio
            $dom = new \DOMDocument();
            $dom->loadXML($response);
            
            // Verifica o status do envio
            $cStat = $dom->getElementsByTagName('cStat')->item(0);
            $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0);
            
            if (!$cStat) {
                throw new Exception('Status não encontrado na resposta da SEFAZ');
            }

            // Se o lote foi processado (104), precisa verificar o protocolo
            if ($cStat->nodeValue == '104') {
                // Procura pelo protocolo
                $protNFe = $dom->getElementsByTagName('protNFe')->item(0);
                if (!$protNFe) {
                    throw new Exception('Protocolo não encontrado na resposta da SEFAZ');
                }

                $infProt = $protNFe->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    throw new Exception('Informações do protocolo não encontradas');
                }

                // Verifica o status do protocolo
                $cStatProt = $infProt->getElementsByTagName('cStat')->item(0);
                $xMotivoProt = $infProt->getElementsByTagName('xMotivo')->item(0);
                $nProt = $infProt->getElementsByTagName('nProt')->item(0);

                if (!$cStatProt) {
                    throw new Exception('Status do protocolo não encontrado');
                }

                // Extrai o número da nota e chave do XML
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                if (!$infNFe) {
                    throw new Exception("Erro ao extrair informações da NFC-e: tag infNFe não encontrada");
                }
                
                $ide = $infNFe->getElementsByTagName('ide')->item(0);
                if (!$ide) {
                    throw new Exception("Erro ao extrair informações da NFC-e: tag ide não encontrada");
                }
                
                $nNF = $ide->getElementsByTagName('nNF')->item(0);
                if (!$nNF) {
                    throw new Exception("Erro ao extrair informações da NFC-e: tag nNF não encontrada");
                }
                $numero_nfe = $nNF->nodeValue;

                // Extrai a chave da NFC-e
                $chNFe = $infNFe->getAttribute('Id');
                if ($chNFe) {
                    $chNFe = str_replace('NFe', '', $chNFe); // Remove o prefixo 'NFe'
                }

                // Salva NFC-e emitida independente do status
                $nfeData = [
                    'venda_id' => (int)$nfe_original->venda_id,
                    'modelo' => 65, // Modelo NFC-e
                    'numero_nfe' => (string)$numero_nfe,
                    'chave_nfe' => (string)$chNFe,
                    'xml' => (string)$xml,
                    'xml_protocolo' => (string)$response,
                    'status' => ($cStatProt->nodeValue == '100') ? 1 : 0,
                    'chave_retorno_evento' => $xMotivoProt ? $xMotivoProt->nodeValue : 'Motivo não informado',
                    'protocolo' => $nProt ? $nProt->nodeValue : '',
                    'valor_total' => $totalProdutos, // Adiciona o valor total dos itens devolvidos
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Insere na tabela nfe_emitidas
                $this->db->insert('nfe_emitidas', $nfeData);
                
                if ($this->db->affected_rows() == 0) {
                    throw new Exception('Erro ao salvar NFC-e no banco de dados');
                }

                // Se a NFC-e foi autorizada, atualiza o campo emitida_nfe na tabela vendas
                if ($cStatProt->nodeValue == '100') {
                    $this->db->where('idVendas', $nfe_original->venda_id);
                    $this->db->update('vendas', ['emitida_nfe' => true]);
                }

                // Atualiza a sequência da nota
                $this->db->where('id', 1);
                $this->db->update('configuracoes_nfce', [
                    'sequencia_nfce' => $config->sequencia_nfce + 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Prepara dados para o modal
                $modalData = [
                    'status' => ($cStatProt->nodeValue == '100') ? 'Autorizada' : 'Rejeitada',
                    'motivo' => $xMotivoProt ? $xMotivoProt->nodeValue : 'Motivo não informado',
                    'protocolo' => $response,
                    'xml' => $xml,
                    'chave_nfe' => $chNFe,
                    'numero_nfe' => $numero_nfe,
                    'modelo' => 65 // Modelo NFC-e
                ];
                
                $this->session->set_flashdata('nfe_modal', $modalData);
                
                if ($cStatProt->nodeValue == '100') {
                    $this->session->set_flashdata('success', 'NFC-e emitida com sucesso!');
                    redirect(base_url() . 'index.php/nfe/gerenciar');
                } else {
                    $this->session->set_flashdata('error', 'NFC-e rejeitada: ' . $xMotivoProt->nodeValue);
                }
                
                redirect(base_url() . 'index.php/nfe');
            }
            // Se não foi processado, lança exceção com o motivo
            else {
                $motivo = $xMotivo ? $xMotivo->nodeValue : 'Erro desconhecido';
                
                // Prepara dados para o modal mesmo em caso de erro
                $modalData = [
                    'status' => 'Rejeitada',
                    'motivo' => $motivo,
                    'protocolo' => $response,
                    'xml' => $xml,
                    'chave_nfe' => '',
                    'numero_nfe' => ''
                ];
                
                $this->session->set_flashdata('nfe_modal', $modalData);
                $this->session->set_flashdata('error', 'Erro ao emitir NFC-e: ' . $motivo);
                redirect(base_url() . 'index.php/nfe/gerenciar');
            }

        } catch (Exception $e) {
            log_message('error', 'Erro ao enviar NFC-e: ' . $e->getMessage());
            
            // Prepara dados para o modal em caso de exceção
            $modalData = [
                'status' => 'Rejeitada',
                'motivo' => $e->getMessage(),
                'protocolo' => '',
                'xml' => isset($xml) ? $xml : '',
                'chave_nfe' => '',
                'numero_nfe' => ''
            ];
            
            $this->session->set_flashdata('nfe_modal', $modalData);
            $this->session->set_flashdata('error', $e->getMessage());
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }
    }

    private function getWebserviceUrl($ambiente, $uf)
    {
        // Retorna a URL do webservice baseado no ambiente e UF
        $urls = [
            'GO' => [
                '1' => [
                    'NfeAutorizacao' => 'https://nfe.sefaz.go.gov.br/nfe/services/NFeAutorizacao4',
                    'NfeRetAutorizacao' => 'https://nfe.sefaz.go.gov.br/nfe/services/NFeRetAutorizacao4',
                    'NfeInutilizacao' => 'https://nfe.sefaz.go.gov.br/nfe/services/NFeInutilizacao4',
                    'NfeConsultaProtocolo' => 'https://nfe.sefaz.go.gov.br/nfe/services/NFeConsultaProtocolo4',
                    'NfeStatusServico' => 'https://nfe.sefaz.go.gov.br/nfe/services/NFeStatusServico4',
                    'RecepcaoEvento' => 'https://nfe.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4',
                    'CscNFCe' => 'https://nfe.sefaz.go.gov.br/nfe/services/v2/CscNFCe',
                    'NfeConsultaQR' => 'http://nfe.sefaz.go.gov.br/nfeweb/sites/nfce/danfeNFCe'
                ],
                '2' => [
                    'NfeAutorizacao' => 'https://homolog.sefaz.go.gov.br/nfe/services/NFeAutorizacao4',
                    'NfeRetAutorizacao' => 'https://homolog.sefaz.go.gov.br/nfe/services/NFeRetAutorizacao4',
                    'NfeInutilizacao' => 'https://homolog.sefaz.go.gov.br/nfe/services/NFeInutilizacao4',
                    'NfeConsultaProtocolo' => 'https://homolog.sefaz.go.gov.br/nfe/services/NFeConsultaProtocolo4',
                    'NfeStatusServico' => 'https://homolog.sefaz.go.gov.br/nfe/services/NFeStatusServico4',
                    'RecepcaoEvento' => 'https://homolog.sefaz.go.gov.br/nfe/services/NFeRecepcaoEvento4',
                    'CscNFCe' => 'https://homolog.sefaz.go.gov.br/nfe/services/v2/CscNFCe',
                    'NfeConsultaQR' => 'http://homolog.sefaz.go.gov.br/nfeweb/sites/nfce/danfeNFCe'
                ]
            ],
            'SP' => [
                '1' => [
                    'NfeAutorizacao' => 'https://nfce.fazenda.sp.gov.br/ws/NFeAutorizacao4.asmx',
                    'NfeRetAutorizacao' => 'https://nfce.fazenda.sp.gov.br/ws/NFeRetAutorizacao4.asmx',
                    'NfeInutilizacao' => 'https://nfce.fazenda.sp.gov.br/ws/NFeInutilizacao4.asmx',
                    'NfeConsultaProtocolo' => 'https://nfce.fazenda.sp.gov.br/ws/NFeConsultaProtocolo4.asmx',
                    'NfeStatusServico' => 'https://nfce.fazenda.sp.gov.br/ws/NFeStatusServico4.asmx',
                    'RecepcaoEvento' => 'https://nfce.fazenda.sp.gov.br/ws/NFeRecepcaoEvento4.asmx',
                    'NfeConsultaQR' => 'https://www.nfce.fazenda.sp.gov.br/qrcode'
                ],
                '2' => [
                    'NfeAutorizacao' => 'https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeAutorizacao4.asmx',
                    'NfeRetAutorizacao' => 'https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeRetAutorizacao4.asmx',
                    'NfeInutilizacao' => 'https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeInutilizacao4.asmx',
                    'NfeConsultaProtocolo' => 'https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeConsultaProtocolo4.asmx',
                    'NfeStatusServico' => 'https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeStatusServico4.asmx',
                    'RecepcaoEvento' => 'https://homologacao.nfce.fazenda.sp.gov.br/ws/NFeRecepcaoEvento4.asmx',
                    'NfeConsultaQR' => 'https://www.homologacao.nfce.fazenda.sp.gov.br/qrcode'
                ]
            ],
            'SVRS' => [
                '1' => [
                    'NfeAutorizacao' => 'https://nfce.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx',
                    'NfeRetAutorizacao' => 'https://nfce.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx',
                    'NfeInutilizacao' => 'https://nfce.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx',
                    'NfeConsultaProtocolo' => 'https://nfce.svrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx',
                    'NfeStatusServico' => 'https://nfce.svrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx',
                    'RecepcaoEvento' => 'https://nfce.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx'
                ],
                '2' => [
                    'NfeAutorizacao' => 'https://nfce-homologacao.svrs.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx',
                    'NfeRetAutorizacao' => 'https://nfce-homologacao.svrs.rs.gov.br/ws/NfeRetAutorizacao/NFeRetAutorizacao4.asmx',
                    'NfeInutilizacao' => 'https://nfce-homologacao.svrs.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao4.asmx',
                    'NfeConsultaProtocolo' => 'https://nfce-homologacao.svrs.rs.gov.br/ws/NfeConsulta/NfeConsulta4.asmx',
                    'NfeStatusServico' => 'https://nfce-homologacao.svrs.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx',
                    'RecepcaoEvento' => 'https://nfce-homologacao.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx'
                ]
            ]
        ];

        // Verifica se a UF está no array de URLs
        if (isset($urls[$uf][$ambiente])) {
            return $urls[$uf][$ambiente];
        }

        // Se não encontrar a UF específica, usa o SVRS como fallback
        if (isset($urls['SVRS'][$ambiente])) {
            return $urls['SVRS'][$ambiente];
        }

        return null;
    }

    private function salvarXMLNFCe($xml, $protocolo, $idVenda)
    {
        // Salva o XML e o protocolo no banco de dados
        $data = [
            'vendas_id' => $idVenda,
            'xml' => $xml,
            'protocolo' => $protocolo,
            'data' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('nfce', $data);
    }

    private function gerarQRCode($emitente, $config, $totalProdutos) {
        // Gera o QR Code para NFC-e
        $chave = $this->gerarChaveNFCe($emitente, $config);
        $url = $this->getUrlConsulta($emitente->uf, $config->ambiente);
        
        if (empty($url)) {
            log_message('error', 'URL de consulta QR Code não encontrada para UF: ' . $emitente->uf);
            return '';
        }

        // Data e hora no formato YYYYMMDDHHmmss
        $dhEmi = date('YmdHis');

        // Monta a string do QR Code conforme especificação da NFCe
        $qrCode = $url . '?chNFe=' . $chave;
        $qrCode .= '&nVersao=100';
        $qrCode .= '&tpAmb=' . $config->ambiente;
        $qrCode .= '&dhEmi=' . $dhEmi; // Adiciona data e hora de emissão
        $qrCode .= '&cDest=' . preg_replace('/[^0-9]/', '', $emitente->cnpj);
        $qrCode .= '&vNF=' . number_format($totalProdutos, 2, '.', '');
        $qrCode .= '&vICMS=0.00';
        $qrCode .= '&digVal=' . $this->gerarDigestValue($chave);
        
        // Adiciona CSC se configurado
        if (!empty($config->csc_id) && !empty($config->csc)) {
            $qrCode .= '&cIdToken=' . $config->csc_id;
            $qrCode .= '&cHashQRCode=' . $this->gerarHashQRCode($qrCode, $config->csc);
        }

        return $qrCode;
    }

    private function gerarHashQRCode($qrCode, $csc) {
        return strtoupper(hash('sha256', $qrCode . $csc));
    }

    private function gerarChaveNFCe($emitente, $config) {
        // Gera a chave da NFC-e
        $cUF = $this->get_cUF($emitente->uf);
        $ano = date('y');
        $mes = date('m');
        $cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
        $mod = '65';
        $serie = str_pad('1', 3, '0', STR_PAD_LEFT);
        $numero = str_pad($config->sequencia_nfce, 9, '0', STR_PAD_LEFT);
        $tpEmis = '1';
        $codigo = $cUF . $ano . $mes . $cnpj . $mod . $serie . $numero . $tpEmis;
        
        // Calcula o DV
        $dv = $this->calculaDV($codigo);
        $codigo .= $dv;
        
        return $codigo;
    }

    private function getUrlConsulta($uf, $ambiente) {
        $urls = [
            'AC' => [
                'homologacao' => 'http://www.hml.sefaznet.ac.gov.br/nfce/qrcode',
                'producao' => 'http://www.sefaznet.ac.gov.br/nfce/qrcode'
            ],
            'AL' => [
                'homologacao' => 'http://nfce.sefaz.al.gov.br/QRCode/consultarNFCe.jsp',
                'producao' => 'http://nfce.sefaz.al.gov.br/QRCode/consultarNFCe.jsp'
            ],
            'AM' => [
                'homologacao' => 'https://sistemas.sefaz.am.gov.br/nfceweb-hom/consultarNFCe.jsp',
                'producao' => 'https://sistemas.sefaz.am.gov.br/nfceweb/consultarNFCe.jsp'
            ],
            'AP' => [
                'homologacao' => 'https://www.sefaz.ap.gov.br/nfcehml/nfce.php',
                'producao' => 'https://www.sefaz.ap.gov.br/nfce/nfcep.php'
            ],
            'BA' => [
                'homologacao' => 'http://hnfe.sefaz.ba.gov.br/servicos/nfce/qrcode.aspx',
                'producao' => 'http://nfe.sefaz.ba.gov.br/servicos/nfce/qrcode.aspx'
            ],
            'CE' => [
                'homologacao' => 'http://nfceh.sefaz.ce.gov.br/pages/ShowNFCe.html',
                'producao' => 'http://nfce.sefaz.ce.gov.br/pages/ShowNFCe.html'
            ],
            'DF' => [
                'homologacao' => 'http://dec.fazenda.df.gov.br/ConsultarNFCe.aspx',
                'producao' => 'http://www.fazenda.df.gov.br/nfce/qrcode'
            ],
            'ES' => [
                'homologacao' => 'http://homologacao.sefaz.es.gov.br/ConsultaNFCe/qrcode.aspx',
                'producao' => 'http://app.sefaz.es.gov.br/ConsultaNFCe/qrcode.aspx'
            ],
            'GO' => [
                'homologacao' => 'http://homolog.sefaz.go.gov.br/nfeweb/sites/nfce/danfeNFCe',
                'producao' => 'http://nfe.sefaz.go.gov.br/nfeweb/sites/nfce/danfeNFCe'
            ],
            'MA' => [
                'homologacao' => 'http://www.hom.nfce.sefaz.ma.gov.br/portal/consultarNFCe.jsp',
                'producao' => 'http://www.nfce.sefaz.ma.gov.br/portal/consultarNFCe.jsp'
            ],
            'MG' => [
                'homologacao' => 'https://portalsped.fazenda.mg.gov.br/portalnfce/sistema/qrcode.xhtml',
                'producao' => 'https://portalsped.fazenda.mg.gov.br/portalnfce/sistema/qrcode.xhtml'
            ],
            'MS' => [
                'homologacao' => 'http://www.dfe.ms.gov.br/nfce/qrcode',
                'producao' => 'http://www.dfe.ms.gov.br/nfce/qrcode'
            ],
            'MT' => [
                'homologacao' => 'http://homologacao.sefaz.mt.gov.br/nfce/consultanfce',
                'producao' => 'http://www.sefaz.mt.gov.br/nfce/consultanfce'
            ],
            'PA' => [
                'homologacao' => 'https://appnfc.sefa.pa.gov.br/portal-homologacao/view/consultas/nfce/nfceForm.seam',
                'producao' => 'https://appnfc.sefa.pa.gov.br/portal/view/consultas/nfce/nfceForm.seam'
            ],
            'PB' => [
                'homologacao' => 'http://www.sefaz.pb.gov.br/nfcehom',
                'producao' => 'http://www.sefaz.pb.gov.br/nfce'
            ],
            'PE' => [
                'homologacao' => 'http://nfcehomolog.sefaz.pe.gov.br/nfce/consulta',
                'producao' => 'http://nfce.sefaz.pe.gov.br/nfce/consulta'
            ],
            'PI' => [
                'homologacao' => 'http://www.sefaz.pi.gov.br/nfce/qrcode',
                'producao' => 'http://www.sefaz.pi.gov.br/nfce/qrcode'
            ],
            'PR' => [
                'homologacao' => 'http://www.fazenda.pr.gov.br/nfce/qrcode',
                'producao' => 'http://www.fazenda.pr.gov.br/nfce/qrcode'
            ],
            'RJ' => [
                'homologacao' => 'http://www4.fazenda.rj.gov.br/consultaNFCe/QRCode',
                'producao' => 'https://consultadfe.fazenda.rj.gov.br/consultaNFCe/QRCode'
            ],
            'RN' => [
                'homologacao' => 'http://hom.nfce.set.rn.gov.br/consultarNFCe.aspx',
                'producao' => 'http://nfce.set.rn.gov.br/consultarNFCe.aspx'
            ],
            'RO' => [
                'homologacao' => 'http://www.nfce.sefin.ro.gov.br/consultanfce/consulta.jsp',
                'producao' => 'http://www.nfce.sefin.ro.gov.br/consultanfce/consulta.jsp'
            ],
            'RR' => [
                'homologacao' => 'http://200.174.88.103:8080/nfce/servlet/qrcode',
                'producao' => 'https://www.sefaz.rr.gov.br/servlet/qrcode'
            ],
            'RS' => [
                'homologacao' => 'https://www.sefaz.rs.gov.br/NFCE/NFCE-COM.aspx',
                'producao' => 'https://www.sefaz.rs.gov.br/NFCE/NFCE-COM.aspx'
            ],
            'SC' => [
                'homologacao' => 'https://hom.sat.sef.sc.gov.br/nfce/consulta',
                'producao' => 'https://sat.sef.sc.gov.br/nfce/consulta'
            ],
            'SE' => [
                'homologacao' => 'http://www.hom.nfe.se.gov.br/nfce/qrcode',
                'producao' => 'http://www.nfce.se.gov.br/nfce/qrcode'
            ],
            'SP' => [
                'homologacao' => 'https://www.homologacao.nfce.fazenda.sp.gov.br/qrcode',
                'producao' => 'https://www.nfce.fazenda.sp.gov.br/qrcode'
            ],
            'to' => [
                'homologacao' => 'http://homologacao.sefaz.to.gov.br/nfce/qrcode',
                'producao' => 'http://www.sefaz.to.gov.br/nfce/qrcode'
            ]
        ];

        $ambiente = $ambiente == 1 ? 'producao' : 'homologacao';
        return isset($urls[$uf][$ambiente]) ? $urls[$uf][$ambiente] : '';
    }

    private function gerarDigestValue($chave) {
        // Gera o valor do digest para o QR Code
        return strtoupper(hash('sha1', $chave));
    }

    public function buscarTributacao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NF-e.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('nfe') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $venda_id = $this->input->post('venda_id');
            $tributacao = $this->nfe_model->buscarTributacao($venda_id);

            if ($tributacao) {
                $this->data['tributacao'] = $tributacao;
            } else {
                $this->session->set_flashdata('error', 'Não foi encontrada tributação para esta venda.');
            }
        }

        $this->data['view'] = 'nfe/buscarTributacao';
        return $this->layout();
    }


    public function autoCompleteVenda()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->nfe_model->autoCompleteVenda($q);
        }
    }

    public function buscarTributacoes()
    {
        $operacao_comercial_id = $this->input->post('operacao_comercial_id');
        $natureza_contribuinte = $this->input->post('natureza_contribuinte');
        $destinacao = $this->input->post('destinacao');
        $objetivo_comercial = $this->input->post('objetivo_comercial');

        // Get tax regime from configuration
        $this->load->model('Mapos_model');
        $configuracao = $this->Mapos_model->getConfiguracao();
        $regime_tributario = $configuracao['regime_tributario'];

        // Set CRT based on tax regime
        $crt = ($regime_tributario === 'Simples Nacional') ? '1' : '3';

        $this->load->model('ClassificacaoFiscal_model');
        $cliente = $this->Clientes_model->getById($venda->clientes_id);
      
        if ($tributacao) {
            $response = [
                'cfop' => $tributacao->cfop,
                'crt' => $crt
            ];

            // Add CST or CSOSN based on tax regime
            if ($regime_tributario === 'Simples Nacional') {
                $response['csosn'] = $tributacao->csosn;
                $response['cst'] = null;
            } else {
                $response['cst'] = $tributacao->cst;
                $response['csosn'] = null;
            }

            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Tributação não enco
ntrada']);
        }
    }

    public function reemitirNFCe()
    {
        try {
            if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
                throw new Exception('Você não tem permissão para emitir NFC-e.');
            }

            $nfe_id = $this->input->post('nfe_id');
            if (empty($nfe_id)) {
                throw new Exception('NFC-e não informada.');
            }

            // Busca a NFC-e original
            $this->db->where('id', $nfe_id);
            $nfe_original = $this->db->get('nfe_emitidas')->row();
            if (!$nfe_original) {
                throw new Exception('NFC-e original não encontrada.');
            }

            // Verifica se é realmente uma NFC-e
            if ($nfe_original->modelo != 65) {
                throw new Exception('O documento informado não é uma NFC-e.');
            }

            $venda_id = $nfe_original->venda_id;

            // Carrega os dados da venda
            $venda = $this->Vendas_model->getById($venda_id);
            if (!$venda) {
                throw new Exception('Venda não encontrada.');
            }

            // Carrega os dados do cliente
            $cliente = $this->Vendas_model->getCliente($venda_id);
            if (!$cliente) {
                throw new Exception('Cliente não encontrado para esta venda.');
            }

            // Carrega os dados do emitente
            $emitente = $this->Mapos_model->getEmitente();
            if (!$emitente) {
                throw new Exception('Dados do emitente não encontrados.');
            }

            // Carrega os produtos da venda
            $produtos = $this->Vendas_model->getProdutos($venda_id);
            if (empty($produtos)) {
                throw new Exception('Nenhum produto encontrado para esta venda.');
            }

            // Carrega as configurações do NFC-e
            $this->db->where('id', 1);
            $config = $this->db->get('configuracoes_nfce')->row();
            if (!$config) {
                throw new Exception('Configurações do NFC-e não encontradas.');
            }

            // Obtém o certificado
            $certificate = $this->nfe->getCertificate();
            if (!$certificate) {
                throw new Exception('Certificado digital não encontrado');
            }

            // Verifica se o certificado está vencido
            $dataValidade = new DateTime($certificate->data_validade);
            $hoje = new DateTime();
            if ($hoje > $dataValidade) {
                throw new Exception('O certificado digital está vencido. Por favor, atualize o certificado nas configurações do sistema.');
            }

            // Tenta ler o certificado
            try {
                // Lê o certificado diretamente do conteúdo binário
                $cert = \NFePHP\Common\Certificate::readPfx($certificate->certificado_digital, $certificate->senha_certificado);
            } catch (Exception $e) {
                throw new Exception('Erro ao ler o certificado digital: ' . $e->getMessage());
            }

            // Prepara a configuração em JSON
            $configJson = json_encode([
                "atualizacao" => date('Y-m-d H:i:s'),
                "tpAmb" => (int)$config->ambiente, // 1 = Produção, 2 = Homologação
                "razaosocial" => $emitente->nome,
                "siglaUF" => $emitente->uf,
                "cnpj" => preg_replace('/[^0-9]/', '', $emitente->cnpj),
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
                "tokenIBPT" => "",
                "csc" => $config->csc ?? "",
                "CSCid" => $config->csc_id ?? "",
                "aProxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                ]
            ]);

            // Cria o objeto Tools com a configuração e o certificado
            $tools = new \NFePHP\NFe\Tools($configJson, $cert);

            // Define o modelo como NFCe (65)
            $tools->model(65);

            // Cria objeto NFe
            $nfe = new Make();

            // [infNFe]
            $std = new \stdClass();
            $std->versao = '4.00';
            $nfe->taginfNFe($std);

            $this->load->model('Mapos_model');
            $this->load->model('Vendas_model');
            $this->load->model('Clientes_model');
            $this->load->model('Produtos_model');
            $this->load->model('ClassificacaoFiscal_model');
            $this->load->model('OperacaoComercial_model');
            $this->load->model('TributacaoProduto_model');
            $operacao_comercial_id = $this->input->post('operacao_comercial_id');
            $natureza_contribuinte = $this->input->post('natureza_contribuinte');
            $destinacao = $this->input->post('destinacao');
            $objetivo_comercial = $this->input->post('objetivo_comercial');
            $emitente = $this->Mapos_model->getEmitente();
            $tributacao = $this->ClassificacaoFiscal_model->getTributacao($operacao_comercial_id, $natureza_contribuinte, $destinacao, $objetivo_comercial);

            $operacao = $this->OperacaoComercial_model->getById($venda->operacao_comercial_id);

            // [ide]
            $std = new \stdClass();
            $std->cUF = $this->get_cUF($emitente->uf);
            $std->cNF = substr($nfe_original->chave_nfe, 25, 8); // Usa o código numérico da NFC-e original
            $std->natOp = $operacao->nome_operacao;
            $std->mod = 65; // Modelo NFC-e
            $std->serie = 1;
            $std->nNF = $nfe_original->numero_nfe; // Usa o número da NFC-e original
            $std->dhEmi = date('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
            $std->tpNF = 1;
            $std->idDest = 1;
            $std->cMunFG = $emitente->ibge;
            $std->tpImp = 4; // NFC-e
            $std->tpEmis = 1;
            $std->cDV = substr($nfe_original->chave_nfe, -1); // Usa o DV da NFC-e original
            $std->tpAmb = $config->ambiente;
            $std->finNFe = 1;
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = '4.00';

            // Usa a chave da NFC-e original
            $chNFe = $nfe_original->chave_nfe;
            
            $nfe->tagide($std);

            $this->db->select('valor');
            $this->db->from('configuracoes');
            $this->db->where('config', 'regime_tributario');
            $this->db->limit(1);
            $regime = $this->db->get()->row();
            $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;

            // [emit]
            $std = new \stdClass();
            $std->xNome = $emitente->nome;
            $std->xFant = $emitente->nome;
            $std->ie = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
            $std->CRT = $this->crt;
            $std->cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $nfe->tagemit($std);

            // [enderEmit]
            $std = new \stdClass();
            $std->xLgr = $emitente->rua;
            $std->nro = $emitente->numero;
            if (!empty($emitente->complemento)) {
                $std->xCpl = $emitente->complemento;
            }
            $std->xBairro = $emitente->bairro;
            $std->cMun = $emitente->ibge;
            $std->xMun = $emitente->cidade;
            $std->uf = $emitente->uf;
            $std->cep = preg_replace('/[^0-9]/', '', $emitente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
            $nfe->tagenderEmit($std);

            // [dest]
            $std = new \stdClass();
            $std->xNome = $cliente->nomeCliente;
            if (strlen(preg_replace('/[^0-9]/', '', $cliente->documento)) == 11) {
                $std->cpf = preg_replace('/[^0-9]/', '', $cliente->documento);
                $std->indIEDest = 9; // Contribuinte Isento
            } else {
                    $std->cnpj = preg_replace('/[^0-9]/', '', $cliente->documento);
                if (!empty($cliente->inscricao_estadual)) {
                    $std->ie = $cliente->inscricao_estadual;
                    $std->indIEDest = 1; // Contribuinte
                } else {
                    $std->indIEDest = 9; // Contribuinte Isento
                }
            }
            $nfe->tagdest($std);

            // [enderDest]
            $std = new \stdClass();
            $std->xLgr = $cliente->rua;
            $std->nro = $cliente->numero;
            if (!empty($cliente->complemento)) {
            $std->xCpl = $cliente->complemento;
            }
            $std->xBairro = $cliente->bairro;
            
            // Garante que o código do município (IBGE) está presente
            if (empty($cliente->ibge)) {
                throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
            }
            
            $std->cMun = $cliente->ibge;
            $std->xMun = $cliente->cidade;
            $std->uf = $cliente->estado;
            $std->cep = preg_replace('/[^0-9]/', '', $cliente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            if (!empty($cliente->telefone)) {
            $std->fone = preg_replace('/[^0-9]/', '', $cliente->telefone);
            }
            $nfe->tagenderDest($std);

            // Adiciona infCpl (informação complementar) conforme solicitado
            $std = new \stdClass();
            $std->infAdFisco = '';
            $std->infCpl = 'Devolução de Venda referente a nota ' . $nfe_original->numero_nfe . ' com chave: ' . $nfe_original->chave_nfe;
            $nfe->taginfAdic($std);


            // Produtos
            $i = 1; // Inicializa o contador em 1
            $totalProdutos = 0;
            $totalICMS = 0;
            foreach ($produtos as $p) {
                
                $emitente = $this->Mapos_model->getEmitente();
                $cliente = $this->Clientes_model->getById($venda->clientes_id);
                // Buscar tributação usando os parâmetros corretos
                $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
                $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                    $venda->operacao_comercial_id,
                    $cliente->natureza_contribuinte,
                    $destinacao, // destinação: Estadual ou Interestadual
                    $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
                );

                               // Buscar tributação do produto
               if ($p->tributacao_produto_id) {
                $tributacao_produto = $this->TributacaoProduto_model->getById($p->tributacao_produto_id);
                if ($tributacao_produto) {
                    // Adicionar informações de impostos do produto
                    $p->cst_ipi = $tributacao_produto->cst_ipi_saida;
                    $p->aliq_ipi = $tributacao_produto->aliq_ipi_saida;
                    $p->cst_pis = $tributacao_produto->cst_pis_saida;
                    $p->aliq_pis = $tributacao_produto->aliq_pis_saida;
                    $p->cst_cofins = $tributacao_produto->cst_cofins_saida;
                    $p->aliq_cofins = $tributacao_produto->aliq_cofins_saida;
                    $p->regime_fiscal = $tributacao_produto->regime_fiscal_tributario;
                }
            }
                // [prod]
                $std = new \stdClass();
                $std->item = $i; // Usa o contador que começa em 1
                $std->cProd = $p->idProdutos;
                $std->cEAN = 'SEM GTIN';
                $std->cEANTrib = 'SEM GTIN'; // Adicionado o campo cEANTrib
                $std->xProd = $p->descricao;
                $std->NCM = $p->NCMs;
                $std->cfop = $tributacao->cfop;
                $std->uCom = $p->unidade;
                $std->qCom = $p->quantidade;
                $std->vUnCom = $p->preco;
                $std->vProd = number_format($p->quantidade * $p->preco, 2, '.', '');
                $std->uTrib = $p->unidade;
                $std->qTrib = $p->quantidade;
                $std->vUnTrib = $p->preco;
                $std->indTot = 1;
                $nfe->tagprod($std);

                // [imposto]
                $std = new \stdClass();
                $std->item = $i; // Usa o mesmo contador
                $nfe->tagimposto($std); // Sempre necessário abrir o bloco com tagimposto()

               // ICMS
               $std = new \stdClass();
             $std->item = $i; // Usa o mesmo contador
             $std->orig = isset($p->origem) ? $p->origem : 0;
             
             if ($this->crt == 1) {
                 // Simples Nacional
                   $std->CSOSN = isset($p->csosn) ? $p->csosn : '102';
                 
                 // Calcula o valor do crédito ICMS para qualquer CSOSN usando o valor total
                 $total = number_format($p->preco * $p->quantidade, 2, '.', '');
                 
                 // Busca a alíquota de crédito ICMS das configurações
                 $this->db->select('valor');
                 $this->db->from('configuracoes');
                 $this->db->where('config', 'aliq_cred_icms');
                 $this->db->limit(1);
                 $aliq_cred = $this->db->get()->row();
                 $pCredSN = $aliq_cred ? str_replace(',', '.', $aliq_cred->valor) : 3.00;
                 
                 $std->pCredSN = number_format($pCredSN, 2, '.', '');
                 $std->vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                 
                   $nfe->tagICMSSN($std);
               } else {
                 // Lucro Real ou Presumido
                 if (!isset($tributacao->cst)) {
                       throw new Exception('CST não configurado na classificação fiscal');
                   }
                 $std->cst = $tributacao->cst;
                   $std->modBC = 3;
                   $std->vBC = $p->preco * $p->quantidade;
                 
                 // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                 if ($p->origem == 1 || $p->origem == 2 || $p->origem == 3 || $p->origem == 8) {
                     $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                 } else {
                     // Carrega o model de Alíquotas se ainda não foi carregado
                     $this->load->model('Aliquotas_model');
                     
                     // Busca a alíquota correspondente
                     $aliquota = $this->Aliquotas_model->getAliquota($emitente->uf, $cliente->estado);
                     
                     if (!$aliquota) {
                         $this->session->set_flashdata('error', 'Alíquotas não configuradas no sistema para a operação entre ' . $emitente->uf . ' e ' . $cliente->estado);
                         redirect(base_url() . 'index.php/vendas');
                         return;
                     }
                     
                     // Define a alíquota baseado no tipo de operação
                     $is_estadual = ($cliente->estado == $emitente->uf);
                     $aliq = $is_estadual ? $aliquota->aliquota_origem : $aliquota->aliquota_destino;
                 }
                 
                   $std->pICMS = $aliq;   
                   $std->vICMS = ($std->vBC * $std->pICMS) / 100;
                   $nfe->tagICMS($std);
                 $valor_icms = $std->vICMS;
               }

               // PIS
               $std = new \stdClass();
               $std->item = $i;
               $std->cst = isset($p->cst_pis) ? $p->cst_pis : '01';
               $base_calculo = $p->preco * $p->quantidade;
               $std->vBC = number_format($base_calculo, 2, '.', '');
               $std->pPIS = isset($p->aliq_pis) ? $p->aliq_pis : 0;
               $std->vPIS = number_format(($base_calculo * $std->pPIS) / 100, 2, '.', '');
               $nfe->tagPIS($std);

               // COFINS
               $std = new \stdClass();
               $std->item = $i;
               $std->cst = isset($p->cst_cofins) ? $p->cst_cofins : '01';
               $std->vBC = number_format($base_calculo, 2, '.', '');
               $std->pCOFINS = isset($p->aliq_cofins) ? $p->aliq_cofins : 0;
               $std->vCOFINS = number_format(($base_calculo * $std->pCOFINS) / 100, 2, '.', '');
               $nfe->tagCOFINS($std);

               // Atualiza totais
               $totalProdutos += floatval($p->quantidade) * floatval($p->preco);
            if (!isset($valor_icms)) $valor_icms = 0;
            $totalICMS += $valor_icms;
               $i++;
           }

            // Adiciona todas as mensagens DIFAL no infCpl geral da nota
            if (!empty($this->mensagensDIFAL)) {
            $std = new \stdClass();
                $std->infAdFisco = '';
                $std->infCpl = "DADOS ADICIONAIS\n\n" . implode("\n", $this->mensagensDIFAL);
                $nfe->taginfAdic($std);
            } else {
                // Adiciona mensagem padrão sobre crédito ICMS para Simples Nacional apenas se for contribuinte
                if ($this->crt == 1 && isset($dest->indIEDest) && $dest->indIEDest == 1) {
                    // Busca a mensagem do Simples Nacional das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'mensagem_simples_nacional');
                    $this->db->limit(1);
                    $mensagem = $this->db->get()->row();
                    
                    $std = new \stdClass();
                    $std->infAdFisco = '';
                    
                    // Calcula o valor do crédito ICMS usando o total
                    $total = number_format($p->preco * $p->quantidade, 2, '.', '');
                    $vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                    
                    $mensagemTexto = $mensagem ? $mensagem->valor : "CRÉDITO ICMS SIMPLES NACIONAL\nConforme Art. 23 da LC 123/2006, o valor do crédito de ICMS corresponde a [percentual]% da base de cálculo do Simples Nacional.";
                    
                    // Substitui os placeholders na mensagem
                    $mensagemTexto = str_replace('[percentual]', number_format($pCredSN, 2, ',', '.'), $mensagemTexto);
                    $mensagemTexto = str_replace('[valor]', number_format($vCredICMSSN, 2, ',', '.'), $mensagemTexto);
                    
                    $std->infCpl = "DADOS ADICIONAIS\n\n" . $mensagemTexto;
                $nfe->taginfAdic($std);
                }
            }

            // [total]
            $std = new \stdClass();
            $std->vBC = ($this->crt == 1) ? 0 : $totalProdutos; // Base zero para CRT 1
            $std->vICMS = ($this->crt == 1) ? 0 : $totalICMS; // ICMS zero para CRT 1
            $std->vICMSDeson = 0;
            $std->vBCST = 0;
            $std->vST = 0;
            $std->vProd = number_format($totalProdutos, 2, '.', '');
            $std->vFrete = 0;
            $std->vSeg = 0;
            $std->vDesc = 0;
            $std->vII = 0;
            $std->vIPI = 0;
            $std->vPIS = 0;
            $std->vCOFINS = 0;
            $std->vOutro = 0;
            $std->vNF = number_format($totalProdutos, 2, '.', '');
            $std->vTotTrib = 0;
            $nfe->tagICMSTot($std);

            // [transp]
            $std = new \stdClass();
            $std->modFrete = 9;
            
            // Add transportadora information if available
            if (isset($transportadora) && !empty($transportadora)) {
                $std->transporta = new \stdClass();
                $std->transporta->cnpj = preg_replace('/[^0-9]/', '', $transportadora->documento);
                $std->transporta->xNome = $transportadora->nomeCliente;
                $std->transporta->ie = !empty($transportadora->ie) ? $transportadora->ie : 'ISENTO';
                $std->transporta->xEnder = $transportadora->rua;
                $std->transporta->xMun = $transportadora->cidade;
                $std->transporta->uf = $transportadora->estado;
            }
            
            $nfe->tagtransp($std);

            // [pag]
            $std = new \stdClass();
            $std->indPag = 0; // 0 = pagamento à vista
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagpag($std);

            // [detPag]
            $std = new \stdClass();
            $std->tPag = '01'; // 01 = Dinheiro
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagdetPag($std);

            // [infNFeSupl] - Informações suplementares para NFC-e
            $std = new \stdClass();
            $std->qrCode = $this->gerarQRCode($emitente, $config, $totalProdutos);
            $std->urlChave = $this->getUrlConsulta($emitente->uf, $config->ambiente);
            $nfe->taginfNFeSupl($std);

            // Verifica se há erros nas tags antes de gerar o XML
            $errors = $nfe->getErrors();
            if (!empty($errors)) {
                $errorMessage = "Erros encontrados no XML da NFC-e:\n";
                foreach ($errors as $error) {
                    $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                }
                log_message('error', 'Erros na NFC-e: ' . $errorMessage);
                throw new Exception($errorMessage);
            }

            // Gera o XML
            $xml = $nfe->getXML();

            // Verifica novamente após gerar o XML
            $errors = $nfe->getErrors();
            if (!empty($errors)) {
                $errorMessage = "Erros encontrados no XML da NFC-e após geração:\n";
                foreach ($errors as $error) {
                    $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                }
                log_message('error', 'Erros na NFC-e após geração: ' . $errorMessage);
                throw new Exception($errorMessage);
            }

            // Assina o XML
            $signed = $tools->signNFe($xml);

            // Obtém a URL do webservice
            $url = $this->getWebserviceUrl($config->ambiente, $emitente->uf);
            if (!$url) {
                throw new Exception('URL do webservice não encontrada para a UF ' . $emitente->uf);
            }

            // Obtém a URL específica para autorização
            $urlAutorizacao = $url['NfeAutorizacao'];

            // Gera o ID do lote
            $idLote = str_pad(1, 15, '0', STR_PAD_LEFT);

            // Envia a NFC-e para autorização (modo síncrono para uma única NFC-e)
            $response = $tools->sefazEnviaLote([$signed], $idLote, 1); // 1 = modo síncrono

            // Processa a resposta do envio
            $dom = new \DOMDocument();
            $dom->loadXML($response);
            
            // Verifica o status do envio
            $cStat = $dom->getElementsByTagName('cStat')->item(0);
            $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0);
            
            if (!$cStat) {
                throw new Exception('Status não encontrado na resposta da SEFAZ');
            }

            // Se o lote foi processado (104), precisa verificar o protocolo
            if ($cStat->nodeValue == '104') {
                // Procura pelo protocolo
                $protNFe = $dom->getElementsByTagName('protNFe')->item(0);
                if (!$protNFe) {
                    throw new Exception('Protocolo não encontrado na resposta da SEFAZ');
                }

                $infProt = $protNFe->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    throw new Exception('Informações do protocolo não encontradas');
                }

                // Verifica o status do protocolo
                $cStatProt = $infProt->getElementsByTagName('cStat')->item(0);
                $xMotivoProt = $infProt->getElementsByTagName('xMotivo')->item(0);
                $nProt = $infProt->getElementsByTagName('nProt')->item(0);

                if (!$cStatProt) {
                    throw new Exception('Status do protocolo não encontrado');
                }

                // Extrai o número da nota e chave do XML
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                if (!$infNFe) {
                    throw new Exception("Erro ao extrair informações da NFC-e: tag infNFe não encontrada");
                }
                
                $ide = $infNFe->getElementsByTagName('ide')->item(0);
                if (!$ide) {
                    throw new Exception("Erro ao extrair informações da NFC-e: tag ide não encontrada");
                }
                
                $nNF = $ide->getElementsByTagName('nNF')->item(0);
                if (!$nNF) {
                    throw new Exception("Erro ao extrair informações da NFC-e: tag nNF não encontrada");
                }
                $numero_nfe = $nNF->nodeValue;

                // Extrai a chave da NFC-e
                $chNFe = $infNFe->getAttribute('Id');
                if ($chNFe) {
                    $chNFe = str_replace('NFe', '', $chNFe); // Remove o prefixo 'NFe'
                }

                // Salva NFC-e emitida independente do status
                $nfeData = [
                    'xml' => (string)$xml,
                    'xml_protocolo' => (string)$response,
                    'status' => ($cStatProt->nodeValue == '100') ? 1 : 0,
                    'chave_retorno_evento' => $xMotivoProt ? $xMotivoProt->nodeValue : 'Motivo não informado',
                    'protocolo' => $nProt ? $nProt->nodeValue : '',
                    'chave_nfe' => (string)$chNFe,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'valor_total' => $totalProdutos
                ];

                // Atualiza o registro existente na tabela nfe_emitidas
                $this->db->where('id', $nfe_id);
                $this->db->update('nfe_emitidas', $nfeData);
                
                if ($this->db->affected_rows() == 0) {
                    throw new Exception('Erro ao atualizar NFC-e no banco de dados');
                }

                // Se a NFC-e foi autorizada, atualiza o campo emitida_nfe na tabela vendas
                if ($cStatProt->nodeValue == '100') {
                    $this->db->where('idVendas', $nfe_original->venda_id);
                    $this->db->update('vendas', ['emitida_nfe' => true]);
                }


                // Prepara dados para o modal
                $modalData = [
                    'status' => ($cStatProt->nodeValue == '100') ? 'Autorizada' : 'Rejeitada',
                    'motivo' => $xMotivoProt ? $xMotivoProt->nodeValue : 'Motivo não informado',
                    'protocolo' => $response,
                    'xml' => $xml,
                    'chave_nfe' => $chNFe,
                    'numero_nfe' => $numero_nfe,
                    'modelo' => 65 // Modelo NFC-e
                ];
                
                $this->session->set_flashdata('nfe_modal', $modalData);
                
                if ($cStatProt->nodeValue == '100') {
                    $this->session->set_flashdata('success', 'NFC-e emitida com sucesso!');
                    redirect(base_url() . 'index.php/nfe/gerenciar');
                } else {
                    $this->session->set_flashdata('error', 'NFC-e rejeitada: ' . $xMotivoProt->nodeValue);
                }
                
                redirect(base_url() . 'index.php/nfe/gerenciar');
            }
            // Se não foi processado, lança exceção com o motivo
            else {
                $motivo = $xMotivo ? $xMotivo->nodeValue : 'Erro desconhecido';
                
                // Prepara dados para o modal mesmo em caso de erro
                $modalData = [
                    'status' => 'Rejeitada',
                    'motivo' => $motivo,
                    'protocolo' => $response,
                    'xml' => $xml,
                    'chave_nfe' => '',
                    'numero_nfe' => ''
                ];
                
                $this->session->set_flashdata('nfe_modal', $modalData);
                $this->session->set_flashdata('error', 'Erro ao emitir NFC-e: ' . $motivo);
                redirect(base_url() . 'index.php/nfe/gerenciar');
            }

        } catch (Exception $e) {
            log_message('error', 'Erro ao enviar NFC-e: ' . $e->getMessage());
            
            // Prepara dados para o modal em caso de exceção
            $modalData = [
                'status' => 'Rejeitada',
                'motivo' => $e->getMessage(),
                'protocolo' => '',
                'xml' => isset($xml) ? $xml : '',
                'chave_nfe' => '',
                'numero_nfe' => ''
            ];
            
            $this->session->set_flashdata('nfe_modal', $modalData);
            $this->session->set_flashdata('error', $e->getMessage());
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }
    }
    

    public function devolver()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
        }

        $nfe_id = $this->input->post('nfe_id');
        $devolver_todos = $this->input->post('devolver_todos');
        $itens_selecionados = $this->input->post('itens_selecionados');
        $quantidades = $this->input->post('quantidades');

        if (empty($nfe_id)) {
            $this->session->set_flashdata('error', 'NFe não informada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Busca a NFe original
        $nfe_original = $this->nfe->getNfe($nfe_id);
        if (!$nfe_original) {
            $this->session->set_flashdata('error', 'NFe não encontrada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Busca a venda original
        $venda = $this->Vendas_model->getById($nfe_original->venda_id);
        if (!$venda) {
            $this->session->set_flashdata('error', 'Venda não encontrada.');
            redirect(base_url() . 'index.php/nfe/buscarVendas');
        }

        // Carrega dados do cliente
        $cliente = $this->Vendas_model->getCliente($nfe_original->venda_id);
        if (!$cliente) {
            $this->session->set_flashdata('error', 'Cliente não encontrado.');
            redirect(base_url() . 'index.php/nfe/buscarVendas');
        }

        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            $this->session->set_flashdata('error', 'Emitente não configurado.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Carrega produtos da venda
        $produtos = $this->Vendas_model->getProdutos($nfe_original->venda_id);
        if (empty($produtos)) {
            $this->session->set_flashdata('error', 'Venda sem produtos.');
            redirect(base_url() . 'index.php/nfe');
        }

        // Se não for devolver todos os itens, verifica se há itens selecionados
        if ($devolver_todos !== 'true' && empty($itens_selecionados)) {
            $this->session->set_flashdata('error', 'Selecione pelo menos um item para devolução.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }

        // Carrega configurações da NFe
        $this->db->where('idConfiguracao', 1);
        $configNFe = $this->db->get('configuracoes_nfe')->row();
        if (!$configNFe) {
            $this->session->set_flashdata('error', 'Configurações de NFe não encontradas. Configure as configurações de NFe primeiro.');
            redirect(base_url() . 'index.php/nfe/configuracoesNFe');
        }

        try {
            // Cria objeto NFe
            $nfe = new Make();

            $this->load->model('Mapos_model');
            $this->load->model('Vendas_model');
            $this->load->model('Clientes_model');
            $this->load->model('Produtos_model');
            $this->load->model('ClassificacaoFiscal_model');
            $this->load->model('OperacaoComercial_model');
            $this->load->model('TributacaoProduto_model');
    
            // [infNFe]
            $std = new \stdClass();
            $std->versao = '4.00';
            $nfe->taginfNFe($std);

            // [ide]
            $std = new \stdClass();
            $std->cUF = $this->get_cUF($emitente->uf);
            $std->cNF = rand(10000000, 99999999);
            $std->natOp = 'Devolução de Venda';
            $std->mod = 55;
            $std->serie = 1;
            $std->nNF = $configNFe->sequencia_nota;
            $std->dhEmi = date('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
            $std->tpNF = 0;
            $std->idDest = ($cliente->estado != $emitente->uf) ? '2' : '1';
            $std->cMunFG = $emitente->ibge;
            $std->tpImp = $configNFe->tipo_impressao_danfe;
            $std->tpEmis = 1;
            $std->cDV = 0;
            $std->tpAmb = $configNFe->ambiente;
            $std->finNFe = 4; // 4 = Devolução
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = $configNFe->versao_nfe;

            // Adiciona referência à NFe original
            if (!empty($nfe_original->chave_nfe)) {
                $std->NFref = new \stdClass();
                $std->NFref->refNFe = $nfe_original->chave_nfe;
                $nfe->tagrefNFe($std->NFref);
            } else {
                throw new Exception('Chave da NFe original não encontrada. Não é possível emitir NFe de devolução sem referência.');
            }

            // Gera o código numérico da NFe
            $cUF = $this->get_cUF($emitente->uf);
            $ano = date('y');
            $mes = date('m');
            $cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $mod = '55';
            $serie = str_pad('1', 3, '0', STR_PAD_LEFT);
            $numero = str_pad($configNFe->sequencia_nota, 9, '0', STR_PAD_LEFT);
            $tpEmis = '1';
            $codigo = $cUF . $ano . $mes . $cnpj . $mod . $serie . $numero . $tpEmis;
            
            // Calcula o DV
            $dv = $this->calculaDV($codigo);
            $codigo .= $dv;
            
            // Define o código numérico
            $std->cNF = substr($codigo, -8);
            $std->cDV = $dv;
            
            $nfe->tagide($std);

            $this->db->select('valor');
            $this->db->from('configuracoes');
            $this->db->where('config', 'regime_tributario');
            $this->db->limit(1);
            $regime = $this->db->get()->row();
            $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;

            // [emit]
            $std = new \stdClass();
            $std->xNome = $emitente->nome;
            $std->xFant = $emitente->nome;
            $std->ie = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
            $std->CRT = $this->crt;
            $std->cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $nfe->tagemit($std);

            // [enderEmit]
            $std = new \stdClass();
            $std->xLgr = $emitente->rua;
            $std->nro = $emitente->numero;
            if (!empty($emitente->complemento)) {
                $std->xCpl = $emitente->complemento;
            }
            $std->xBairro = $emitente->bairro;
            $std->cMun = $emitente->ibge;
            $std->xMun = $emitente->cidade;
            $std->uf = $emitente->uf;
            $std->cep = preg_replace('/[^0-9]/', '', $emitente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
            $nfe->tagenderEmit($std);

            // [dest]
            $std = new \stdClass();
            $std->xNome = $cliente->nomeCliente;
            if (strlen(preg_replace('/[^0-9]/', '', $cliente->documento)) == 11) {
                $std->cpf = preg_replace('/[^0-9]/', '', $cliente->documento);
                $std->indIEDest = 9; // Contribuinte Isento
            } else {
                $std->cnpj = preg_replace('/[^0-9]/', '', $cliente->documento);
                if (!empty($cliente->inscricao_estadual)) {
                    $std->ie = $cliente->inscricao_estadual;
                    $std->indIEDest = 1; // Contribuinte
                } else {
                    $std->indIEDest = 9; // Contribuinte Isento
                }
            }
            $nfe->tagdest($std);

            // [enderDest]
            $std = new \stdClass();
            $std->xLgr = $cliente->rua;
            $std->nro = $cliente->numero;
            if (!empty($cliente->complemento)) {
            $std->xCpl = $cliente->complemento;
            }
            $std->xBairro = $cliente->bairro;
            
            // Garante que o código do município (IBGE) está presente
            if (empty($cliente->ibge)) {
                throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
            }
            
            $std->cMun = $cliente->ibge;
            $std->xMun = $cliente->cidade;
            $std->uf = $cliente->estado;
            $std->cep = preg_replace('/[^0-9]/', '', $cliente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            if (!empty($cliente->telefone)) {
            $std->fone = preg_replace('/[^0-9]/', '', $cliente->telefone);
            }
            $nfe->tagenderDest($std);

            // Adiciona infCpl (informação complementar) conforme solicitado
            $std = new \stdClass();
            $std->infAdFisco = '';
            if ($devolver_todos === 'true') {
                $std->infCpl = 'Devolução de Venda referente a nota ' . $nfe_original->numero_nfe . ' com chave: ' . $nfe_original->chave_nfe;
            } else {
                $std->infCpl = 'Devolução de Venda Parcial referente a nota ' . $nfe_original->numero_nfe . ' com chave: ' . $nfe_original->chave_nfe;
            }
            $nfe->taginfAdic($std);

            // Produtos
            $i = 1;
            $totalProdutos = 0;
            $totalICMS = 0; // Inicializa o total de ICMS

            foreach ($produtos as $produto) {
                // Se não for devolver todos, verifica se o item está selecionado
                if ($devolver_todos !== 'true' && !in_array($produto->idProdutos, $itens_selecionados)) {
                    continue;
                }

                // Se houver quantidades específicas, usa a quantidade informada
                $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                             (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);

                $emitente = $this->Mapos_model->getEmitente();
                $cliente = $this->Clientes_model->getById($venda->clientes_id);
                
                // Buscar tributação usando os parâmetros corretos
                $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
                $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                    $venda->operacao_comercial_id,
                    $cliente->natureza_contribuinte,
                    $destinacao, // destinação: Estadual ou Interestadual
                    $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
                );

                if (!$tributacao) {
                    $this->session->set_flashdata('error', 'Não foi encontrada tributação para a operação comercial selecionada na venda. Por favor, verifique se existe uma classificação fiscal cadastrada com os parâmetros: Operação Comercial ID: ' . $venda->operacao_comercial_id . ', Natureza Contribuinte: ' . $cliente->natureza_contribuinte . ', Destinação: ' . $destinacao . ', Objetivo Comercial: ' . $cliente->objetivo_comercial);
                    redirect(base_url('vendas'));
                }

                // Definir CFOP e CST/CSOSN baseado na tributação encontrada;
                if (strtolower($this->crt) === 'simples nacional') {
                    $produto->csosn = $tributacao->csosn;
                    $produto->cst = null;
                } else {
                    $produto->cst = $tributacao->cst;
                    $produto->csosn = null;
                }

                // Buscar tributação do produto
                if ($produto->tributacao_produto_id) {
                    $tributacao_produto = $this->TributacaoProduto_model->getById($produto->tributacao_produto_id);
                    if ($tributacao_produto) {
                        // Adicionar informações de impostos do produto
                        $produto->cst_ipi = $tributacao_produto->cst_ipi_saida;
                        $produto->aliq_ipi = $tributacao_produto->aliq_ipi_saida;
                        $produto->cst_pis = $tributacao_produto->cst_pis_saida;
                        $produto->aliq_pis = $tributacao_produto->aliq_pis_saida;
                        $produto->cst_cofins = $tributacao_produto->cst_cofins_saida;
                        $produto->aliq_cofins = $tributacao_produto->aliq_cofins_saida;
                        $produto->regime_fiscal = $tributacao_produto->regime_fiscal_tributario;
                    }
                }

                // Calcula o valor do ICMS
                $valor_icms = 0;
                if ($this->crt == 3) { // Lucro Real
                    // Carrega o model de Alíquotas se ainda não foi carregado
                    $this->load->model('Aliquotas_model');
                    
                    $is_estadual = ($cliente->estado == $emitente->uf);
                    
                    // Busca a alíquota correspondente
                    $aliquota = $this->Aliquotas_model->getAliquota($emitente->uf, $cliente->estado);
                    
                    if (!$aliquota) {
                        $this->session->set_flashdata('error', 'Alíquotas não configuradas no sistema para a operação entre ' . $emitente->uf . ' e ' . $cliente->estado);
                        redirect(base_url() . 'index.php/vendas');
                        return;
                    }
                    
                    // Define a alíquota baseado no tipo de operação
                    $aliq = $is_estadual ? $aliquota->aliquota_origem : $aliquota->aliquota_destino;
                    $valor_icms = ($produto->preco * $quantidade * $aliq) / 100;
                    
                    // Se for operação interestadual e cliente não inscrito, calcula o DIFAL
                    if (!$is_estadual && $cliente->natureza_contribuinte === 'nao_inscrito') {
                        $vBCUFDest = $produto->preco * $quantidade; // Base de cálculo
                        
                        // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                        if ($produto->origem == 1 || $produto->origem == 2 || $produto->origem == 3 || $produto->origem == 8) {
                            $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                            $pICMSInter = 4.00;
                        } else {
                            $pICMSInter = $aliq; // Alíquota interestadual normal
                        }
                        
                        // Busca a alíquota do estado de destino (estado do cliente)
                        $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                        if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                            $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                            redirect(base_url() . 'index.php/vendas');
                            return;
                        }
                        
                        $pICMSUFDest = $aliquota_destino->aliquota_origem; // Alíquota interna UF destino
                        
                        // Calcula o DIFAL - primeiro a diferença das alíquotas, depois aplica na base
                        $difAliquotas = $pICMSUFDest - $pICMSInter; // Exemplo: 19% - 12% = 7%
                        $difal = ($difAliquotas * $vBCUFDest) / 100; // Aplica a diferença na base
                        
                        // Adiciona a tag ICMSUFDest
                        $std = new \stdClass();
                        $std->item = $i;
                        $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                        $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                        $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                        $std->pICMSInterPart = 100.00;
                        $std->vICMSUFDest = number_format($difal, 2, '.', '');
                        $std->vICMSUFRemet = '0.00';
                        $nfe->tagICMSUFDest($std);

                        // Adiciona o DIFAL no obsCont e infAdic se for maior que 0
                        if ($difal > 0) {
                            $mensagemDIFAL = "DIFAL: R$ " . number_format($difal, 2, ',', '.') . 
                                           " (Diferença entre alíquotas: " . number_format($difAliquotas, 2, ',', '.') . "%)";

                            // Escapar o texto para garantir que o XML seja válido
                            $mensagemDIFAL = htmlspecialchars($mensagemDIFAL, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                            
                            // Armazena a mensagem DIFAL para ser usada depois no infCpl geral
                            if (!isset($this->mensagensDIFAL)) {
                                $this->mensagensDIFAL = [];
                            }
                            $this->mensagensDIFAL[] = $mensagemDIFAL;
                        }
                    } else if (!$is_estadual) {
                        // Se for operação interestadual mas cliente é inscrito, ainda precisa gerar ICMSUFDest
                        $vBCUFDest = $produto->preco * $quantidade;
                        
                        // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                        if ($produto->origem == 1 || $produto->origem == 2 || $produto->origem == 3 || $produto->origem == 8) {
                            $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                            $pICMSInter = 4.00;
                        } else {
                            $pICMSInter = $aliq; // Alíquota interestadual normal
                        }
                        
                        $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                        if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                            $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                            redirect(base_url() . 'index.php/vendas');
                            return;
                        }
                        
                        $pICMSUFDest = $aliquota_destino->aliquota_origem;
                        
                        $std = new \stdClass();
                        $std->item = $i;
                        $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                        $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                        $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                        $std->pICMSInterPart = 100.00;
                        $std->vICMSUFDest = '0.00';
                        $std->vICMSUFRemet = '0.00';
                        $nfe->tagICMSUFDest($std);
                    }
                }
                
                $natureza_contribuinte = $this->input->post('natureza_contribuinte');
                $emitente = $this->Mapos_model->getEmitente();
                $cliente = $this->Clientes_model->getById($venda->clientes_id);
                   // Buscar tributação usando os parâmetros corretos
                   $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
                   $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                       $venda->operacao_comercial_id,
                       $cliente->natureza_contribuinte,
                       $destinacao, // destinação: Estadual ou Interestadual
                       $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
                   );
                // [prod]
                $std = new \stdClass();
                $std->item = $i;
                $std->cProd = $produto->idProdutos;
                $std->cEAN = 'SEM GTIN';
                $std->cEANTrib = 'SEM GTIN'; // Adicionado o campo cEANTrib
                $std->xProd = $produto->descricao;
                $std->NCM = $produto->NCMs;
                $std->cfop = ($cliente->estado == $emitente->uf) ? '1202' : '2202';
                $std->uCom = 'UN';
                $std->qCom = $quantidade;
                $std->vUnCom = $produto->preco;
                $std->vProd = $produto->preco * $quantidade;
                // Campos obrigatórios adicionais
                $std->uTrib = 'UN'; // Unidade Tributável
                $std->qTrib = $quantidade; // Quantidade Tributável
                $std->vUnTrib = $produto->preco; // Valor Unitário de tributação
                $std->indTot = 1; // Indica se valor do Item entra no valor total da NF-e
                $std->cEAN = 'SEM GTIN'; // Código de barras
                $std->cEANTrib = 'SEM GTIN'; // Código de barras da unidade tributável
                $nfe->tagprod($std);

                // [imposto]
                $std = new \stdClass();
                $std->item = $i;
                $nfe->tagimposto($std); // Sempre necessário abrir o bloco com tagimposto()
                
                // ICMS
                $std = new \stdClass();
                $std->item = $i;
                $std->orig = $produto->origem;
                
                if ($this->crt == 1) {
                    // Simples Nacional
                    $std->CSOSN = isset($produto->csosn) ? $produto->csosn : '102';
                    
                    // Calcula o valor do crédito ICMS para qualquer CSOSN
                    $vBC = number_format($produto->preco * $quantidade, 2, '.', '');
                    
                    // Busca a alíquota de crédito ICMS das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'aliq_cred_icms');
                    $this->db->limit(1);
                    $aliq_cred = $this->db->get()->row();
                    $pCredSN = $aliq_cred ? str_replace(',', '.', $aliq_cred->valor) : 3.00;
                    
                    $std->pCredSN = number_format($pCredSN, 2, '.', '');
                    $std->vCredICMSSN = number_format(($vBC * $pCredSN) / 100, 2, '.', '');
                    
                    $nfe->tagICMSSN($std);
                } else {
                    // Lucro Real ou Presumido
                    if (!isset($produto->cst)) {
                       throw new Exception('CST não configurado na classificação fiscal');
                    }
                    $std->cst = isset($produto->cst) ? $produto->cst : '00';
                    $std->modBC = 3;
                    $std->vBC = $produto->preco * $quantidade;
                    $std->pICMS = $aliq;   
                    $std->vICMS = ($std->vBC * $std->pICMS) / 100;
                    $nfe->tagICMS($std);
                    $valor_icms = $std->vICMS;
                }
                
                // PIS
                $std = new \stdClass();
                $std->item = $i;
                $std->cst = isset($produto->cst_pis) ? $produto->cst_pis : '01';
                $base_calculo = $produto->preco * $quantidade;
                $std->vBC = number_format($base_calculo, 2, '.', '');
                $std->pPIS = isset($produto->aliq_pis) ? $produto->aliq_pis : 0;
                $std->vPIS = number_format(($base_calculo * $std->pPIS) / 100, 2, '.', '');
                $nfe->tagPIS($std);
                
                // COFINS
                $std = new \stdClass();
                $std->item = $i;
                $std->cst = isset($produto->cst_cofins) ? $produto->cst_cofins : '01';
                $std->vBC = number_format($base_calculo, 2, '.', '');
                $std->pCOFINS = isset($produto->aliq_cofins) ? $produto->aliq_cofins : 0;
                $std->vCOFINS = number_format(($base_calculo * $std->pCOFINS) / 100, 2, '.', '');
                $nfe->tagCOFINS($std);
                
                // Atualiza totais
                $totalProdutos += floatval($quantidade) * floatval($produto->preco);
                if (!isset($valor_icms)) $valor_icms = 0;
                $totalICMS += $valor_icms;
                $i++;
            }

            // Adiciona todas as mensagens DIFAL no infCpl geral da nota
            if (!empty($this->mensagensDIFAL)) {
                $std = new \stdClass();
                $std->infAdFisco = '';
                $std->infCpl = "DADOS ADICIONAIS\n\n" . implode("\n", $this->mensagensDIFAL);
                $nfe->taginfAdic($std);
            } else {
                // Adiciona mensagem padrão sobre crédito ICMS para Simples Nacional apenas se for contribuinte
                if ($this->crt == 1 && isset($dest->indIEDest) && $dest->indIEDest == 1) {
                    // Busca a mensagem do Simples Nacional das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'mensagem_simples_nacional');
                    $this->db->limit(1);
                    $mensagem = $this->db->get()->row();
                    
                    $std = new \stdClass();
                    $std->infAdFisco = '';
                    
                    // Calcula o valor do crédito ICMS usando o total
                    $total = number_format($produto->preco * $quantidade, 2, '.', '');
                    $vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                    
                    $mensagemTexto = $mensagem ? $mensagem->valor : "CRÉDITO ICMS SIMPLES NACIONAL\nConforme Art. 23 da LC 123/2006, o valor do crédito de ICMS corresponde a [percentual]% da base de cálculo do Simples Nacional.";
                    
                    // Substitui os placeholders na mensagem
                    $mensagemTexto = str_replace('[percentual]', number_format($pCredSN, 2, ',', '.'), $mensagemTexto);
                    $mensagemTexto = str_replace('[valor]', number_format($vCredICMSSN, 2, ',', '.'), $mensagemTexto);
                    
                    $std->infCpl = "DADOS ADICIONAIS\n\n" . $mensagemTexto;
                $nfe->taginfAdic($std);
                }
            }

            // [total]
            $std = new \stdClass();
            if ($this->crt == 1) {
            $std->vBC = number_format(0, 2, '.', '');
            $std->vICMS = number_format(0, 2, '.', '');
            } else {
            $std->vBC = number_format($totalProdutos, 2, '.', '');
            $std->vICMS = number_format($totalICMS, 2, '.', '');
            }
            $std->vICMSDeson = 0;
            $std->vBCST = 0;
            $std->vST = 0;
            $std->vProd = number_format($totalProdutos, 2, '.', '');
            $std->vFrete = 0;
            $std->vSeg = 0;
            $std->vDesc = 0;
            $std->vII = 0;
            $std->vIPI = 0;
            $std->vPIS = 0;
            $std->vCOFINS = 0;
            $std->vOutro = 0;
            $std->vNF = number_format($totalProdutos, 2, '.', '');
            $std->vTotTrib = 0;
            $nfe->tagICMSTot($std);

            // [transp]
            $std = new \stdClass();
            $std->modFrete = 9;
            
            // Add transportadora information if available
            if (isset($transportadora) && !empty($transportadora)) {
                $std->transporta = new \stdClass();
                $std->transporta->cnpj = preg_replace('/[^0-9]/', '', $transportadora->documento);
                $std->transporta->xNome = $transportadora->nomeCliente;
                $std->transporta->ie = !empty($transportadora->ie) ? $transportadora->ie : 'ISENTO';
                $std->transporta->xEnder = $transportadora->rua;
                $std->transporta->xMun = $transportadora->cidade;
                $std->transporta->uf = $transportadora->estado;
            }
            
            $nfe->tagtransp($std);

            // [pag]
            $std = new \stdClass();
            $std->indPag = 0; // 0 = pagamento à vista
            $std->tPag = '90'; // 90 = sem Pagamento
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagpag($std);

            // [detPag]
            $std = new \stdClass();
            $std->tPag = '90'; // 90 = sem Pagamento
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagdetPag($std);

            try {
            $xml = $nfe->getXML();
                
                // Obtém o objeto Tools com o certificado
                $tools = $this->getTools();

                // Verifica se há erros nas tags antes de prosseguir
                $errors = $nfe->getErrors();
                if (!empty($errors)) {
                    $errorMessage = "Erros encontrados no XML da NFC-e:\n";
                    foreach ($errors as $error) {
                        $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                    }
                    throw new Exception($errorMessage);
                }

            // Assina o XML
                $signed = $tools->signNFe($xml);

                // Envia para a SEFAZ
                $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
                $response = $tools->sefazEnviaLote([$signed], $idLote);
            $st = new \NFePHP\NFe\Common\Standardize($response);
            $std = $st->toStd();

                if ($std->cStat != 103) {
                    throw new Exception("Erro ao enviar NFe: " . $std->xMotivo);
                }

                $recibo = $std->infRec->nRec;
                
                // Aguarda o processamento do lote
                $tentativas = 0;
                $maxTentativas = 10;
                $status = 0;
                $chave_retorno_evento = '';
                
                while ($tentativas < $maxTentativas) {
                    // Consulta recibo
                    $protocolo = $tools->sefazConsultaRecibo($recibo);
                    
                    // Log da resposta bruta
                    log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                    
                    $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                    $std = $st->toStd();
                    
                    // Log do objeto padronizado
                    log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                    
                    // Verifica se o lote ainda está em processamento
                    if (isset($std->cStat) && $std->cStat == '105') {
                        // Lote em processamento, aguarda e tenta novamente
                        sleep(2);
                        $tentativas++;
                        continue;
                    }
                    
                    // Extrai o status real da NFe do protocolo
                    $dom = new \DOMDocument();
                    $dom->loadXML($protocolo);
                    
                    // Procura por diferentes tags possíveis
                    $infProt = $dom->getElementsByTagName('infProt')->item(0);
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                    }
                    
                    if (!$infProt) {
                        // Tenta extrair informações diretamente do objeto padronizado
                        if (isset($std->cStat)) {
                            $status = ($std->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsReciNFe->cStat)) {
                            $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsSitNFe->cStat)) {
                            $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                        } else {
                            throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                        }
                    } else {
                        $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                        $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                        
                        if ($cStat && $cStat->nodeValue == '100') {
                            $status = 1;
                            $chave_retorno_evento = "Autorizado o uso da NF-e";
                        } else {
                            $status = 0;
                            $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                        }
                    }
                    
                    // Se encontrou o status, sai do loop
                    if ($status != 0 || $chave_retorno_evento != '') {
                        break;
                    }
                    
                    // Aguarda antes da próxima tentativa
                    sleep(2);
                    $tentativas++;
                }
                
                if ($tentativas >= $maxTentativas) {
                    throw new Exception("Tempo limite excedido ao aguardar processamento do lote");
                }

                // Log da resposta bruta
                log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                
                $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                $std = $st->toStd();
                
                // Log do objeto padronizado
                log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                
                // Extrai o status real da NFe do protocolo
                $dom = new \DOMDocument();
                $dom->loadXML($protocolo);
                
                // Procura por diferentes tags possíveis
                $infProt = $dom->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                }
                
                if (!$infProt) {
                    // Tenta extrair informações diretamente do objeto padronizado
                    if (isset($std->cStat)) {
                        $status = ($std->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsReciNFe->cStat)) {
                        $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsSitNFe->cStat)) {
                        $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                    } else {
                        throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                    }
                } else {
                    $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                    $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                    
                    if ($cStat && $cStat->nodeValue == '100') {
                        $status = 1;
                        $chave_retorno_evento = "Autorizado o uso da NF-e";
                    } else {
                        $status = 0;
                        $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                    }
                }

                // Extrai o número da nota e chave do XML
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                if (!$infNFe) {
                    throw new Exception("Erro ao extrair informações da NFe: tag infNFe não encontrada");
                }
                
                $ide = $infNFe->getElementsByTagName('ide')->item(0);
                if (!$ide) {
                    throw new Exception("Erro ao extrair informações da NFe: tag ide não encontrada");
                }
                
                $nNF = $ide->getElementsByTagName('nNF')->item(0);
                if (!$nNF) {
                    throw new Exception("Erro ao extrair informações da NFe: tag nNF não encontrada");
                }
                $numero_nfe = $nNF->nodeValue;

                // Extrai a chave da NFe
                $chNFe = $infNFe->getAttribute('Id');
                if ($chNFe) {
                    $chNFe = str_replace('NFe', '', $chNFe); // Remove o prefixo 'NFe'
                }

                // Mostra a resposta da SEFAZ para análise
                log_message('debug', 'Resposta da SEFAZ: ' . $protocolo);

                // Salva NFe emitida
            $nfeData = [
                    'venda_id' => (int)$nfe_original->venda_id,
                    'modelo' => 55, // Modelo NFe
                    'numero_nfe' => (string)$numero_nfe,
                    'chave_nfe' => (string)$chNFe,
                    'xml' => (string)$signed,
                    'xml_protocolo' => (string)$protocolo,
                    'status' => $status,
                    'chave_retorno_evento' => $chave_retorno_evento,
                    'protocolo' => '', // Será preenchido posteriormente
                    'valor_total' => $totalProdutos, // Adiciona o valor total dos itens devolvidos
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
            ];

                // Insere na tabela nfe_emitidas
            $this->db->insert('nfe_emitidas', $nfeData);

                if ($this->db->affected_rows() == 0) {
                    throw new Exception('Erro ao salvar NFe no banco de dados');
                }

                // Atualiza o campo emitida_nfe na tabela vendas
                $this->db->where('idVendas', $nfe_original->venda_id);
                $this->db->update('vendas', ['emitida_nfe' => true]);

                // Atualiza a sequência da nota
            $this->db->where('idConfiguracao', 1);
                $this->db->update('configuracoes_nfe', [
                    'sequencia_nota' => $configNFe->sequencia_nota + 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Prepara dados para o modal
                $modalData = [
                    'status' => ($status == 1) ? 'Autorizada' : 'Rejeitada',
                    'motivo' => $chave_retorno_evento,
                    'protocolo' => $protocolo,
                    'xml' => $signed,
                    'chave_nfe' => $chNFe,
                    'numero_nfe' => $numero_nfe,
                    'modelo' => 55 // Modelo NFe
                ];
                
                $configNFe = $this->db->get('configuracoes_nfe')->row();
                if ($configNFe && isset($configNFe->preview_nfe) && $configNFe->preview_nfe) {
                    $nfe_emitida = $this->db->order_by('id', 'desc')->get('nfe_emitidas')->row();
                    if ($nfe_emitida) {
                        $this->session->set_flashdata('preview_nfe_id', $nfe_emitida->id);
                    }
                } else {
                    $this->session->set_flashdata('nfe_modal', $modalData);
                    $this->session->set_flashdata('success', 'Nota fiscal emitida com sucesso!');
                }
            redirect(base_url() . 'index.php/nfe/gerenciar');

        } catch (Exception $e) {
                $errors = $nfe->getErrors();
                $errorMessage = $e->getMessage();
                
                if (!empty($errors)) {
                    $errorMessage .= " - Erros nas tags: " . implode(", ", array_map(function($error) {
                        return str_replace('"', '', $error);
                    }, $errors));
                }
                
                // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
                $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
                $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
                $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
                redirect(base_url() . 'index.php/nfe');
            }

        } catch (Exception $e) {
            // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
            $errorMessage = $e->getMessage();
            $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
            $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
            $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
            redirect(base_url() . 'index.php/nfe');
        }
    }
    public function getItensVenda($nfe_id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
        }

        try {
            // Busca a NFe
            $nfe = $this->nfe->getNfe($nfe_id);
            if (!$nfe) {
                throw new Exception('NFe não encontrada.');
            }

            // Busca os produtos da venda
            $produtos = $this->Vendas_model->getProdutos($nfe->venda_id);
            if (empty($produtos)) {
                throw new Exception('Venda sem produtos.');
            }

            // Formata os dados para retorno
            $itens = array();
            foreach ($produtos as $produto) {
                $itens[] = array(
                    'idProdutos' => $produto->idProdutos,
                    'descricao' => $produto->descricao,
                    'quantidade' => $produto->quantidade,
                    'preco' => $produto->preco
                );
            }

            echo json_encode(array(
                'success' => true,
                'itens' => $itens
            ));

        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
    }
    public function devolucaoCompra($entrada_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
        }
    
            // Se não recebeu o ID da entrada como parâmetro, tenta pegar do POST ou GET
            if (empty($entrada_id)) {
                $entrada_id = $this->input->post('entrada_id') ? $this->input->post('entrada_id') : $this->input->get('entrada_id');
            }
    
            $devolver_todos = $this->input->post('devolver_todos');
            $itens_selecionados = $this->input->post('itens_selecionados');
            $quantidades = $this->input->post('quantidades');
    
            if (empty($entrada_id)) {
                $this->session->set_flashdata('error', 'Entrada não informada.');
                redirect(base_url() . 'index.php/faturamentoEntrada');
            }
    
            // Carrega o model de FaturamentoEntrada
            $this->load->model('FaturamentoEntrada_model');
            
            // Busca os dados da entrada
            $entrada = $this->FaturamentoEntrada_model->getById($entrada_id);
            if (!$entrada) {
                $this->session->set_flashdata('error', 'Entrada não encontrada.');
                redirect(base_url() . 'index.php/faturamentoEntrada');
            }
    
            // Busca a NFe original da entrada
            $this->db->where('id', $entrada->id);
            $nfe_original = $this->db->get('faturamento_entrada')->row();
            if (!$nfe_original) {
                $this->session->set_flashdata('error', 'NFe da entrada não encontrada.');
                redirect(base_url() . 'index.php/faturamentoEntrada');
            }
    
            // Carrega dados do fornecedor
            $this->load->model('Clientes_model');
            $fornecedor = $this->Clientes_model->getById($entrada->fornecedor_id);
            if (!$fornecedor) {
                $this->session->set_flashdata('error', 'Fornecedor não encontrado.');
                redirect(base_url() . 'index.php/faturamentoEntrada');
            }
    
            // Carrega dados do emitente
            $this->load->model('Mapos_model');
            $emitente = $this->Mapos_model->getEmitente();
            if (!$emitente) {
                $this->session->set_flashdata('error', 'Emitente não configurado.');
                redirect(base_url() . 'index.php/nfe');
            }
    
            // Carrega produtos da entrada
            $produtos = $this->FaturamentoEntrada_model->getItens($entrada_id);
            if (empty($produtos)) {
                $this->session->set_flashdata('error', 'Entrada sem produtos.');
                redirect(base_url() . 'index.php/faturamentoEntrada');
            }
    
            // Se for devolver todos, define os itens selecionados como todos os produtos
            if ($devolver_todos === 'true') {
                $itens_selecionados = array();
                $quantidades = array();
                foreach ($produtos as $produto) {
                    $itens_selecionados[] = $produto->produto_id;
                    $quantidades[$produto->produto_id] = $produto->quantidade;
                }
            } else if (empty($itens_selecionados)) {
                // Se não for devolver todos e não houver itens selecionados, redireciona para a página de seleção
                $this->data['entrada'] = $entrada;
                $this->data['produtos'] = $produtos;
                $this->data['view'] = 'faturamento_entrada/selecionarItensDevolucao';
                return $this->layout();
            }
    
            // Carrega configurações da NFe
            $this->db->where('idConfiguracao', 1);
            $configNFe = $this->db->get('configuracoes_nfe')->row();
            if (!$configNFe) {
                $this->session->set_flashdata('error', 'Configurações de NFe não encontradas. Configure as configurações de NFe primeiro.');
                redirect(base_url() . 'index.php/nfe/configuracoesNFe');
            }
    
            try {
                // Cria objeto NFe
                $nfe = new Make();
    
                $this->load->model('Mapos_model');
                $this->load->model('Clientes_model');
                $this->load->model('Produtos_model');
                $this->load->model('ClassificacaoFiscal_model');
                $this->load->model('OperacaoComercial_model');
                $this->load->model('TributacaoProduto_model');
                $this->load->model('FaturamentoEntrada_model');
    
                // [infNFe]
                $std = new \stdClass();
                $std->versao = '4.00';
                $nfe->taginfNFe($std);
    
                // [ide]
                $std = new \stdClass();
                $std->cUF = $this->get_cUF($emitente->uf);
                $std->cNF = rand(10000000, 99999999);
                $std->natOp = 'Devolução Compra';
                $std->mod = 55;
                $std->serie = 1;
                $std->nNF = $configNFe->sequencia_nota;
                $std->dhEmi = date('Y-m-d\TH:i:sP');
                $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
                $std->tpNF = 0;
                $std->idDest = ($fornecedor->estado != $emitente->uf) ? '2' : '1';
                $std->cMunFG = $emitente->ibge;
                $std->tpImp = $configNFe->tipo_impressao_danfe;
                $std->tpEmis = 1;
                $std->cDV = 0;
                $std->tpAmb = $configNFe->ambiente;
                $std->finNFe = 4; // 4 = Devolução
                $std->indFinal = 1;
                $std->indPres = 1;
                $std->procEmi = 0;
                $std->verProc = $configNFe->versao_nfe;
    
                // Adiciona referência à NFe original
                if (!empty($nfe_original->chave_nfe)) {
                    $std->NFref = new \stdClass();
                    $std->NFref->refNFe = $nfe_original->chave_nfe;
                    $nfe->tagrefNFe($std->NFref);
                } else {
                    throw new Exception('Chave da NFe original não encontrada. Não é possível emitir NFe de devolução sem referência.');
                }
    
                // Gera o código numérico da NFe
                $cUF = $this->get_cUF($emitente->uf);
                $ano = date('y');
                $mes = date('m');
                $cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
                $mod = '55';
                $serie = str_pad('1', 3, '0', STR_PAD_LEFT);
                $numero = str_pad($configNFe->sequencia_nota, 9, '0', STR_PAD_LEFT);
                $tpEmis = '1';
                $codigo = $cUF . $ano . $mes . $cnpj . $mod . $serie . $numero . $tpEmis;
                
                // Calcula o DV
                $dv = $this->calculaDV($codigo);
                $codigo .= $dv;
                
                // Define o código numérico
                $std->cNF = substr($codigo, -8);
                $std->cDV = $dv;
                
                $nfe->tagide($std);
    
                $this->db->select('valor');
                $this->db->from('configuracoes');
                $this->db->where('config', 'regime_tributario');
                $this->db->limit(1);
                $regime = $this->db->get()->row();
                $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;
    
                // [emit]
                $std = new \stdClass();
                $std->xNome = $emitente->nome;
                $std->xFant = $emitente->nome;
                $std->ie = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
                $std->CRT = $this->crt;
                $std->cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
                $nfe->tagemit($std);
    
                // [enderEmit]
                $std = new \stdClass();
                $std->xLgr = $emitente->rua;
                $std->nro = $emitente->numero;
                if (!empty($emitente->complemento)) {
                    $std->xCpl = $emitente->complemento;
                }
                $std->xBairro = $emitente->bairro;
                $std->cMun = $emitente->ibge;
                $std->xMun = $emitente->cidade;
                $std->uf = $emitente->uf;
                $std->cep = preg_replace('/[^0-9]/', '', $emitente->cep);
                $std->cPais = '1058';
                $std->xPais = 'BRASIL';
                $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
                $nfe->tagenderEmit($std);
    
                // [dest]
                $std = new \stdClass();
                $std->xNome = $fornecedor->nome;
                if (strlen(preg_replace('/[^0-9]/', '', $fornecedor->documento)) == 11) {
                    $std->cpf = preg_replace('/[^0-9]/', '', $fornecedor->documento);
                    $std->indIEDest = 9; // Contribuinte Isento
                } else {
                    $std->cnpj = preg_replace('/[^0-9]/', '', $fornecedor->documento);
                    if (!empty($fornecedor->inscricao_estadual)) {
                        $std->ie = $fornecedor->inscricao_estadual;
                        $std->indIEDest = 1; // Contribuinte
                    } else {
                        $std->indIEDest = 9; // Contribuinte Isento
                    }
                }
                $nfe->tagdest($std);
    
                // [enderDest]
                $std = new \stdClass();
                $std->xLgr = $fornecedor->rua;
                $std->nro = $fornecedor->numero;
                if (!empty($fornecedor->complemento)) {
                    $std->xCpl = $fornecedor->complemento;
                }
                $std->xBairro = $fornecedor->bairro;
                
                // Garante que o código do município (IBGE) está presente
                if (empty($fornecedor->ibge)) {
                    throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
                }
                
                $std->cMun = $fornecedor->ibge;
                $std->xMun = $fornecedor->cidade;
                $std->uf = $fornecedor->estado;
                $std->cep = preg_replace('/[^0-9]/', '', $fornecedor->cep);
                $std->cPais = '1058';
                $std->xPais = 'BRASIL';
                if (!empty($fornecedor->telefone)) {
                    $std->fone = preg_replace('/[^0-9]/', '', $fornecedor->telefone);
                }
                $nfe->tagenderDest($std);
    
                // Adiciona infCpl (informação complementar) conforme solicitado
                $std = new \stdClass();
                $std->infAdFisco = '';
                if ($devolver_todos === 'true') {
                    $std->infCpl = 'Devolução de Compra referente a nota ' . $nfe_original->numero_nfe . ' com chave: ' . $nfe_original->chave_nfe;
                } else {
                    $std->infCpl = 'Devolução de Compra Parcial referente a nota ' . $nfe_original->numero_nfe . ' com chave: ' . $nfe_original->chave_nfe;
                }
                $nfe->taginfAdic($std);
    
                // Produtos
                $i = 1;
                $totalProdutos = 0;
                $totalICMS = 0; // Inicializa o total de ICMS
    
                foreach ($produtos as $produto) {
                    // Se não for devolver todos, verifica se o item está selecionado
                    if ($devolver_todos !== 'true' && !in_array($produto->idProdutos, $itens_selecionados)) {
                        continue;
                    }
    
                    // Se houver quantidades específicas, usa a quantidade informada
                    $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                                 (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);
    
                    $emitente = $this->Mapos_model->getEmitente();
                    $cliente = $this->Clientes_model->getById($entrada->clientes_id);
                    
                    // Buscar tributação usando os parâmetros corretos
                    $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
                    $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                        $entrada->operacao_comercial_id,
                        $cliente->natureza_contribuinte,
                        $destinacao, // destinação: Estadual ou Interestadual
                        $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
                    );
    
                    if (!$tributacao) {
                        $this->session->set_flashdata('error', 'Não foi encontrada tributação para a operação comercial selecionada na entrada. Por favor, verifique se existe uma classificação fiscal cadastrada com os parâmetros: Operação Comercial ID: ' . $entrada->operacao_comercial_id . ', Natureza Contribuinte: ' . $cliente->natureza_contribuinte . ', Destinação: ' . $destinacao . ', Objetivo Comercial: ' . $cliente->objetivo_comercial);
                        redirect(base_url('vendas'));
                        return;
                    }
    
                    // Definir CFOP e CST/CSOSN baseado na tributação encontrada;
                    if (strtolower($this->crt) === 'simples nacional') {
                        $produto->csosn = $tributacao->csosn;
                        $produto->cst = null;
                    } else {
                        $produto->cst = $tributacao->cst;
                        $produto->csosn = null;
                    }
    
                    // Buscar tributação do produto
                    if ($produto->tributacao_produto_id) {
                        $tributacao_produto = $this->TributacaoProduto_model->getById($produto->tributacao_produto_id);
                        if ($tributacao_produto) {
                            // Adicionar informações de impostos do produto
                            $produto->cst_ipi = $tributacao_produto->cst_ipi_saida;
                            $produto->aliq_ipi = $tributacao_produto->aliq_ipi_saida;
                            $produto->cst_pis = $tributacao_produto->cst_pis_saida;
                            $produto->aliq_pis = $tributacao_produto->aliq_pis_saida;
                            $produto->cst_cofins = $tributacao_produto->cst_cofins_saida;
                            $produto->aliq_cofins = $tributacao_produto->aliq_cofins_saida;
                            $produto->regime_fiscal = $tributacao_produto->regime_fiscal_tributario;
                        }
                    }
    
                    // Calcula o valor do ICMS
                    $valor_icms = 0;
                    if ($this->crt == 3) { // Lucro Real
                        // Carrega o model de FaturamentoEntrada
                        $this->load->model('FaturamentoEntrada_model');
                        
                        // Busca os dados de ICMS do faturamento de entrada
                        $faturamento_entrada = $this->FaturamentoEntrada_model->getByNfeId($entrada->id);
                        
                        if (!$faturamento_entrada) {
                            $this->session->set_flashdata('error', 'Dados de ICMS do faturamento de entrada não encontrados.');
                            redirect(base_url() . 'index.php/vendas');
                            return;
                        }
                        
                        // Usa os valores do faturamento de entrada
                        $aliq = $faturamento_entrada->aliquota_icms;
                        $base_icms = $faturamento_entrada->base_icms;
                        $valor_icms = $faturamento_entrada->valor_icms;
                        
                        // Se for operação interestadual e cliente não inscrito, calcula o DIFAL
                        if (!$is_estadual && $cliente->natureza_contribuinte === 'nao_inscrito') {
                            $vBCUFDest = $base_icms; // Base de cálculo do faturamento de entrada
                            
                            // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                            if ($produto->origem == 1 || $produto->origem == 2 || $produto->origem == 3 || $produto->origem == 8) {
                                $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                                $pICMSInter = 4.00;
                            } else {
                                $pICMSInter = $aliq; // Alíquota interestadual do faturamento de entrada
                            }
                            
                            // Busca a alíquota do estado de destino (estado do cliente)
                            $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                            if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                                $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                                redirect(base_url() . 'index.php/vendas');
                                return;
                            }
                            
                            $pICMSUFDest = $aliquota_destino->aliquota_origem; // Alíquota interna UF destino
                            
                            // Calcula o DIFAL - primeiro a diferença das alíquotas, depois aplica na base
                            $difAliquotas = $pICMSUFDest - $pICMSInter; // Exemplo: 19% - 12% = 7%
                            $difal = ($difAliquotas * $vBCUFDest) / 100; // Aplica a diferença na base
                            
                            // Adiciona a tag ICMSUFDest
                            $std = new \stdClass();
                            $std->item = $i;
                            $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                            $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                            $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                            $std->pICMSInterPart = 100.00;
                            $std->vICMSUFDest = number_format($difal, 2, '.', '');
                            $std->vICMSUFRemet = '0.00';
                            $nfe->tagICMSUFDest($std);
    
                            // Adiciona o DIFAL no obsCont e infAdic se for maior que 0
                            if ($difal > 0) {
                                $mensagemDIFAL = "DIFAL: R$ " . number_format($difal, 2, ',', '.') . 
                                               " (Diferença entre alíquotas: " . number_format($difAliquotas, 2, ',', '.') . "%)";
    
                                // Escapar o texto para garantir que o XML seja válido
                                $mensagemDIFAL = htmlspecialchars($mensagemDIFAL, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                                
                                // Armazena a mensagem DIFAL para ser usada depois no infCpl geral
                                if (!isset($this->mensagensDIFAL)) {
                                    $this->mensagensDIFAL = [];
                                }
                                $this->mensagensDIFAL[] = $mensagemDIFAL;
                            }
                        } else if (!$is_estadual) {
                            // Se for operação interestadual mas cliente é inscrito, ainda precisa gerar ICMSUFDest
                            $vBCUFDest = $base_icms;
                            
                            // Verifica se a origem é 1, 2, 3 ou 8 para aplicar alíquota de 4%
                            if ($produto->origem == 1 || $produto->origem == 2 || $produto->origem == 3 || $produto->origem == 8) {
                                $aliq = 4.00; // Alíquota fixa de 4% para estas origens
                                $pICMSInter = 4.00;
                            } else {
                                $pICMSInter = $aliq; // Alíquota interestadual normal
                            }
                            
                            $aliquota_destino = $this->Aliquotas_model->getAliquota($cliente->estado, $cliente->estado);
                            if (!$aliquota_destino || !isset($aliquota_destino->aliquota_origem)) {
                                $this->session->set_flashdata('error', 'Alíquota interna não configurada para o estado do cliente (' . $cliente->estado . '). Configure as alíquotas antes de emitir a nota.');
                                redirect(base_url() . 'index.php/vendas');
                                return;
                            }
                            
                            $pICMSUFDest = $aliquota_destino->aliquota_origem;
                            
                            $std = new \stdClass();
                            $std->item = $i;
                            $std->vBCUFDest = number_format($vBCUFDest, 2, '.', '');
                            $std->pICMSUFDest = number_format($pICMSUFDest, 2, '.', '');
                            $std->pICMSInter = number_format($pICMSInter, 2, '.', '');
                            $std->pICMSInterPart = 100.00;
                            $std->vICMSUFDest = '0.00';
                            $std->vICMSUFRemet = '0.00';
                            $nfe->tagICMSUFDest($std);
                        }
                    }
                    
                    $natureza_contribuinte = $this->input->post('natureza_contribuinte');
                    $emitente = $this->Mapos_model->getEmitente();
                    $cliente = $this->Clientes_model->getById($entrada->clientes_id);
                       // Buscar tributação usando os parâmetros corretos
                       $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';
                       $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                           $entrada->operacao_comercial_id,
                           $cliente->natureza_contribuinte,
                           $destinacao, // destinação: Estadual ou Interestadual
                           $cliente->objetivo_comercial // objetivo_comercial: consumo ou revenda
                       );
                // [prod]
                $std = new \stdClass();
                    $std->item = $i;
                    $std->cProd = $produto->idProdutos;
                    $std->cEAN = 'SEM GTIN';
                    $std->cEANTrib = 'SEM GTIN';
                    $std->xProd = $produto->descricao;
                    $std->NCM = $produto->NCMs;
                    $std->cfop = ($fornecedor->estado == $emitente->uf) ? '5202' : '6202';
                    $std->uCom = 'UN';
                    $std->qCom = $quantidade;
                    $std->vUnCom = $produto->preco;
                    $std->vProd = $produto->preco * $quantidade;
                    $std->uTrib = 'UN';
                    $std->qTrib = $quantidade;
                    $std->vUnTrib = $produto->preco;
                    $std->indTot = 1;
                    $std->cEAN = 'SEM GTIN';
                    $std->cEANTrib = 'SEM GTIN';
                $nfe->tagprod($std);
    
                // [imposto]
                $std = new \stdClass();
                    $std->item = $i;
                    $nfe->tagimposto($std);
                    
                    // ICMS
                    $std = new \stdClass();
                    $std->item = $i;
                    $std->orig = $produto->origem;
                    
                    if ($this->crt == 1) {
                        // Simples Nacional
                        $std->CSOSN = isset($produto->csosn) ? $produto->csosn : '102';
                        
                        // Calcula o valor do crédito ICMS para qualquer CSOSN
                        $vBC = number_format($produto->preco * $quantidade, 2, '.', '');
                        
                        // Busca a alíquota de crédito ICMS das configurações
                        $this->db->select('valor');
                        $this->db->from('configuracoes');
                        $this->db->where('config', 'aliq_cred_icms');
                        $this->db->limit(1);
                        $aliq_cred = $this->db->get()->row();
                        $pCredSN = $aliq_cred ? str_replace(',', '.', $aliq_cred->valor) : 3.00;
                        
                        $std->pCredSN = number_format($pCredSN, 2, '.', '');
                        $std->vCredICMSSN = number_format(($vBC * $pCredSN) / 100, 2, '.', '');
                        
                        $nfe->tagICMSSN($std);
                    } else {
                        // Lucro Real ou Presumido
                        if (!isset($produto->cst)) {
                           throw new Exception('CST não configurado na classificação fiscal');
                        }
                        $std->cst = isset($produto->cst) ? $produto->cst : '00';
                        $std->modBC = 3;
                        $std->vBC = $produto->base_icms;
                        $std->pICMS = $produto->aliquota_icms;
                        $std->vICMS = $produto->valor_icms;
                        $nfe->tagICMS($std);
                        $valor_icms = $std->vICMS;
                    }
                    
                    // PIS
                $std = new \stdClass();
                $std->item = $i;
                    $std->cst = isset($produto->cst_pis) ? $produto->cst_pis : '01';
                    $base_calculo = $produto->preco * $quantidade;
                    $std->vBC = number_format($base_calculo, 2, '.', '');
                    $std->pPIS = isset($produto->aliq_pis) ? $produto->aliq_pis : 0;
                    $std->vPIS = number_format(($base_calculo * $std->pPIS) / 100, 2, '.', '');
                $nfe->tagPIS($std);
    
                    // COFINS
                $std = new \stdClass();
                $std->item = $i;
                    $std->cst = isset($produto->cst_cofins) ? $produto->cst_cofins : '01';
                    $std->vBC = number_format($base_calculo, 2, '.', '');
                    $std->pCOFINS = isset($produto->aliq_cofins) ? $produto->aliq_cofins : 0;
                    $std->vCOFINS = number_format(($base_calculo * $std->pCOFINS) / 100, 2, '.', '');
                $nfe->tagCOFINS($std);
    
                    // Atualiza totais
                    $totalProdutos += floatval($quantidade) * floatval($produto->preco);
                    if (!isset($valor_icms)) $valor_icms = 0;
                    $totalICMS += $valor_icms;
                    $i++;
                }
    
                // Adiciona todas as mensagens DIFAL no infCpl geral da nota
                if (!empty($this->mensagensDIFAL)) {
            $std = new \stdClass();
                    $std->infAdFisco = '';
                    $std->infCpl = "DADOS ADICIONAIS\n\n" . implode("\n", $this->mensagensDIFAL);
                    $nfe->taginfAdic($std);
                } else {
                    // Adiciona mensagem padrão sobre crédito ICMS para Simples Nacional apenas se for contribuinte
                    if ($this->crt == 1 && isset($dest->indIEDest) && $dest->indIEDest == 1) {
                        // Busca a mensagem do Simples Nacional das configurações
                        $this->db->select('valor');
                        $this->db->from('configuracoes');
                        $this->db->where('config', 'mensagem_simples_nacional');
                        $this->db->limit(1);
                        $mensagem = $this->db->get()->row();
                        
                        $std = new \stdClass();
                        $std->infAdFisco = '';
                        
                        // Calcula o valor do crédito ICMS usando o total
                        $total = number_format($produto->preco * $quantidade, 2, '.', '');
                        $vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                        
                        $mensagemTexto = $mensagem ? $mensagem->valor : "CRÉDITO ICMS SIMPLES NACIONAL\nConforme Art. 23 da LC 123/2006, o valor do crédito de ICMS corresponde a [percentual]% da base de cálculo do Simples Nacional.";
                        
                        // Substitui os placeholders na mensagem
                        $mensagemTexto = str_replace('[percentual]', number_format($pCredSN, 2, ',', '.'), $mensagemTexto);
                        $mensagemTexto = str_replace('[valor]', number_format($vCredICMSSN, 2, ',', '.'), $mensagemTexto);
                        
                        $std->infCpl = "DADOS ADICIONAIS\n\n" . $mensagemTexto;
                    $nfe->taginfAdic($std);
                    }
                }
    
                // [total]
                $std = new \stdClass();
                if ($this->crt == 1) {
                $std->vBC = number_format(0, 2, '.', '');
                $std->vICMS = number_format(0, 2, '.', '');
                } else {
                $std->vBC = number_format($totalProdutos, 2, '.', '');
                $std->vICMS = number_format($totalICMS, 2, '.', '');
                }
                $std->vICMSDeson = 0;
                $std->vBCST = 0;
                $std->vST = 0;
                $std->vProd = number_format($totalProdutos, 2, '.', '');
                $std->vFrete = 0;
                $std->vSeg = 0;
                $std->vDesc = 0;
                $std->vII = 0;
                $std->vIPI = 0;
                $std->vPIS = 0;
                $std->vCOFINS = 0;
                $std->vOutro = 0;
                $std->vNF = number_format($totalProdutos, 2, '.', '');
                $std->vTotTrib = 0;
            $nfe->tagICMSTot($std);
    
            // [transp]
            $std = new \stdClass();
            $std->modFrete = 9;
            
            // Add transportadora information if available
            if (isset($transportadora) && !empty($transportadora)) {
                $std->transporta = new \stdClass();
                $std->transporta->cnpj = preg_replace('/[^0-9]/', '', $transportadora->documento);
                $std->transporta->xNome = $transportadora->nomeCliente;
                $std->transporta->ie = !empty($transportadora->ie) ? $transportadora->ie : 'ISENTO';
                $std->transporta->xEnder = $transportadora->rua;
                $std->transporta->xMun = $transportadora->cidade;
                $std->transporta->uf = $transportadora->estado;
            }
            
            $nfe->tagtransp($std);
    
            // [pag]
            $std = new \stdClass();
                $std->indPag = 0; // 0 = pagamento à vista
                $std->tPag = '90'; // 90 = sem Pagamento
                $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagpag($std);
    
                // [detPag]
                $std = new \stdClass();
                $std->tPag = '90'; // 90 = sem Pagamento
                $std->vPag = number_format($totalProdutos, 2, '.', '');
                $nfe->tagdetPag($std);
    
            try {
                $xml = $nfe->getXML();
                
                // Obtém o objeto Tools com o certificado
                $tools = $this->getTools();
    
                // Verifica se há erros nas tags antes de prosseguir
                $errors = $nfe->getErrors();
                if (!empty($errors)) {
                    $errorMessage = "Erros encontrados no XML da NFC-e:\n";
                    foreach ($errors as $error) {
                        $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                    }
                    throw new Exception($errorMessage);
                }
    
            // Assina o XML
                $signed = $tools->signNFe($xml);
    
            // Envia para a SEFAZ
                $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
                $response = $tools->sefazEnviaLote([$signed], $idLote);
            $st = new \NFePHP\NFe\Common\Standardize($response);
            $std = $st->toStd();
    
                if ($std->cStat != 103) {
                    throw new Exception("Erro ao enviar NFe: " . $std->xMotivo);
                }
    
                $recibo = $std->infRec->nRec;
                
                // Aguarda o processamento do lote
                $tentativas = 0;
                $maxTentativas = 10;
                $status = 0;
                $chave_retorno_evento = '';
                
                while ($tentativas < $maxTentativas) {
                    // Consulta recibo
                    $protocolo = $tools->sefazConsultaRecibo($recibo);
                    
                    // Log da resposta bruta
                    log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                    
                    $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                    $std = $st->toStd();
                    
                    // Log do objeto padronizado
                    log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                    
                    // Verifica se o lote ainda está em processamento
                    if (isset($std->cStat) && $std->cStat == '105') {
                        // Lote em processamento, aguarda e tenta novamente
                        sleep(2);
                        $tentativas++;
                        continue;
                    }
                    
                    // Extrai o status real da NFe do protocolo
                    $dom = new \DOMDocument();
                    $dom->loadXML($protocolo);
                    
                    // Procura por diferentes tags possíveis
                    $infProt = $dom->getElementsByTagName('infProt')->item(0);
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                    }
                    
                    if (!$infProt) {
                        // Tenta extrair informações diretamente do objeto padronizado
                        if (isset($std->cStat)) {
                            $status = ($std->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsReciNFe->cStat)) {
                            $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsSitNFe->cStat)) {
                            $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                        } else {
                            throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                        }
                    } else {
                        $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                        $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                        
                        if ($cStat && $cStat->nodeValue == '100') {
                            $status = 1;
                            $chave_retorno_evento = "Autorizado o uso da NF-e";
                        } else {
                            $status = 0;
                            $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                        }
                    }
                    
                    // Se encontrou o status, sai do loop
                    if ($status != 0 || $chave_retorno_evento != '') {
                        break;
                    }
                    
                    // Aguarda antes da próxima tentativa
                    sleep(2);
                    $tentativas++;
                }
                
                if ($tentativas >= $maxTentativas) {
                    throw new Exception("Tempo limite excedido ao aguardar processamento do lote");
                }
    
                // Log da resposta bruta
                log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                
                $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                $std = $st->toStd();
                
                // Log do objeto padronizado
                log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                
                // Extrai o status real da NFe do protocolo
                $dom = new \DOMDocument();
                $dom->loadXML($protocolo);
                
                // Procura por diferentes tags possíveis
                $infProt = $dom->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                }
                
                if (!$infProt) {
                    // Tenta extrair informações diretamente do objeto padronizado
                    if (isset($std->cStat)) {
                        $status = ($std->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsReciNFe->cStat)) {
                        $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsSitNFe->cStat)) {
                        $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                    } else {
                        throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                    }
                } else {
                    $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                    $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                    
                    if ($cStat && $cStat->nodeValue == '100') {
                        $status = 1;
                        $chave_retorno_evento = "Autorizado o uso da NF-e";
                    } else {
                        $status = 0;
                        $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                    }
                }
    
                // Extrai o número da nota e chave do XML
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                if (!$infNFe) {
                    throw new Exception("Erro ao extrair informações da NFe: tag infNFe não encontrada");
                }
                
                $ide = $infNFe->getElementsByTagName('ide')->item(0);
                if (!$ide) {
                    throw new Exception("Erro ao extrair informações da NFe: tag ide não encontrada");
                }
                
                $nNF = $ide->getElementsByTagName('nNF')->item(0);
                if (!$nNF) {
                    throw new Exception("Erro ao extrair informações da NFe: tag nNF não encontrada");
                }
                $numero_nfe = $nNF->nodeValue;
    
                // Extrai a chave da NFe
                $chNFe = $infNFe->getAttribute('Id');
                if ($chNFe) {
                    $chNFe = str_replace('NFe', '', $chNFe); // Remove o prefixo 'NFe'
                }
    
                // Mostra a resposta da SEFAZ para análise
                log_message('debug', 'Resposta da SEFAZ: ' . $protocolo);
    
                // Salva NFe emitida
            $nfeData = [
                    'venda_id' => (int)$entrada->venda_id,
                    'modelo' => 55, // Modelo NFe
                    'numero_nfe' => (string)$numero_nfe,
                    'chave_nfe' => (string)$chNFe,
                    'xml' => (string)$signed,
                    'xml_protocolo' => (string)$protocolo,
                    'status' => $status,
                    'chave_retorno_evento' => $chave_retorno_evento,
                    'protocolo' => '', // Será preenchido posteriormente
                    'valor_total' => $totalProdutos, // Adiciona o valor total dos itens devolvidos
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
            ];
    
                // Insere na tabela nfe_emitidas
            $this->db->insert('nfe_emitidas', $nfeData);
    
                if ($this->db->affected_rows() == 0) {
                    throw new Exception('Erro ao salvar NFe no banco de dados');
                }
    
                // Atualiza o campo emitida_nfe na tabela vendas
                $this->db->where('idVendas', $entrada->venda_id);
                $this->db->update('vendas', ['emitida_nfe' => true]);
    
                // Atualiza a sequência da nota
            $this->db->where('idConfiguracao', 1);
                $this->db->update('configuracoes_nfe', [
                    'sequencia_nota' => $configNFe->sequencia_nota + 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
    
                // Prepara dados para o modal
                $modalData = [
                    'status' => ($status == 1) ? 'Autorizada' : 'Rejeitada',
                    'motivo' => $chave_retorno_evento,
                    'protocolo' => $protocolo,
                    'xml' => $signed,
                    'chave_nfe' => $chNFe,
                    'numero_nfe' => $numero_nfe,
                    'modelo' => 55 // Modelo NFe
                ];
                
                $configNFe = $this->db->get('configuracoes_nfe')->row();
                if ($configNFe && isset($configNFe->preview_nfe) && $configNFe->preview_nfe) {
                    $nfe_emitida = $this->db->order_by('id', 'desc')->get('nfe_emitidas')->row();
                    if ($nfe_emitida) {
                        $this->session->set_flashdata('preview_nfe_id', $nfe_emitida->id);
                    }
                } else {
                    $this->session->set_flashdata('nfe_modal', $modalData);
                    $this->session->set_flashdata('success', 'Nota fiscal emitida com sucesso!');
                }
            redirect(base_url() . 'index.php/nfe/gerenciar');
    
        } catch (Exception $e) {
                $errors = $nfe->getErrors();
                $errorMessage = $e->getMessage();
                
                if (!empty($errors)) {
                    $errorMessage .= " - Erros nas tags: " . implode(", ", array_map(function($error) {
                        return str_replace('"', '', $error);
                    }, $errors));
                }
                
                // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
                $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
                $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
                $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
                redirect(base_url() . 'index.php/nfe');
            }
    
        } catch (Exception $e) {
            // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
            $errorMessage = $e->getMessage();
            $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage); // Remove caracteres não imprimíveis
            $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); // Escapa para HTML/JS
            $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
            redirect(base_url() . 'index.php/nfe');
        }
    } 
}

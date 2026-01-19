<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Permissoes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar as permissões no sistema.');
            redirect(base_url());
        }

        $this->load->helper(['form', 'codegen_helper']);
        $this->load->model('permissoes_model');
        $this->data['menuConfiguracoes'] = 'Permissões';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('permissoes/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->permissoes_model->count('permissoes');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->permissoes_model->get('permissoes', 'idPermissao,nome,data,situacao', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'permissoes/permissoes';

        return $this->layout();
    }

    public function adicionar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $nomePermissao = $this->input->post('nome');
            $cadastro = date('Y-m-d');
            $situacao = 1;

            $permissoes = [
                'aCliente' => $this->input->post('aCliente'),
                'eCliente' => $this->input->post('eCliente'),
                'dCliente' => $this->input->post('dCliente'),
                'vCliente' => $this->input->post('vCliente'),

                // Pessoas
                'aPessoa' => $this->input->post('aPessoa'),
                'ePessoa' => $this->input->post('ePessoa'),
                'dPessoa' => $this->input->post('dPessoa'),
                'vPessoa' => $this->input->post('vPessoa'),

                // Contratos
                'aContrato' => $this->input->post('aContrato'),
                'eContrato' => $this->input->post('eContrato'),
                'dContrato' => $this->input->post('dContrato'),
                'vContrato' => $this->input->post('vContrato'),

                'aTipoCliente' => $this->input->post('aTipoCliente'),
                'eTipoCliente' => $this->input->post('eTipoCliente'),
                'dTipoCliente' => $this->input->post('dTipoCliente'),
                'vTipoCliente' => $this->input->post('vTipoCliente'),

                // Empresas
                'aEmpresa' => $this->input->post('aEmpresa'),
                'eEmpresa' => $this->input->post('eEmpresa'),
                'dEmpresa' => $this->input->post('dEmpresa'),
                'vEmpresa' => $this->input->post('vEmpresa'),

                'aProduto' => $this->input->post('aProduto'),
                'eProduto' => $this->input->post('eProduto'),
                'dProduto' => $this->input->post('dProduto'),
                'vProduto' => $this->input->post('vProduto'),

                'aServico' => $this->input->post('aServico'),
                'eServico' => $this->input->post('eServico'),
                'dServico' => $this->input->post('dServico'),
                'vServico' => $this->input->post('vServico'),

                'aOs' => $this->input->post('aOs'),
                'eOs' => $this->input->post('eOs'),
                'dOs' => $this->input->post('dOs'),
                'vOs' => $this->input->post('vOs'),

                'aVenda' => $this->input->post('aVenda'),
                'eVenda' => $this->input->post('eVenda'),
                'dVenda' => $this->input->post('dVenda'),
                'vVenda' => $this->input->post('vVenda'),

                'aGarantia' => $this->input->post('aGarantia'),
                'eGarantia' => $this->input->post('eGarantia'),
                'dGarantia' => $this->input->post('dGarantia'),
                'vGarantia' => $this->input->post('vGarantia'),

                'aArquivo' => $this->input->post('aArquivo'),
                'eArquivo' => $this->input->post('eArquivo'),
                'dArquivo' => $this->input->post('dArquivo'),
                'vArquivo' => $this->input->post('vArquivo'),

                'aPagamento' => $this->input->post('aPagamento'),
                'ePagamento' => $this->input->post('ePagamento'),
                'dPagamento' => $this->input->post('dPagamento'),
                'vPagamento' => $this->input->post('vPagamento'),

                'aLancamento' => $this->input->post('aLancamento'),
                'eLancamento' => $this->input->post('eLancamento'),
                'dLancamento' => $this->input->post('dLancamento'),
                'vLancamento' => $this->input->post('vLancamento'),

                'cUsuario' => $this->input->post('cUsuario'),
                'cEmitente' => $this->input->post('cEmitente'),
                'cPermissao' => $this->input->post('cPermissao'),
                'cBackup' => $this->input->post('cBackup'),
                'cAuditoria' => $this->input->post('cAuditoria'),
                'cEmail' => $this->input->post('cEmail'),
                'cSistema' => $this->input->post('cSistema'),

                'rCliente' => $this->input->post('rCliente'),
                'rProduto' => $this->input->post('rProduto'),
                'rServico' => $this->input->post('rServico'),
                'rOs' => $this->input->post('rOs'),
                'rVenda' => $this->input->post('rVenda'),
                'rFinanceiro' => $this->input->post('rFinanceiro'),
                'rNfe' => $this->input->post('rNfe'),

                'aCobranca' => $this->input->post('aCobranca'),
                'eCobranca' => $this->input->post('eCobranca'),
                'dCobranca' => $this->input->post('dCobranca'),
                'vCobranca' => $this->input->post('vCobranca'),

                'vNfe' => $this->input->post('vNfe'),
                'eNfe' => $this->input->post('eNfe'),

                'vNcm' => $this->input->post('vNcm'),
                'aNcm' => $this->input->post('aNcm'),
                'eNcm' => $this->input->post('eNcm'),
                'dNcm' => $this->input->post('dNcm'),

                'vTributacao' => $this->input->post('vTributacao'),
                'vTributacaoProduto' => $this->input->post('vTributacaoProduto'),
                'aTributacaoProduto' => $this->input->post('aTributacaoProduto'),
                'eTributacaoProduto' => $this->input->post('eTributacaoProduto'),
                'dTributacaoProduto' => $this->input->post('dTributacaoProduto'),

                // Permissões de Operação Comercial
                'vOperacaoComercial' => $this->input->post('vOperacaoComercial'),
                'aOperacaoComercial' => $this->input->post('aOperacaoComercial'),
                'eOperacaoComercial' => $this->input->post('eOperacaoComercial'),
                'dOperacaoComercial' => $this->input->post('dOperacaoComercial'),

                // Permissões de Classificação Fiscal
                'vClassificacaoFiscal' => $this->input->post('vClassificacaoFiscal'),
                'aClassificacaoFiscal' => $this->input->post('aClassificacaoFiscal'),
                'eClassificacaoFiscal' => $this->input->post('eClassificacaoFiscal'),
                'dClassificacaoFiscal' => $this->input->post('dClassificacaoFiscal'),

                // Permissões de Alíquotas
                'vAliquota' => $this->input->post('vAliquota'),
                'aAliquota' => $this->input->post('aAliquota'),
                'eAliquota' => $this->input->post('eAliquota'),
                'dAliquota' => $this->input->post('dAliquota'),

                // Permissões de Faturamento de Entrada
                'vFaturamentoEntrada' => $this->input->post('vFaturamentoEntrada'),
                'aFaturamentoEntrada' => $this->input->post('aFaturamentoEntrada'),
                'eFaturamentoEntrada' => $this->input->post('eFaturamentoEntrada'),
                'dFaturamentoEntrada' => $this->input->post('dFaturamentoEntrada'),

                // Permissões de NFECom
                'vNfecom' => $this->input->post('vNfecom'),
                'aNfecom' => $this->input->post('aNfecom'),
                'eNfecom' => $this->input->post('eNfecom'),
                'dNfecom' => $this->input->post('dNfecom'),

                // Certificados Digitais
                'vCertificado' => $this->input->post('vCertificado'),
                'aCertificado' => $this->input->post('aCertificado'),
                'dCertificado' => $this->input->post('dCertificado'),

                // Configurações Fiscais
                'vConfigFiscal' => $this->input->post('vConfigFiscal'),
                'eConfigFiscal' => $this->input->post('eConfigFiscal'),
            ];
            $permissoes = serialize($permissoes);

            $data = [
                'nome' => $nomePermissao,
                'data' => $cadastro,
                'permissoes' => $permissoes,
                'situacao' => $situacao,
            ];

            if ($this->permissoes_model->add('permissoes', $data) == true) {
                $this->session->set_flashdata('success', 'Permissão adicionada com sucesso!');
                log_info('Adicionou uma permissão');
                redirect(site_url('permissoes/adicionar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'permissoes/adicionarPermissao';

        return $this->layout();
    }

    public function editar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $nomePermissao = $this->input->post('nome');
            $situacao = $this->input->post('situacao');
            $permissoes = [
                'aCliente' => $this->input->post('aCliente'),
                'eCliente' => $this->input->post('eCliente'),
                'dCliente' => $this->input->post('dCliente'),
                'vCliente' => $this->input->post('vCliente'),

                // Pessoas
                'aPessoa' => $this->input->post('aPessoa'),
                'ePessoa' => $this->input->post('ePessoa'),
                'dPessoa' => $this->input->post('dPessoa'),
                'vPessoa' => $this->input->post('vPessoa'),

                // Contratos
                'aContrato' => $this->input->post('aContrato'),
                'eContrato' => $this->input->post('eContrato'),
                'dContrato' => $this->input->post('dContrato'),
                'vContrato' => $this->input->post('vContrato'),

                'aTipoCliente' => $this->input->post('aTipoCliente'),
                'eTipoCliente' => $this->input->post('eTipoCliente'),
                'dTipoCliente' => $this->input->post('dTipoCliente'),
                'vTipoCliente' => $this->input->post('vTipoCliente'),

                // Empresas
                'aEmpresa' => $this->input->post('aEmpresa'),
                'eEmpresa' => $this->input->post('eEmpresa'),
                'dEmpresa' => $this->input->post('dEmpresa'),
                'vEmpresa' => $this->input->post('vEmpresa'),

                'aProduto' => $this->input->post('aProduto'),
                'eProduto' => $this->input->post('eProduto'),
                'dProduto' => $this->input->post('dProduto'),
                'vProduto' => $this->input->post('vProduto'),

                'aServico' => $this->input->post('aServico'),
                'eServico' => $this->input->post('eServico'),
                'dServico' => $this->input->post('dServico'),
                'vServico' => $this->input->post('vServico'),

                'aOs' => $this->input->post('aOs'),
                'eOs' => $this->input->post('eOs'),
                'dOs' => $this->input->post('dOs'),
                'vOs' => $this->input->post('vOs'),

                'aVenda' => $this->input->post('aVenda'),
                'eVenda' => $this->input->post('eVenda'),
                'dVenda' => $this->input->post('dVenda'),
                'vVenda' => $this->input->post('vVenda'),

                'aGarantia' => $this->input->post('aGarantia'),
                'eGarantia' => $this->input->post('eGarantia'),
                'dGarantia' => $this->input->post('dGarantia'),
                'vGarantia' => $this->input->post('vGarantia'),

                'aArquivo' => $this->input->post('aArquivo'),
                'eArquivo' => $this->input->post('eArquivo'),
                'dArquivo' => $this->input->post('dArquivo'),
                'vArquivo' => $this->input->post('vArquivo'),

                'aPagamento' => $this->input->post('aPagamento'),
                'ePagamento' => $this->input->post('ePagamento'),
                'dPagamento' => $this->input->post('dPagamento'),
                'vPagamento' => $this->input->post('vPagamento'),

                'aLancamento' => $this->input->post('aLancamento'),
                'eLancamento' => $this->input->post('eLancamento'),
                'dLancamento' => $this->input->post('dLancamento'),
                'vLancamento' => $this->input->post('vLancamento'),

                'cUsuario' => $this->input->post('cUsuario'),
                'cEmitente' => $this->input->post('cEmitente'),
                'cPermissao' => $this->input->post('cPermissao'),
                'cBackup' => $this->input->post('cBackup'),
                'cAuditoria' => $this->input->post('cAuditoria'),
                'cEmail' => $this->input->post('cEmail'),
                'cSistema' => $this->input->post('cSistema'),

                'rCliente' => $this->input->post('rCliente'),
                'rProduto' => $this->input->post('rProduto'),
                'rServico' => $this->input->post('rServico'),
                'rOs' => $this->input->post('rOs'),
                'rVenda' => $this->input->post('rVenda'),
                'rFinanceiro' => $this->input->post('rFinanceiro'),
                'rNfe' => $this->input->post('rNfe'),

                'aCobranca' => $this->input->post('aCobranca'),
                'eCobranca' => $this->input->post('eCobranca'),
                'dCobranca' => $this->input->post('dCobranca'),
                'vCobranca' => $this->input->post('vCobranca'),

                'vNfe' => $this->input->post('vNfe'),
                'eNfe' => $this->input->post('eNfe'),

                'vNcm' => $this->input->post('vNcm'),
                'aNcm' => $this->input->post('aNcm'),
                'eNcm' => $this->input->post('eNcm'),
                'dNcm' => $this->input->post('dNcm'),

                'vTributacao' => $this->input->post('vTributacao'),
                'vTributacaoProduto' => $this->input->post('vTributacaoProduto'),
                'aTributacaoProduto' => $this->input->post('aTributacaoProduto'),
                'eTributacaoProduto' => $this->input->post('eTributacaoProduto'),
                'dTributacaoProduto' => $this->input->post('dTributacaoProduto'),

                // Permissões de Operação Comercial
                'vOperacaoComercial' => $this->input->post('vOperacaoComercial'),
                'aOperacaoComercial' => $this->input->post('aOperacaoComercial'),
                'eOperacaoComercial' => $this->input->post('eOperacaoComercial'),
                'dOperacaoComercial' => $this->input->post('dOperacaoComercial'),

                // Permissões de Classificação Fiscal
                'vClassificacaoFiscal' => $this->input->post('vClassificacaoFiscal'),
                'aClassificacaoFiscal' => $this->input->post('aClassificacaoFiscal'),
                'eClassificacaoFiscal' => $this->input->post('eClassificacaoFiscal'),
                'dClassificacaoFiscal' => $this->input->post('dClassificacaoFiscal'),

                // Permissões de Alíquotas
                'vAliquota' => $this->input->post('vAliquota'),
                'aAliquota' => $this->input->post('aAliquota'),
                'eAliquota' => $this->input->post('eAliquota'),
                'dAliquota' => $this->input->post('dAliquota'),

                // Permissões de Faturamento de Entrada
                'vFaturamentoEntrada' => $this->input->post('vFaturamentoEntrada'),
                'aFaturamentoEntrada' => $this->input->post('aFaturamentoEntrada'),
                'eFaturamentoEntrada' => $this->input->post('eFaturamentoEntrada'),
                'dFaturamentoEntrada' => $this->input->post('dFaturamentoEntrada'),

                // Permissões de NFECom
                'vNfecom' => $this->input->post('vNfecom'),
                'aNfecom' => $this->input->post('aNfecom'),
                'eNfecom' => $this->input->post('eNfecom'),
                'dNfecom' => $this->input->post('dNfecom'),

                // Certificados Digitais
                'vCertificado' => $this->input->post('vCertificado'),
                'aCertificado' => $this->input->post('aCertificado'),
                'dCertificado' => $this->input->post('dCertificado'),

                // Configurações Fiscais
                'vConfigFiscal' => $this->input->post('vConfigFiscal'),
                'eConfigFiscal' => $this->input->post('eConfigFiscal'),
            ];
            $permissoes = serialize($permissoes);

            $data = [
                'nome' => $nomePermissao,
                'permissoes' => $permissoes,
                'situacao' => $situacao,
            ];

            if ($this->permissoes_model->edit('permissoes', $data, 'idPermissao', $this->input->post('idPermissao')) == true) {
                $this->session->set_flashdata('success', 'Permissão editada com sucesso!');
                log_info('Alterou uma permissão. ID: ' . $this->input->post('idPermissao'));
                redirect(site_url('permissoes/editar/') . $this->input->post('idPermissao'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['result'] = $this->permissoes_model->getById($this->uri->segment(3));

        $this->data['view'] = 'permissoes/editarPermissao';

        return $this->layout();
    }

    public function desativar()
    {
        $id = $this->input->post('id');
        if (!$id) {
            $this->session->set_flashdata('error', 'Erro ao tentar desativar permissão.');
            redirect(site_url('permissoes/gerenciar/'));
        }
        $data = [
            'situacao' => false,
        ];
        if ($this->permissoes_model->edit('permissoes', $data, 'idPermissao', $id)) {
            log_info('Desativou uma permissão. ID: ' . $id);
            $this->session->set_flashdata('success', 'Permissão desativada com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao desativar permissão!');
        }

        redirect(site_url('permissoes/gerenciar/'));
    }

    public function exportarXml()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para exportar XMLs de NF-e.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('data_inicio') ?: date('Y-m-d', strtotime('-7 days'));
        $dataFinal = $this->input->get('data_fim') ?: date('Y-m-d');
        $chave = $this->input->get('chave');
        $cliente = $this->input->get('cliente');
        $numero = $this->input->get('numero');
        $modelo = $this->input->get('modelo');
        $status = $this->input->get('status');

        $this->db->select('nfe_emitidas.*, clientes.nomeCliente');
        $this->db->from('nfe_emitidas');
        $this->db->join('vendas', 'vendas.idVendas = nfe_emitidas.venda_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
        $this->db->where('nfe_emitidas.created_at >=', $dataInicial . ' 00:00:00');
        $this->db->where('nfe_emitidas.created_at <=', $dataFinal . ' 23:59:59');
        $this->db->where('nfe_emitidas.status !=', 0); // Exclui NFes rejeitadas

        if ($chave) {
            $this->db->like('nfe_emitidas.chave_nfe', $chave);
        }
        if ($cliente) {
            $this->db->like('clientes.nomeCliente', $cliente);
        }
        if ($numero) {
            $this->db->like('nfe_emitidas.numero_nfe', $numero);
        }
        if ($modelo) {
            $this->db->where('nfe_emitidas.modelo', $modelo);
        }
        if ($status) {
            $this->db->where('nfe_emitidas.status', $status);
        }

        $nfe = $this->db->get()->result();

        if (empty($nfe)) {
            $this->session->set_flashdata('error', 'Nenhuma NFe encontrada para exportar.');
            redirect('nfe/gerenciar');
        }

        // Cria um arquivo ZIP temporário
        $zip = new ZipArchive();
        $zipName = 'nfe_xml_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $zipName;

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($nfe as $n) {
                if (!empty($n->xml)) {
                    // Gera um nome de arquivo baseado na chave da NFe
                    $fileName = $n->chave_nfe . '.xml';
                    $zip->addFromString($fileName, $n->xml);
                }
            }
            $zip->close();

            // Força o download do arquivo ZIP
            $this->load->helper('download');
            $data = file_get_contents($zipPath);
            force_download($zipName, $data);

            // Remove o arquivo ZIP temporário
            unlink($zipPath);
        } else {
            $this->session->set_flashdata('error', 'Erro ao criar arquivo ZIP.');
            redirect('nfe/gerenciar');
        }
    }
}

/* End of file permissoes.php */
/* Location: ./application/controllers/permissoes.php */

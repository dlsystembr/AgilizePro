<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ConfiguracoesFiscais extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ConfiguracoesFiscais_model');
        $this->load->model('Certificados_model');
        $this->load->model('Empresas_model');
        $this->data['menuConfigFiscais'] = 'configuracoes_fiscais';
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vConfigFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar configurações fiscais.');
            redirect(base_url());
        }

        // Busca primeira empresa
        $empresa = $this->db->limit(1)->get('empresas')->row();

        if (!$empresa) {
            $this->session->set_flashdata('error', 'Nenhuma empresa cadastrada.');
            redirect(base_url());
        }

        $this->data['empresa'] = $empresa;
        $this->data['configuracoes'] = $this->ConfiguracoesFiscais_model->get($empresa->EMP_ID);
        $this->data['tiposDocumento'] = $this->ConfiguracoesFiscais_model->getTiposDocumento();

        $this->data['view'] = 'configuracoes_fiscais/index';
        return $this->layout();
    }

    public function configurar($tipoDocumento = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eConfigFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar configurações fiscais.');
            redirect(base_url());
        }

        if (!$tipoDocumento) {
            $this->session->set_flashdata('error', 'Tipo de documento não especificado.');
            redirect(site_url('configuracoesfiscais'));
        }

        $tipoDocumento = strtoupper($tipoDocumento);
        $tiposValidos = array_keys($this->ConfiguracoesFiscais_model->getTiposDocumento());

        if (!in_array($tipoDocumento, $tiposValidos)) {
            $this->session->set_flashdata('error', 'Tipo de documento inválido.');
            redirect(site_url('configuracoesfiscais'));
        }

        // Busca empresa
        $empresa = $this->db->limit(1)->get('empresas')->row();

        if (!$empresa) {
            $this->session->set_flashdata('error', 'Nenhuma empresa cadastrada.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Busca configuração existente
        $config = $this->ConfiguracoesFiscais_model->getByTipo($empresa->EMP_ID, $tipoDocumento);

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('CFG_AMBIENTE', 'Ambiente', 'required');
            $this->form_validation->set_rules('CFG_SERIE', 'Série', 'required|trim');
            $this->form_validation->set_rules('CFG_NUMERO_ATUAL', 'Número Atual', 'required|numeric');

            if ($this->form_validation->run() == false) {
                $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
            } else {
                $data = [
                    'CER_ID' => $this->input->post('CER_ID') ?: null,
                    'CFG_AMBIENTE' => $this->input->post('CFG_AMBIENTE'),
                    'CFG_SERIE' => $this->input->post('CFG_SERIE'),
                    'CFG_NUMERO_ATUAL' => $this->input->post('CFG_NUMERO_ATUAL'),
                    'CFG_FORMATO_IMPRESSAO' => $this->input->post('CFG_FORMATO_IMPRESSAO') ?: 'A4',
                    'CFG_ATIVO' => $this->input->post('CFG_ATIVO') !== null ? (int) $this->input->post('CFG_ATIVO') : 1,
                ];

                // Campos específicos por tipo
                if ($tipoDocumento === 'NFCE' || $tipoDocumento === 'NFCOM') {
                    $data['CFG_CSC_ID'] = $this->input->post('CFG_CSC_ID');
                    $data['CFG_CSC_TOKEN'] = $this->input->post('CFG_CSC_TOKEN');
                }

                if ($tipoDocumento === 'NFSE') {
                    $data['CFG_ALIQUOTA_ISS'] = $this->input->post('CFG_ALIQUOTA_ISS');
                    $data['CFG_REGIME_ESPECIAL'] = $this->input->post('CFG_REGIME_ESPECIAL');
                }

                if ($this->ConfiguracoesFiscais_model->salvar($empresa->EMP_ID, $tipoDocumento, $data)) {
                    $this->session->set_flashdata('success', 'Configuração salva com sucesso!');
                    log_info('Configurou ' . $tipoDocumento);
                    redirect(site_url('configuracoesfiscais'));
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Erro ao salvar configuração.</div>';
                }
            }
        }

        $this->data['empresa'] = $empresa;
        $this->data['config'] = $config;
        $this->data['tipoDocumento'] = $tipoDocumento;
        $this->data['tipoNome'] = $this->ConfiguracoesFiscais_model->getTiposDocumento()[$tipoDocumento];
        $this->data['certificados'] = $this->Certificados_model->getCertificadosValidos($empresa->EMP_ID);

        $this->data['view'] = 'configuracoes_fiscais/configurar';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eConfigFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir configuração.');
            redirect(site_url('configuracoesfiscais'));
        }

        $this->ConfiguracoesFiscais_model->delete($id);
        log_info('Removeu uma configuração fiscal. ID ' . $id);

        $this->session->set_flashdata('success', 'Configuração excluída com sucesso!');
        redirect(site_url('configuracoesfiscais'));
    }
}

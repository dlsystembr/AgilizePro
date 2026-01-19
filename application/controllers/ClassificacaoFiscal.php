<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ClassificacaoFiscal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vClassificacaoFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar o módulo de Classificação Fiscal.');
            redirect(base_url());
        }

        $this->load->model('ClassificacaoFiscal_model');
        $this->load->model('OperacaoComercial_model');
        $this->load->helper('form');
        $this->data['menuConfiguracoes'] = 'Configurações';
        $this->data['menuClassificacaoFiscal'] = 'Classificação Fiscal';
    }

    public function index()
    {
        $this->data['view'] = 'classificacaofiscal/classificacaofiscal';
        $this->data['results'] = $this->ClassificacaoFiscal_model->get();

        // Get tax regime from configuration
        $this->load->model('Mapos_model');
        $configuracao = $this->Mapos_model->getConfiguracao();
        $this->data['regime_tributario'] = $configuracao['regime_tributario'];

        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aClassificacaoFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar classificação fiscal.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('operacao_comercial_id', 'Operação Comercial', 'required|trim');
        $this->form_validation->set_rules('natureza_contribuinte', 'Natureza do Contribuinte', 'required|trim');
        $this->form_validation->set_rules('cfop', 'CFOP', 'required|trim');
        $this->form_validation->set_rules('destinacao', 'Destinação', 'required|trim');
        $this->form_validation->set_rules('objetivo_comercial', 'Objetivo Comercial', 'required|trim');
        $this->form_validation->set_rules('tipo_icms', 'Tipo ICMS', 'required|in_list[normal,st]');

        // Get tax regime from configuration
        $this->load->model('Mapos_model');
        $configuracao = $this->Mapos_model->getConfiguracao();
        $this->data['regime_tributario'] = $configuracao['regime_tributario'];

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'operacao_comercial_id' => $this->input->post('operacao_comercial_id'),
                'natureza_contribuinte' => $this->input->post('natureza_contribuinte'),
                'cfop' => $this->input->post('cfop'),
                'destinacao' => $this->input->post('destinacao'),
                'objetivo_comercial' => $this->input->post('objetivo_comercial'),
                'tipo_icms' => $this->input->post('tipo_icms'),
                'mensagem_fiscal' => $this->input->post('mensagem_fiscal'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Add CST or CSOSN based on tax regime
            if ($this->data['regime_tributario'] === 'Simples Nacional') {
                $this->form_validation->set_rules('csosn', 'CSOSN', 'required|trim');
                $data['csosn'] = $this->input->post('csosn');
                $data['cst'] = null;
            } else {
                $this->form_validation->set_rules('cst', 'CST', 'required|trim');
                $data['cst'] = $this->input->post('cst');
                $data['csosn'] = null;
            }

            if ($this->ClassificacaoFiscal_model->add('classificacao_fiscal', $data)) {
                $this->session->set_flashdata('success', 'Classificação Fiscal adicionada com sucesso!');
                redirect(site_url('classificacaofiscal'));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar adicionar a classificação fiscal.</div>';
            }
        }

        $this->data['operacoes'] = $this->OperacaoComercial_model->getAll();
        $this->data['view'] = 'classificacaofiscal/adicionarClassificacaoFiscal';
        return $this->layout();
    }

    public function editar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eClassificacaoFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar classificação fiscal.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('operacao_comercial_id', 'Operação Comercial', 'required|trim');
        $this->form_validation->set_rules('natureza_contribuinte', 'Natureza do Contribuinte', 'required|trim');
        $this->form_validation->set_rules('cfop', 'CFOP', 'required|trim');
        $this->form_validation->set_rules('destinacao', 'Destinação', 'required|trim');
        $this->form_validation->set_rules('objetivo_comercial', 'Objetivo Comercial', 'required|trim');
        $this->form_validation->set_rules('tipo_icms', 'Tipo ICMS', 'required|in_list[normal,st]');

        // Get tax regime from configuration
        $this->load->model('Mapos_model');
        $configuracao = $this->Mapos_model->getConfiguracao();
        $this->data['regime_tributario'] = $configuracao['regime_tributario'];

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'operacao_comercial_id' => $this->input->post('operacao_comercial_id'),
                'natureza_contribuinte' => $this->input->post('natureza_contribuinte'),
                'cfop' => $this->input->post('cfop'),
                'destinacao' => $this->input->post('destinacao'),
                'objetivo_comercial' => $this->input->post('objetivo_comercial'),
                'tipo_icms' => $this->input->post('tipo_icms'),
                'mensagem_fiscal' => $this->input->post('mensagem_fiscal'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Add CST or CSOSN based on tax regime
            if ($this->data['regime_tributario'] === 'Simples Nacional') {
                $this->form_validation->set_rules('csosn', 'CSOSN', 'required|trim');
                $data['csosn'] = $this->input->post('csosn');
                $data['cst'] = null;
            } else {
                $this->form_validation->set_rules('cst', 'CST', 'required|trim');
                $data['cst'] = $this->input->post('cst');
                $data['csosn'] = null;
            }

            if ($this->ClassificacaoFiscal_model->edit('classificacao_fiscal', $data, 'id', $id)) {
                $this->session->set_flashdata('success', 'Classificação Fiscal editada com sucesso!');
                redirect(site_url('classificacaofiscal'));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar editar a classificação fiscal.</div>';
            }
        }

        $this->data['result'] = $this->ClassificacaoFiscal_model->getById($id);
        $this->data['operacoes'] = $this->OperacaoComercial_model->getAll();
        $this->data['view'] = 'classificacaofiscal/editarClassificacaoFiscal';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dClassificacaoFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir classificação fiscal.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir classificação fiscal.');
            redirect(site_url('classificacaofiscal'));
        }

        if ($this->ClassificacaoFiscal_model->delete('classificacao_fiscal', 'id', $id)) {
            $this->session->set_flashdata('success', 'Classificação Fiscal excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir classificação fiscal.');
        }
        redirect(site_url('classificacaofiscal'));
    }

    public function clonar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aClassificacaoFiscal')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para clonar classificação fiscal.');
            redirect(base_url());
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID inválido para clonagem.');
            return redirect(site_url('classificacaofiscal'));
        }

        $orig = $this->ClassificacaoFiscal_model->getById($id);
        if (!$orig) {
            $this->session->set_flashdata('error', 'Classificação Fiscal não encontrada.');
            return redirect(site_url('classificacaofiscal'));
        }

        // Preenche o formulário de adição com os dados clonados e permite cancelar ou salvar
        $this->load->model('Mapos_model');
        $configuracao = $this->Mapos_model->getConfiguracao();
        $this->data['regime_tributario'] = $configuracao['regime_tributario'];

        $this->data['custom_error'] = '';
        $this->data['operacoes'] = $this->OperacaoComercial_model->getAll();
        $this->data['prefill'] = [
            'operacao_comercial_id' => $orig->operacao_comercial_id,
            'natureza_contribuinte' => $orig->natureza_contribuinte,
            'cfop' => $orig->cfop,
            'destinacao' => $orig->destinacao,
            'objetivo_comercial' => $orig->objetivo_comercial,
            'mensagem_fiscal' => $orig->mensagem_fiscal,
            'cst' => isset($orig->cst) ? $orig->cst : '',
            'csosn' => isset($orig->csosn) ? $orig->csosn : ''
        ];
        $this->data['view'] = 'classificacaofiscal/adicionarClassificacaoFiscal';
        return $this->layout();
    }
}
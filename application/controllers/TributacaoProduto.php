<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class TributacaoProduto extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vTributacaoProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar o módulo de Tributação de Produtos.');
            redirect(base_url());
        }
        
        $this->load->model('tributacaoproduto_model');
        $this->data['menuConfiguracoes'] = 'Configurações';
        $this->data['menuTributacaoProduto'] = 'Tributação Produto';
    }
    
    public function index()
    {
        $this->data['view'] = 'tributacaoproduto/tributacaoproduto';
        $this->data['results'] = $this->tributacaoproduto_model->get();
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aTributacaoProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar tributação de produtos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome_configuracao', 'Nome da Configuração', 'required|trim');
        $this->form_validation->set_rules('cst_ipi_saida', 'CST IPI Saída', 'required|trim');
        $this->form_validation->set_rules('aliq_ipi_saida', 'Alíquota IPI Saída', 'required|trim');
        $this->form_validation->set_rules('cst_pis_saida', 'CST PIS Saída', 'required|trim');
        $this->form_validation->set_rules('aliq_pis_saida', 'Alíquota PIS Saída', 'required|trim');
        $this->form_validation->set_rules('cst_cofins_saida', 'CST COFINS Saída', 'required|trim');
        $this->form_validation->set_rules('aliq_cofins_saida', 'Alíquota COFINS Saída', 'required|trim');
        $this->form_validation->set_rules('regime_fiscal_tributario', 'Regime Fiscal Tributário', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'nome_configuracao' => $this->input->post('nome_configuracao'),
                'cst_ipi_saida' => $this->input->post('cst_ipi_saida'),
                'aliq_ipi_saida' => $this->input->post('aliq_ipi_saida'),
                'cst_pis_saida' => $this->input->post('cst_pis_saida'),
                'aliq_pis_saida' => $this->input->post('aliq_pis_saida'),
                'cst_cofins_saida' => $this->input->post('cst_cofins_saida'),
                'aliq_cofins_saida' => $this->input->post('aliq_cofins_saida'),
                'regime_fiscal_tributario' => $this->input->post('regime_fiscal_tributario'),
                'aliq_red_icms' => $this->input->post('aliq_red_icms'),
                'aliq_iva' => $this->input->post('aliq_iva'),
                'aliq_rd_icms_st' => $this->input->post('aliq_rd_icms_st')
            );

            if ($this->tributacaoproduto_model->add('tributacao_produto', $data) == TRUE) {
                $this->session->set_flashdata('success', 'Tributação de produto adicionada com sucesso!');
                redirect(base_url() . 'index.php/tributacaoproduto/');
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro.</div>';
            }
        }
        
        $this->data['view'] = 'tributacaoproduto/adicionarTributacao';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eTributacaoProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar tributação de produtos.');
            redirect(base_url());
        }
        
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome_configuracao', 'Nome da Configuração', 'required|trim');
        $this->form_validation->set_rules('cst_ipi_saida', 'CST IPI Saída', 'required|trim');
        $this->form_validation->set_rules('aliq_ipi_saida', 'Alíquota IPI Saída', 'required|trim');
        $this->form_validation->set_rules('cst_pis_saida', 'CST PIS Saída', 'required|trim');
        $this->form_validation->set_rules('aliq_pis_saida', 'Alíquota PIS Saída', 'required|trim');
        $this->form_validation->set_rules('cst_cofins_saida', 'CST COFINS Saída', 'required|trim');
        $this->form_validation->set_rules('aliq_cofins_saida', 'Alíquota COFINS Saída', 'required|trim');
        $this->form_validation->set_rules('regime_fiscal_tributario', 'Regime Fiscal Tributário', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'nome_configuracao' => $this->input->post('nome_configuracao'),
                'cst_ipi_saida' => $this->input->post('cst_ipi_saida'),
                'aliq_ipi_saida' => $this->input->post('aliq_ipi_saida'),
                'cst_pis_saida' => $this->input->post('cst_pis_saida'),
                'aliq_pis_saida' => $this->input->post('aliq_pis_saida'),
                'cst_cofins_saida' => $this->input->post('cst_cofins_saida'),
                'aliq_cofins_saida' => $this->input->post('aliq_cofins_saida'),
                'regime_fiscal_tributario' => $this->input->post('regime_fiscal_tributario'),
                'aliq_red_icms' => $this->input->post('aliq_red_icms'),
                'aliq_iva' => $this->input->post('aliq_iva'),
                'aliq_rd_icms_st' => $this->input->post('aliq_rd_icms_st')
            );

            if ($this->tributacaoproduto_model->edit('tributacao_produto', $data, 'id', $this->input->post('id')) == TRUE) {
                $this->session->set_flashdata('success', 'Tributação de produto editada com sucesso!');
                redirect(base_url() . 'index.php/tributacaoproduto/');
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro.</div>';
            }
        }

        $this->data['result'] = $this->tributacaoproduto_model->getById($this->uri->segment(3));
        $this->data['view'] = 'tributacaoproduto/editarTributacao';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dTributacaoProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir tributação de produtos.');
            redirect(base_url());
        }
        
        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir tributação de produto.');
            redirect(base_url() . 'index.php/tributacaoproduto/');
        }

        if ($this->tributacaoproduto_model->delete('tributacao_produto', 'id', $id)) {
            $this->session->set_flashdata('success', 'Tributação de produto excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir tributação de produto.');
        }
        redirect(base_url() . 'index.php/tributacaoproduto/');
    }
} 
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class OperacaoComercial extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('OperacaoComercial_model');
        $this->load->model('Mapos_model');
        $this->data['menuConfiguracoes'] = 'Configurações';
        $this->data['menuOperacaoComercial'] = 'Operação Comercial';
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOperacaoComercial')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar operações comerciais.');
            redirect(base_url());
        }

        $this->data['results'] = $this->OperacaoComercial_model->get();
        $this->data['regime_tributario'] = $this->Mapos_model->getConfiguracao()['regime_tributario'];
        $this->data['view'] = 'operacaocomercial/operacaocomercial';
        return $this->layout();
    }

    public function adicionar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome da Operação', 'required|trim');
        $this->form_validation->set_rules('mensagem_nota_fiscal', 'Mensagem Nota Fiscal', 'trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'nome' => $this->input->post('nome'),
                'mensagem_nota' => $this->input->post('mensagem_nota_fiscal')
            );
            if ($this->OperacaoComercial_model->add($data)) {
                $this->session->set_flashdata('success', 'Operação Comercial adicionada com sucesso!');
                redirect(base_url('index.php/operacaocomercial'));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro.</div>';
            }
        }
        $this->data['regime_tributario'] = $this->Mapos_model->getConfiguracao()['regime_tributario'];
        $this->data['view'] = 'operacaocomercial/adicionarOperacao';
        return $this->layout();
    }

    public function editar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Operação não encontrada.');
            redirect(base_url('index.php/operacaocomercial'));
        }

        $result = $this->OperacaoComercial_model->getById($id);
        if (!$result) {
            $this->session->set_flashdata('error', 'Operação não encontrada.');
            redirect(base_url('index.php/operacaocomercial'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome da Operação', 'required|trim');
        $this->form_validation->set_rules('mensagem_nota_fiscal', 'Mensagem Nota Fiscal', 'trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'nome' => $this->input->post('nome'),
                'mensagem_nota' => $this->input->post('mensagem_nota_fiscal')
            );
            if ($this->OperacaoComercial_model->update($id, $data)) {
                $this->session->set_flashdata('success', 'Operação Comercial editada com sucesso!');
                redirect(base_url('index.php/operacaocomercial'));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro.</div>';
            }
        }
        $this->data['result'] = $result;
        $this->data['regime_tributario'] = $this->Mapos_model->getConfiguracao()['regime_tributario'];
        $this->data['view'] = 'operacaocomercial/editarOperacao';
        return $this->layout();
    }

    public function excluir($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Operação não encontrada.');
            redirect(base_url('index.php/operacaocomercial'));
        }
        if ($this->OperacaoComercial_model->delete($id)) {
            $this->session->set_flashdata('success', 'Operação Comercial excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir operação comercial.');
        }
        redirect(base_url('index.php/operacaocomercial'));
    }
} 
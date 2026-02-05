<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tela de parâmetros do sistema (por empresa).
 * Usa a tabela parametros (prm_*). Requer emp_id na sessão.
 */
class Parametros extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Parametros_model', 'parametros_model');
    }

    /**
     * Lista parâmetros agrupados por grupo e exibe o formulário.
     */
    public function index()
    {
        $emp_id = (int) $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada. Selecione a empresa no topo da página.');
            redirect(base_url());
        }

        if (!$this->session->userdata('is_super') && !$this->permission->checkPermission($this->session->userdata('permissao'), 'vConfiguracao')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar os parâmetros do sistema.');
            redirect(base_url());
        }

        if ($this->input->method() === 'post') {
            $this->salvar();
            return;
        }

        $this->data['menuParametros'] = true;
        $this->data['parametros_agrupados'] = $this->parametros_model->getTodosPorEmpresaAgrupados($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->data['view'] = 'parametros/index';
        $this->layout();
    }

    /**
     * Salva os parâmetros enviados via POST (param[prm_nome] = valor).
     */
    public function salvar()
    {
        $emp_id = (int) $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada.');
            redirect(base_url());
        }

        if (!$this->session->userdata('is_super') && !$this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para alterar os parâmetros.');
            redirect(base_url('index.php/parametros'));
        }

        $param = $this->input->post('param');
        if (!is_array($param)) {
            $param = [];
        }

        if ($this->parametros_model->salvarLote($emp_id, $param)) {
            $this->session->set_flashdata('success', 'Parâmetros salvos com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar os parâmetros.');
        }
        redirect(base_url('index.php/parametros'));
    }
}

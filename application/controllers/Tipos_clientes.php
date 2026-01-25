<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tipos_clientes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tipos_clientes_model');
        $this->data['menuTiposClientes'] = 'configuracoes';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vTipoCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar tipos de clientes.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = base_url() . 'index.php/tipos_clientes/gerenciar/';
        $this->data['configuration']['total_rows'] = $this->Tipos_clientes_model->count('TIPOS_CLIENTES');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->Tipos_clientes_model->get('TIPOS_CLIENTES', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'tipos_clientes/gerenciarTiposClientes';
        $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aTipoCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar tipos de clientes.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('tipos_clientes') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'tpc_nome' => $this->input->post('nome'),
                'tpc_codigo_cliente' => $this->input->post('codigoCliente') ?: null,
                'tpc_data_cadastro' => date('Y-m-d H:i:s'),
            ];

            if ($this->Tipos_clientes_model->add('TIPOS_CLIENTES', $data) == true) {
                $this->session->set_flashdata('success', 'Tipo de Cliente adicionado com sucesso!');
                log_info('Adicionou um tipo de cliente.');
                redirect(base_url() . 'index.php/tipos_clientes/gerenciar');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'tipos_clientes/adicionarTipoCliente';
        $this->layout();
    }

    public function editar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eTipoCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar tipos de clientes.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('tipos_clientes') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'tpc_nome' => $this->input->post('nome'),
                'tpc_codigo_cliente' => $this->input->post('codigoCliente') ?: null,
            ];

            if ($this->Tipos_clientes_model->edit('TIPOS_CLIENTES', $data, 'tpc_id', $this->input->post('id')) == true) {
                $this->session->set_flashdata('success', 'Tipo de Cliente editado com sucesso!');
                log_info('Editou um tipo de cliente. ID: ' . $this->input->post('id'));
                redirect(base_url() . 'index.php/tipos_clientes/gerenciar');
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['result'] = $this->Tipos_clientes_model->getById($this->uri->segment(3));
        $this->data['view'] = 'tipos_clientes/editarTipoCliente';
        $this->layout();
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dTipoCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir tipos de clientes.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir tipo de cliente.');
            redirect(base_url() . 'index.php/tipos_clientes/gerenciar');
        }

        $this->Tipos_clientes_model->delete('TIPOS_CLIENTES', 'tpc_id', $id);

        $this->session->set_flashdata('success', 'Tipo de Cliente excluído com sucesso!');
        log_info('Excluiu um tipo de cliente. ID: ' . $id);
        redirect(base_url() . 'index.php/tipos_clientes/gerenciar');
    }
}

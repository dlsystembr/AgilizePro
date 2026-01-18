<?php

class TiposPessoa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tipos_pessoa_model');
        $this->data['menuTiposPessoa'] = 'Tipos de Pessoa';
    }

    public function index()
    {
        $this->data['view'] = 'tipos_pessoa/tipos_pessoa';
        $this->data['tipos'] = $this->tipos_pessoa_model->get('tipos_pessoa', '*');
        $this->layout();
    }

    public function adicionar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('tipos_pessoa') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'ativo' => $this->input->post('ativo'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->tipos_pessoa_model->add('tipos_pessoa', $data) == true) {
                $this->session->set_flashdata('success', 'Tipo de pessoa adicionado com sucesso!');
                log_info('Adicionou um tipo de pessoa. Nome: ' . $data['nome']);
                redirect(site_url('tipos_pessoa/'));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar adicionar o tipo de pessoa.</div>';
            }
        }

        $this->data['view'] = 'tipos_pessoa/adicionarTipoPessoa';
        $this->layout();
    }

    public function editar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Tipo de pessoa não encontrado.');
            redirect(site_url('tipos_pessoa/'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('tipos_pessoa') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nome' => $this->input->post('nome'),
                'descricao' => $this->input->post('descricao'),
                'ativo' => $this->input->post('ativo'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->tipos_pessoa_model->edit('tipos_pessoa', $data, 'id', $id) == true) {
                $this->session->set_flashdata('success', 'Tipo de pessoa editado com sucesso!');
                log_info('Alterou um tipo de pessoa. ID: ' . $id);
                redirect(site_url('tipos_pessoa/'));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar editar o tipo de pessoa.</div>';
            }
        }

        $this->data['tipo'] = $this->tipos_pessoa_model->getById($id);
        $this->data['view'] = 'tipos_pessoa/editarTipoPessoa';
        $this->layout();
    }

    public function excluir()
    {
        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir tipo de pessoa.');
            redirect(site_url('tipos_pessoa/'));
        }

        // Verificar se existem pessoas com este tipo
        $this->db->where('tipo_id', $id);
        $count = $this->db->count_all_results('pessoa_tipos');

        if ($count > 0) {
            $this->session->set_flashdata('error', 'Não é possível excluir este tipo pois existem pessoas vinculadas a ele.');
            redirect(site_url('tipos_pessoa/'));
        }

        if ($this->tipos_pessoa_model->delete('tipos_pessoa', 'id', $id) == true) {
            log_info('Removeu um tipo de pessoa. ID: ' . $id);
            $this->session->set_flashdata('success', 'Tipo de pessoa excluído com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir tipo de pessoa.');
        }
        redirect(site_url('tipos_pessoa/'));
    }
} 
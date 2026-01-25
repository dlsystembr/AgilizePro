<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Empresas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Empresas_model');
        $this->data['menuEmpresas'] = 'empresas';
    }

    public function index()
    {
        // Verifica se existe alguma empresa cadastrada
        $empresa = $this->db->limit(1)->get('empresas')->row();

        if ($empresa) {
            // Se existe, redireciona para editar
            redirect(site_url('empresas/editar/' . $empresa->emp_id));
        } else {
            // Se não existe, redireciona para adicionar
            redirect(site_url('empresas/adicionar'));
        }
    }

    public function gerenciar()
    {
        // Mesmo comportamento do index
        $this->index();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aEmpresa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar empresas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('emp_cnpj', 'cnpj', 'required|trim');
        $this->form_validation->set_rules('emp_razao_social', 'Razão Social', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            // Upload de logo
            $logoPath = '';
            if (!empty($_FILES['userfile']['name'])) {
                $this->load->library('upload');
                $config['upload_path'] = './assets/logos/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = true;

                // Cria o diretório se não existir
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->upload->initialize($config);

                if ($this->upload->do_upload('userfile')) {
                    $upload_data = $this->upload->data();
                    $logoPath = 'assets/logos/' . $upload_data['file_name'];
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Erro no upload: ' . $this->upload->display_errors('', '') . '</div>';
                }
            }

            $data = [
                'emp_cnpj' => preg_replace('/[^0-9]/', '', $this->input->post('emp_cnpj')),
                'emp_razao_social' => $this->input->post('emp_razao_social'),
                'emp_nome_fantasia' => $this->input->post('emp_nome_fantasia'),
                'emp_ie' => $this->input->post('emp_ie'),
                'emp_cep' => preg_replace('/[^0-9]/', '', $this->input->post('emp_cep')),
                'emp_logradouro' => $this->input->post('emp_logradouro'),
                'emp_numero' => $this->input->post('emp_numero'),
                'emp_complemento' => $this->input->post('emp_complemento'),
                'emp_bairro' => $this->input->post('emp_bairro'),
                'emp_cidade' => $this->input->post('emp_cidade'),
                'emp_uf' => $this->input->post('emp_uf'),
                'emp_telefone' => preg_replace('/[^0-9]/', '', $this->input->post('emp_telefone')),
                'emp_email' => $this->input->post('emp_email'),
                'emp_regime_tributario' => $this->input->post('emp_regime_tributario'),
                'emp_logo_path' => $logoPath,
            ];

            if ($this->Empresas_model->add('empresas', $data)) {
                $empresaId = $this->db->insert_id();
                $this->session->set_flashdata('success', 'Empresa adicionada com sucesso!');
                log_info('Adicionou uma empresa.');
                redirect(site_url('empresas/editar/' . $empresaId));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        // Carrega estados para o formulário
        $this->data['estados'] = $this->db->order_by('est_uf', 'ASC')->get('estados')->result();

        $this->data['view'] = 'empresas/adicionarEmpresa';
        return $this->layout();
    }

    public function editar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url('index.php/empresas'));
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eEmpresa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar empresas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('emp_cnpj', 'cnpj', 'required|trim');
        $this->form_validation->set_rules('emp_razao_social', 'Razão Social', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            // Upload de logo
            $logoPath = $this->input->post('EMP_LOGO_PATH_ATUAL'); // Mantém o logo atual
            if (!empty($_FILES['userfile']['name'])) {
                $this->load->library('upload');
                $config['upload_path'] = './assets/logos/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = true;

                // Cria o diretório se não existir
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->upload->initialize($config);

                if ($this->upload->do_upload('userfile')) {
                    // Remove o logo antigo se existir
                    $logoAntigo = $this->input->post('EMP_LOGO_PATH_ATUAL');
                    if ($logoAntigo && file_exists(FCPATH . $logoAntigo)) {
                        unlink(FCPATH . $logoAntigo);
                    }

                    $upload_data = $this->upload->data();
                    $logoPath = 'assets/logos/' . $upload_data['file_name'];
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Erro no upload: ' . $this->upload->display_errors('', '') . '</div>';
                }
            }

            $data = [
                'emp_cnpj' => preg_replace('/[^0-9]/', '', $this->input->post('emp_cnpj')),
                'emp_razao_social' => $this->input->post('emp_razao_social'),
                'emp_nome_fantasia' => $this->input->post('emp_nome_fantasia'),
                'emp_ie' => $this->input->post('emp_ie'),
                'emp_cep' => preg_replace('/[^0-9]/', '', $this->input->post('emp_cep')),
                'emp_logradouro' => $this->input->post('emp_logradouro'),
                'emp_numero' => $this->input->post('emp_numero'),
                'emp_complemento' => $this->input->post('emp_complemento'),
                'emp_bairro' => $this->input->post('emp_bairro'),
                'emp_cidade' => $this->input->post('emp_cidade'),
                'emp_uf' => $this->input->post('emp_uf'),
                'emp_telefone' => preg_replace('/[^0-9]/', '', $this->input->post('emp_telefone')),
                'emp_email' => $this->input->post('emp_email'),
                'emp_regime_tributario' => $this->input->post('emp_regime_tributario'),
                'emp_logo_path' => $logoPath,
            ];

            if ($this->Empresas_model->edit('empresas', $data, 'emp_id', $id)) {
                $this->session->set_flashdata('success', 'Empresa editada com sucesso!');
                log_info('Alterou uma empresa. ID ' . $id);
                redirect(site_url('empresas/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->Empresas_model->getById($id);
        $this->data['estados'] = $this->db->order_by('est_uf', 'ASC')->get('estados')->result();

        $this->data['view'] = 'empresas/editarEmpresa';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dEmpresa')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir empresas.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir empresa.');
            redirect(site_url('empresas/gerenciar/'));
        }

        $this->Empresas_model->delete('empresas', 'emp_id', $id);
        log_info('Removeu uma empresa. ID ' . $id);

        $this->session->set_flashdata('success', 'Empresa excluída com sucesso!');
        redirect(site_url('empresas/'));
    }
}

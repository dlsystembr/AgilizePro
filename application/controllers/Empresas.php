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
            redirect(site_url('empresas/editar/' . $empresa->EMP_ID));
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

        $this->form_validation->set_rules('EMP_CNPJ', 'CNPJ', 'required|trim');
        $this->form_validation->set_rules('EMP_RAZAO_SOCIAL', 'Razão Social', 'required|trim');
        $this->form_validation->set_rules('EMP_CODIGO', 'Código', 'trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            // Gera código automaticamente se vazio
            $codigo = trim((string) $this->input->post('EMP_CODIGO', true));
            if ($codigo === '') {
                $row = $this->db->query("SELECT MAX(CAST(EMP_CODIGO AS UNSIGNED)) AS max_cod FROM empresas WHERE EMP_CODIGO REGEXP '^[0-9]+$'")->row();
                $next = isset($row->max_cod) && $row->max_cod !== null ? ((int) $row->max_cod) + 1 : 1;
                $codigo = (string) $next;
            }

            $data = [
                'EMP_CODIGO' => $codigo,
                'EMP_CNPJ' => preg_replace('/[^0-9]/', '', $this->input->post('EMP_CNPJ')),
                'EMP_RAZAO_SOCIAL' => $this->input->post('EMP_RAZAO_SOCIAL'),
                'EMP_NOME_FANTASIA' => $this->input->post('EMP_NOME_FANTASIA'),
                'EMP_IE' => $this->input->post('EMP_IE'),
                'EMP_CEP' => preg_replace('/[^0-9]/', '', $this->input->post('EMP_CEP')),
                'EMP_LOGRADOURO' => $this->input->post('EMP_LOGRADOURO'),
                'EMP_NUMERO' => $this->input->post('EMP_NUMERO'),
                'EMP_COMPLEMENTO' => $this->input->post('EMP_COMPLEMENTO'),
                'EMP_BAIRRO' => $this->input->post('EMP_BAIRRO'),
                'EMP_CIDADE' => $this->input->post('EMP_CIDADE'),
                'EMP_UF' => $this->input->post('EMP_UF'),
                'EMP_TELEFONE' => preg_replace('/[^0-9]/', '', $this->input->post('EMP_TELEFONE')),
                'EMP_EMAIL' => $this->input->post('EMP_EMAIL'),
                'EMP_REGIME_TRIBUTARIO' => $this->input->post('EMP_REGIME_TRIBUTARIO'),
                'EMP_LOGO_PATH' => $this->input->post('EMP_LOGO_PATH'),
                'EMP_SITUACAO' => $this->input->post('EMP_SITUACAO') !== null ? (int) $this->input->post('EMP_SITUACAO') : 1,
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
        $this->data['estados'] = $this->db->order_by('EST_UF', 'ASC')->get('estados')->result();

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

        $this->form_validation->set_rules('EMP_CNPJ', 'CNPJ', 'required|trim');
        $this->form_validation->set_rules('EMP_RAZAO_SOCIAL', 'Razão Social', 'required|trim');
        $this->form_validation->set_rules('EMP_CODIGO', 'Código', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'EMP_CODIGO' => $this->input->post('EMP_CODIGO'),
                'EMP_CNPJ' => preg_replace('/[^0-9]/', '', $this->input->post('EMP_CNPJ')),
                'EMP_RAZAO_SOCIAL' => $this->input->post('EMP_RAZAO_SOCIAL'),
                'EMP_NOME_FANTASIA' => $this->input->post('EMP_NOME_FANTASIA'),
                'EMP_IE' => $this->input->post('EMP_IE'),
                'EMP_CEP' => preg_replace('/[^0-9]/', '', $this->input->post('EMP_CEP')),
                'EMP_LOGRADOURO' => $this->input->post('EMP_LOGRADOURO'),
                'EMP_NUMERO' => $this->input->post('EMP_NUMERO'),
                'EMP_COMPLEMENTO' => $this->input->post('EMP_COMPLEMENTO'),
                'EMP_BAIRRO' => $this->input->post('EMP_BAIRRO'),
                'EMP_CIDADE' => $this->input->post('EMP_CIDADE'),
                'EMP_UF' => $this->input->post('EMP_UF'),
                'EMP_TELEFONE' => preg_replace('/[^0-9]/', '', $this->input->post('EMP_TELEFONE')),
                'EMP_EMAIL' => $this->input->post('EMP_EMAIL'),
                'EMP_REGIME_TRIBUTARIO' => $this->input->post('EMP_REGIME_TRIBUTARIO'),
                'EMP_LOGO_PATH' => $this->input->post('EMP_LOGO_PATH'),
                'EMP_SITUACAO' => $this->input->post('EMP_SITUACAO') !== null ? (int) $this->input->post('EMP_SITUACAO') : 1,
            ];

            if ($this->Empresas_model->edit('empresas', $data, 'EMP_ID', $id)) {
                $this->session->set_flashdata('success', 'Empresa editada com sucesso!');
                log_info('Alterou uma empresa. ID ' . $id);
                redirect(site_url('empresas/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->Empresas_model->getById($id);
        $this->data['estados'] = $this->db->order_by('EST_UF', 'ASC')->get('estados')->result();

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

        $this->Empresas_model->delete('empresas', 'EMP_ID', $id);
        log_info('Removeu uma empresa. ID ' . $id);

        $this->session->set_flashdata('success', 'Empresa excluída com sucesso!');
        redirect(site_url('empresas/'));
    }
}

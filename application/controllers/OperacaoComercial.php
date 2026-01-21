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

        $this->data['view'] = 'operacaocomercial/operacaocomercial';
        
        // Configuração da paginação
        $this->load->library('pagination');
        
        // Parâmetros de pesquisa
        $search = $this->input->get('pesquisa');
        $situacao = $this->input->get('situacao') !== null ? $this->input->get('situacao') : '1'; // Padrão: Ativas
        $per_page = 20; // Número fixo de registros por página
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $start = ($page - 1) * $per_page;

        // Configuração da paginação
        $this->data['configuration']['base_url'] = base_url('index.php/operacaocomercial/index');
        $this->data['configuration']['total_rows'] = $this->OperacaoComercial_model->count($search, $situacao);
        $this->data['configuration']['per_page'] = $per_page;
        $this->data['configuration']['use_page_numbers'] = TRUE;
        $this->data['configuration']['page_query_string'] = TRUE;
        $this->data['configuration']['query_string_segment'] = 'page';
        $this->data['configuration']['reuse_query_string'] = TRUE;
        $this->data['configuration']['num_links'] = 2;

        $this->pagination->initialize($this->data['configuration']);

        // Busca as operações com paginação
        $this->data['results'] = $this->OperacaoComercial_model->get($search, $situacao, $per_page, $start);
        $this->data['search'] = $search;
        $configuracao = $this->Mapos_model->getConfiguracao();
        $this->data['regime_tributario'] = isset($configuracao['regime_tributario']) ? $configuracao['regime_tributario'] : null;
        
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aOperacaoComercial')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar operações comerciais.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('OPC_SIGLA', 'Sigla', 'required|trim|max_length[10]');
        $this->form_validation->set_rules('OPC_NOME', 'Nome', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('OPC_NATUREZA_OPERACAO', 'Natureza da Operação', 'required|trim');
        $this->form_validation->set_rules('OPC_TIPO_MOVIMENTO', 'Tipo de Movimento', 'required|trim');
        $this->form_validation->set_rules('OPC_FINALIDADE_NFE', 'Finalidade NFe', 'required|trim');

        if ($this->form_validation->run('operacao_comercial') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'OPC_SIGLA' => $this->input->post('OPC_SIGLA'),
                'OPC_NOME' => $this->input->post('OPC_NOME'),
                'OPC_NATUREZA_OPERACAO' => $this->input->post('OPC_NATUREZA_OPERACAO'),
                'OPC_TIPO_MOVIMENTO' => $this->input->post('OPC_TIPO_MOVIMENTO'),
                'OPC_AFETA_CUSTO' => $this->input->post('OPC_AFETA_CUSTO'),
                'OPC_FATO_FISCAL' => $this->input->post('OPC_FATO_FISCAL'),
                'OPC_GERA_FINANCEIRO' => $this->input->post('OPC_GERA_FINANCEIRO'),
                'OPC_MOVIMENTA_ESTOQUE' => $this->input->post('OPC_MOVIMENTA_ESTOQUE'),
                'OPC_SITUACAO' => $this->input->post('OPC_SITUACAO'),
                'OPC_FINALIDADE_NFE' => $this->input->post('OPC_FINALIDADE_NFE')
            );
            if ($id = $this->OperacaoComercial_model->add($data)) {
                $this->session->set_flashdata('success', 'Operação Comercial adicionada com sucesso!');
                redirect(base_url('index.php/operacaocomercial/visualizarOperacao/' . $id));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro.</div>';
            }
        }
        $configuracao = $this->Mapos_model->getConfiguracao();
        $this->data['regime_tributario'] = isset($configuracao['regime_tributario']) ? $configuracao['regime_tributario'] : null;
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

        $this->form_validation->set_rules('OPC_SIGLA', 'Sigla', 'required|trim|max_length[10]');
        $this->form_validation->set_rules('OPC_NOME', 'Nome', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('OPC_NATUREZA_OPERACAO', 'Natureza da Operação', 'required|trim');
        $this->form_validation->set_rules('OPC_TIPO_MOVIMENTO', 'Tipo de Movimento', 'required|trim');
        $this->form_validation->set_rules('OPC_FINALIDADE_NFE', 'Finalidade NFe', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'OPC_SIGLA' => $this->input->post('OPC_SIGLA'),
                'OPC_NOME' => $this->input->post('OPC_NOME'),
                'OPC_NATUREZA_OPERACAO' => $this->input->post('OPC_NATUREZA_OPERACAO'),
                'OPC_TIPO_MOVIMENTO' => $this->input->post('OPC_TIPO_MOVIMENTO'),
                'OPC_AFETA_CUSTO' => $this->input->post('OPC_AFETA_CUSTO'),
                'OPC_FATO_FISCAL' => $this->input->post('OPC_FATO_FISCAL'),
                'OPC_GERA_FINANCEIRO' => $this->input->post('OPC_GERA_FINANCEIRO'),
                'OPC_MOVIMENTA_ESTOQUE' => $this->input->post('OPC_MOVIMENTA_ESTOQUE'),
                'OPC_SITUACAO' => $this->input->post('OPC_SITUACAO'),
                'OPC_FINALIDADE_NFE' => $this->input->post('OPC_FINALIDADE_NFE')
            );
            if ($this->OperacaoComercial_model->update($id, $data)) {
                $this->session->set_flashdata('success', 'Operação Comercial editada com sucesso!');
                redirect(base_url('index.php/operacaocomercial/visualizarOperacao/' . $id));
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro.</div>';
            }
        }
        $this->data['result'] = $result;
        $configuracao = $this->Mapos_model->getConfiguracao();
        $this->data['regime_tributario'] = isset($configuracao['regime_tributario']) ? $configuracao['regime_tributario'] : null;
        $this->data['view'] = 'operacaocomercial/editarOperacao';
        return $this->layout();
    }

    public function excluir($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dOperacaoComercial')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir operações comerciais.');
            redirect(base_url());
        }

        if ($id == null) {
            $this->session->set_flashdata('error', 'Operação não encontrada.');
            redirect(base_url('index.php/operacaocomercial'));
        }

        if ($this->OperacaoComercial_model->delete($id)) {
            $this->session->set_flashdata('success', 'Operação Comercial excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar excluir a operação comercial.');
        }
        redirect(base_url('index.php/operacaocomercial'));
    }

    public function visualizaroperacao($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOperacaoComercial')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar operações comerciais.');
            redirect(base_url());
        }

        if (!$id) {
            $this->session->set_flashdata('error', 'Operação não encontrada.');
            redirect(base_url() . 'index.php/operacaocomercial');
        }

        $this->data['result'] = $this->OperacaoComercial_model->getById($id);
        if (!$this->data['result']) {
            $this->session->set_flashdata('error', 'Operação não encontrada.');
            redirect(base_url() . 'index.php/operacaocomercial');
        }

        $this->data['custom_error'] = '';
        $this->data['view'] = 'operacaocomercial/visualizarOperacao';
        return $this->layout();
    }

    public function adicionarOperacao()
    {
        $this->load->library('permission');
        $this->permission->checkPermission($this->session->userdata('permissao'), 'aOperacaoComercial');
        $this->load->library('form_validation');
        $data['custom_error'] = '';

        if ($this->form_validation->run('operacao_comercial') == false) {
            $data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'OPC_SIGLA' => $this->input->post('OPC_SIGLA'),
                'OPC_NOME' => $this->input->post('OPC_NOME'),
                'OPC_NATUREZA_OPERACAO' => $this->input->post('OPC_NATUREZA_OPERACAO'),
                'OPC_TIPO_MOVIMENTO' => $this->input->post('OPC_TIPO_MOVIMENTO'),
                'OPC_AFETA_CUSTO' => $this->input->post('OPC_AFETA_CUSTO'),
                'OPC_FATO_FISCAL' => $this->input->post('OPC_FATO_FISCAL'),
                'OPC_GERA_FINANCEIRO' => $this->input->post('OPC_GERA_FINANCEIRO'),
                'OPC_MOVIMENTA_ESTOQUE' => $this->input->post('OPC_MOVIMENTA_ESTOQUE'),
                'OPC_SITUACAO' => $this->input->post('OPC_SITUACAO'),
                'OPC_FINALIDADE_NFE' => $this->input->post('OPC_FINALIDADE_NFE')
            );

            if ($this->operacao_comercial_model->add('operacao_comercial', $data) == true) {
                $this->session->set_flashdata('success', 'Operação comercial adicionada com sucesso!');
                $id = $this->operacao_comercial_model->getLastId();
                redirect(base_url() . 'index.php/operacaocomercial/visualizarOperacao/' . $id);
            } else {
                $data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar adicionar a operação comercial.</div>';
            }
        }

        $data['view'] = 'operacaocomercial/adicionarOperacao';
        $this->load->view('tema/topo', $data);
    }

    public function editarOperacao()
    {
        $this->load->library('permission');
        $this->permission->checkPermission($this->session->userdata('permissao'), 'eOperacaoComercial');
        $this->load->library('form_validation');
        $data['custom_error'] = '';

        if ($this->form_validation->run('operacao_comercial') == false) {
            $data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'OPC_SIGLA' => $this->input->post('OPC_SIGLA'),
                'OPC_NOME' => $this->input->post('OPC_NOME'),
                'OPC_NATUREZA_OPERACAO' => $this->input->post('OPC_NATUREZA_OPERACAO'),
                'OPC_TIPO_MOVIMENTO' => $this->input->post('OPC_TIPO_MOVIMENTO'),
                'OPC_AFETA_CUSTO' => $this->input->post('OPC_AFETA_CUSTO'),
                'OPC_FATO_FISCAL' => $this->input->post('OPC_FATO_FISCAL'),
                'OPC_GERA_FINANCEIRO' => $this->input->post('OPC_GERA_FINANCEIRO'),
                'OPC_MOVIMENTA_ESTOQUE' => $this->input->post('OPC_MOVIMENTA_ESTOQUE'),
                'OPC_SITUACAO' => $this->input->post('OPC_SITUACAO'),
                'OPC_FINALIDADE_NFE' => $this->input->post('OPC_FINALIDADE_NFE')
            );

            if ($this->operacao_comercial_model->edit('operacao_comercial', $data, 'OPC_ID', $this->input->post('OPC_ID')) == true) {
                $this->session->set_flashdata('success', 'Operação comercial editada com sucesso!');
                redirect(base_url() . 'index.php/operacaocomercial/visualizarOperacao/' . $this->input->post('OPC_ID'));
            } else {
                $data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar editar a operação comercial.</div>';
            }
        }

        $data['result'] = $this->operacao_comercial_model->getById($this->uri->segment(3));
        $data['view'] = 'operacaocomercial/editarOperacao';
        $this->load->view('tema/topo', $data);
    }
} 
<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Contratos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Contratos_model');
        $this->data['menuContratos'] = 'contratos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vContrato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar contratos.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('contratos/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->Contratos_model->count('contratos', $pesquisa);
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url('index.php/contratos') . "?pesquisa={$pesquisa}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->Contratos_model->get('contratos', '*', $pesquisa, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'contratos/contratos';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aContrato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar contratos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('PES_ID', 'Cliente', 'required|trim');
        $this->form_validation->set_rules('CTR_NUMERO', 'Número do Contrato', 'required|trim|is_unique[contratos.CTR_NUMERO]');
        $this->form_validation->set_rules('CTR_DATA_INICIO', 'Data de Início', 'required|trim');
        $this->form_validation->set_rules('CTR_TIPO_ASSINANTE', 'Tipo de Assinante', 'required|in_list[1,2,3,4,5,6,7,8,99]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $anexo = null;
            
            // Processar upload de anexo
            if (!empty($_FILES['CTR_ANEXO']['name'])) {
                $config['upload_path'] = './uploads/contratos/';
                $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                $config['max_size'] = 5120; // 5MB
                $config['encrypt_name'] = true;

                // Criar diretório se não existir
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('CTR_ANEXO')) {
                    $upload_data = $this->upload->data();
                    $anexo = 'uploads/contratos/' . $upload_data['file_name'];
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Erro no upload: ' . $this->upload->display_errors('', '') . '</div>';
                }
            }

            if (!$this->data['custom_error']) {
                // Converter datas do formato brasileiro para o formato do banco
                $dataInicio = $this->input->post('CTR_DATA_INICIO');
                $dataFim = $this->input->post('CTR_DATA_FIM');
                
                if ($dataInicio) {
                    $dataInicio = DateTime::createFromFormat('d/m/Y', $dataInicio);
                    $dataInicio = $dataInicio ? $dataInicio->format('Y-m-d') : null;
                }
                
                if ($dataFim) {
                    $dataFim = DateTime::createFromFormat('d/m/Y', $dataFim);
                    $dataFim = $dataFim ? $dataFim->format('Y-m-d') : null;
                }
                
                $data = [
                    'PES_ID' => $this->input->post('PES_ID'),
                    'CTR_NUMERO' => $this->input->post('CTR_NUMERO'),
                    'CTR_DATA_INICIO' => $dataInicio,
                    'CTR_DATA_FIM' => $dataFim,
                    'CTR_TIPO_ASSINANTE' => $this->input->post('CTR_TIPO_ASSINANTE'),
                    'CTR_ANEXO' => $anexo,
                    'CTR_OBSERVACAO' => $this->input->post('CTR_OBSERVACAO'),
                    'CTR_SITUACAO' => $this->input->post('CTR_SITUACAO') !== null ? (int) $this->input->post('CTR_SITUACAO') : 1,
                    'CTR_DATA_CADASTRO' => date('Y-m-d H:i:s'),
                ];

                if ($this->Contratos_model->add('contratos', $data)) {
                    $contratoId = $this->db->insert_id();
                    $this->session->set_flashdata('success', 'Contrato adicionado com sucesso!');
                    log_info('Adicionou um contrato.');
                    redirect(base_url('index.php/contratos/visualizar/' . $contratoId));
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger"><p>Ocorreu um erro ao adicionar o contrato.</p></div>';
                }
            }
        }

        $this->data['tiposAssinante'] = $this->Contratos_model->getTiposAssinante();
        $this->data['view'] = 'contratos/adicionarContrato';
        return $this->layout();
    }

    public function editar($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eContrato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar contratos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('PES_ID', 'Cliente', 'required|trim');
        $this->form_validation->set_rules('CTR_NUMERO', 'Número do Contrato', 'required|trim');
        $this->form_validation->set_rules('CTR_DATA_INICIO', 'Data de Início', 'required|trim');
        $this->form_validation->set_rules('CTR_TIPO_ASSINANTE', 'Tipo de Assinante', 'required|in_list[1,2,3,4,5,6,7,8,99]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $contrato = $this->Contratos_model->getById($id);
            $anexo = $contrato->CTR_ANEXO;
            
            // Processar upload de novo anexo
            if (!empty($_FILES['CTR_ANEXO']['name'])) {
                $config['upload_path'] = './uploads/contratos/';
                $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                $config['max_size'] = 5120; // 5MB
                $config['encrypt_name'] = true;

                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('CTR_ANEXO')) {
                    // Remover anexo antigo
                    if ($anexo && file_exists($anexo)) {
                        unlink($anexo);
                    }
                    $upload_data = $this->upload->data();
                    $anexo = 'uploads/contratos/' . $upload_data['file_name'];
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Erro no upload: ' . $this->upload->display_errors('', '') . '</div>';
                }
            }

            if (!$this->data['custom_error']) {
                // Converter datas do formato brasileiro para o formato do banco
                $dataInicio = $this->input->post('CTR_DATA_INICIO');
                $dataFim = $this->input->post('CTR_DATA_FIM');
                
                if ($dataInicio) {
                    $dataInicio = DateTime::createFromFormat('d/m/Y', $dataInicio);
                    $dataInicio = $dataInicio ? $dataInicio->format('Y-m-d') : null;
                }
                
                if ($dataFim) {
                    $dataFim = DateTime::createFromFormat('d/m/Y', $dataFim);
                    $dataFim = $dataFim ? $dataFim->format('Y-m-d') : null;
                }
                
                $data = [
                    'PES_ID' => $this->input->post('PES_ID'),
                    'CTR_NUMERO' => $this->input->post('CTR_NUMERO'),
                    'CTR_DATA_INICIO' => $dataInicio,
                    'CTR_DATA_FIM' => $dataFim,
                    'CTR_TIPO_ASSINANTE' => $this->input->post('CTR_TIPO_ASSINANTE'),
                    'CTR_ANEXO' => $anexo,
                    'CTR_OBSERVACAO' => $this->input->post('CTR_OBSERVACAO'),
                    'CTR_SITUACAO' => $this->input->post('CTR_SITUACAO') !== null ? (int) $this->input->post('CTR_SITUACAO') : 1,
                    'CTR_DATA_ALTERACAO' => date('Y-m-d H:i:s'),
                ];

                if ($this->Contratos_model->edit('contratos', $data, 'CTR_ID', $id)) {
                    $this->session->set_flashdata('success', 'Contrato editado com sucesso!');
                    log_info('Alterou um contrato. ID ' . $id);
                    redirect(base_url('index.php/contratos/visualizar/' . $id));
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger"><p>Ocorreu um erro ao editar o contrato.</p></div>';
                }
            }
        }

        $this->data['result'] = $this->Contratos_model->getById($id);
        $this->data['tiposAssinante'] = $this->Contratos_model->getTiposAssinante();
        $this->data['view'] = 'contratos/editarContrato';
        return $this->layout();
    }

    public function visualizar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vContrato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar contratos.');
            redirect(base_url());
        }

        if ($id == null) {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        $this->data['result'] = $this->Contratos_model->getById($id);
        $this->data['tiposAssinante'] = $this->Contratos_model->getTiposAssinante();
        
        if (!$this->data['result']) {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        $this->data['view'] = 'contratos/visualizarContrato';
        return $this->layout();
    }

    public function excluir($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dContrato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir contratos.');
            redirect(base_url());
        }

        if ($id == null) {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        $contrato = $this->Contratos_model->getById($id);
        
        if (!$contrato) {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        // Remover anexo se existir
        if ($contrato->CTR_ANEXO && file_exists($contrato->CTR_ANEXO)) {
            unlink($contrato->CTR_ANEXO);
        }

        if ($this->Contratos_model->delete('contratos', 'CTR_ID', $id)) {
            $this->session->set_flashdata('success', 'Contrato excluído com sucesso!');
            log_info('Excluiu um contrato. ID ' . $id);
        } else {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao excluir o contrato.');
        }

        redirect(base_url('index.php/contratos'));
    }

    public function download_anexo($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vContrato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar contratos.');
            redirect(base_url());
        }

        if ($id == null) {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        $contrato = $this->Contratos_model->getById($id);
        
        if (!$contrato || !$contrato->CTR_ANEXO) {
            $this->session->set_flashdata('error', 'Anexo não encontrado.');
            redirect(base_url('index.php/contratos/visualizar/' . $id));
        }

        if (!file_exists($contrato->CTR_ANEXO)) {
            $this->session->set_flashdata('error', 'Arquivo não encontrado.');
            redirect(base_url('index.php/contratos/visualizar/' . $id));
        }

        $this->load->helper('download');
        force_download($contrato->CTR_ANEXO, null);
    }

    public function buscarCliente()
    {
        $termo = $this->input->get('term');
        
        if (!$termo) {
            echo json_encode([]);
            return;
        }

        $this->db->select('PES_ID, PES_NOME, PES_RAZAO_SOCIAL, PES_CPFCNPJ');
        $this->db->from('pessoas');
        $this->db->group_start();
        $this->db->like('PES_NOME', $termo);
        $this->db->or_like('PES_RAZAO_SOCIAL', $termo);
        $this->db->or_like('PES_CPFCNPJ', $termo);
        $this->db->group_end();
        $this->db->where('PES_SITUACAO', 1);
        $this->db->limit(10);
        
        $pessoas = $this->db->get()->result();
        
        $resultado = [];
        foreach ($pessoas as $pessoa) {
            $label = $pessoa->PES_NOME;
            if ($pessoa->PES_RAZAO_SOCIAL) {
                $label .= ' (' . $pessoa->PES_RAZAO_SOCIAL . ')';
            }
            $label .= ' - ' . $pessoa->PES_CPFCNPJ;
            
            $resultado[] = [
                'id' => $pessoa->PES_ID,
                'label' => $label,
                'value' => $pessoa->PES_NOME,
                'nome' => $pessoa->PES_NOME,
                'razao_social' => $pessoa->PES_RAZAO_SOCIAL,
                'cpfcnpj' => $pessoa->PES_CPFCNPJ
            ];
        }
        
        echo json_encode($resultado);
    }
}

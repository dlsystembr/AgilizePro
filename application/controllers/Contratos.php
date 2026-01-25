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

        $this->form_validation->set_rules('pes_id', 'Cliente', 'required|trim');
        $this->form_validation->set_rules('ctr_numero', 'Número do Contrato', 'required|trim|is_unique[contratos.ctr_numero]');
        $this->form_validation->set_rules('ctr_data_inicio', 'Data de Início', 'required|trim');
        $this->form_validation->set_rules('ctr_tipo_assinante', 'Tipo de Assinante', 'required|in_list[1,2,3,4,5,6,7,8,99]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $anexo = null;
            
            // Processar upload de anexo
            if (!empty($_FILES['ctr_anexo']['name'])) {
                $config['upload_path'] = './uploads/contratos/';
                $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                $config['max_size'] = 5120; // 5MB
                $config['encrypt_name'] = true;

                // Criar diretório se não existir
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('ctr_anexo')) {
                    $upload_data = $this->upload->data();
                    $anexo = 'uploads/contratos/' . $upload_data['file_name'];
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Erro no upload: ' . $this->upload->display_errors('', '') . '</div>';
                }
            }

            if (!$this->data['custom_error']) {
                // Converter datas do formato brasileiro para o formato do banco
                $dataInicio = $this->input->post('ctr_data_inicio');
                $dataFim = $this->input->post('ctr_data_fim');
                
                if ($dataInicio) {
                    $dataInicio = DateTime::createFromFormat('d/m/Y', $dataInicio);
                    $dataInicio = $dataInicio ? $dataInicio->format('Y-m-d') : null;
                }
                
                if ($dataFim) {
                    $dataFim = DateTime::createFromFormat('d/m/Y', $dataFim);
                    $dataFim = $dataFim ? $dataFim->format('Y-m-d') : null;
                }
                
                $data = [
                    'pes_id' => $this->input->post('pes_id'),
                    'ctr_numero' => $this->input->post('ctr_numero'),
                    'ctr_data_inicio' => $dataInicio,
                    'ctr_data_fim' => $dataFim,
                    'ctr_tipo_assinante' => $this->input->post('ctr_tipo_assinante'),
                    'ctr_anexo' => $anexo,
                    'ctr_observacao' => $this->input->post('ctr_observacao'),
                    'ctr_situacao' => $this->input->post('ctr_situacao') !== null ? (int) $this->input->post('ctr_situacao') : 1,
                    'ctr_data_cadastro' => date('Y-m-d H:i:s'),
                ];

                if ($this->Contratos_model->add('contratos', $data)) {
                    $contratoId = $this->db->insert_id();
                    
                    // Salvar itens do contrato (serviços)
                    $itens = $this->input->post('itens');
                    if (!empty($itens) && is_array($itens)) {
                        foreach ($itens as $item) {
                            if (!empty($item['pro_id']) && !empty($item['cti_preco'])) {
                                $itemData = [
                                    'ctr_id' => $contratoId,
                                    'pro_id' => $item['pro_id'],
                                    'cti_preco' => str_replace(',', '.', $item['cti_preco']),
                                    'cti_quantidade' => !empty($item['cti_quantidade']) ? str_replace(',', '.', $item['cti_quantidade']) : 1.0000,
                                    'cti_observacao' => !empty($item['cti_observacao']) ? $item['cti_observacao'] : null,
                                    'cti_ativo' => 1
                                ];
                                $this->Contratos_model->addItem($itemData);
                            }
                        }
                    }
                    
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

        $this->form_validation->set_rules('pes_id', 'Cliente', 'required|trim');
        $this->form_validation->set_rules('ctr_numero', 'Número do Contrato', 'required|trim');
        $this->form_validation->set_rules('ctr_data_inicio', 'Data de Início', 'required|trim');
        $this->form_validation->set_rules('ctr_tipo_assinante', 'Tipo de Assinante', 'required|in_list[1,2,3,4,5,6,7,8,99]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $contrato = $this->Contratos_model->getById($id);
            $anexo = $contrato->ctr_anexo;
            
            // Processar upload de novo anexo
            if (!empty($_FILES['ctr_anexo']['name'])) {
                $config['upload_path'] = './uploads/contratos/';
                $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                $config['max_size'] = 5120; // 5MB
                $config['encrypt_name'] = true;

                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('ctr_anexo')) {
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
                $dataInicio = $this->input->post('ctr_data_inicio');
                $dataFim = $this->input->post('ctr_data_fim');
                
                if ($dataInicio) {
                    $dataInicio = DateTime::createFromFormat('d/m/Y', $dataInicio);
                    $dataInicio = $dataInicio ? $dataInicio->format('Y-m-d') : null;
                }
                
                if ($dataFim) {
                    $dataFim = DateTime::createFromFormat('d/m/Y', $dataFim);
                    $dataFim = $dataFim ? $dataFim->format('Y-m-d') : null;
                }
                
                $data = [
                    'pes_id' => $this->input->post('pes_id'),
                    'ctr_numero' => $this->input->post('ctr_numero'),
                    'ctr_data_inicio' => $dataInicio,
                    'ctr_data_fim' => $dataFim,
                    'ctr_tipo_assinante' => $this->input->post('ctr_tipo_assinante'),
                    'ctr_anexo' => $anexo,
                    'ctr_observacao' => $this->input->post('ctr_observacao'),
                    'ctr_situacao' => $this->input->post('ctr_situacao') !== null ? (int) $this->input->post('ctr_situacao') : 1,
                    'ctr_data_alteracao' => date('Y-m-d H:i:s'),
                ];

                if ($this->Contratos_model->edit('contratos', $data, 'ctr_id', $id)) {
                    // Processar itens do contrato
                    $itens = $this->input->post('itens');
                    $itensExistentes = $this->input->post('itens_existentes'); // IDs dos itens que devem ser mantidos
                    
                    // IDs dos itens que vieram no formulário (para manter)
                    $itensIdsForm = [];
                    
                    // Log para debug (remover depois)
                    log_message('debug', 'Itens recebidos no POST: ' . print_r($itens, true));
                    log_message('debug', 'Itens existentes: ' . print_r($itensExistentes, true));
                    
                    if (!empty($itens) && is_array($itens)) {
                        foreach ($itens as $index => $item) {
                            // Validar se tem pro_id e cti_preco
                            if (!empty($item['pro_id']) && isset($item['cti_preco']) && trim($item['cti_preco']) !== '') {
                                $itemData = [
                                    'ctr_id' => $id,
                                    'pro_id' => $item['pro_id'],
                                    'cti_preco' => str_replace(',', '.', $item['cti_preco']),
                                    'cti_quantidade' => !empty($item['cti_quantidade']) ? str_replace(',', '.', $item['cti_quantidade']) : 1.0000,
                                    'cti_observacao' => !empty($item['cti_observacao']) ? $item['cti_observacao'] : null,
                                    'cti_ativo' => isset($item['cti_ativo']) ? (int)$item['cti_ativo'] : 1
                                ];
                                
                                // Se tem cti_id, é atualização
                                if (!empty($item['cti_id'])) {
                                    $this->Contratos_model->updateItem($item['cti_id'], $itemData);
                                    $itensIdsForm[] = $item['cti_id'];
                                    log_message('debug', 'Item atualizado: cti_id=' . $item['cti_id']);
                                } else {
                                    // É novo item
                                    $resultado = $this->Contratos_model->addItem($itemData);
                                    if ($resultado) {
                                        $itensIdsForm[] = $resultado; // Adicionar o ID do novo item
                                        log_message('debug', 'Novo item adicionado: cti_id=' . $resultado);
                                    } else {
                                        log_message('error', 'Erro ao adicionar novo item');
                                    }
                                }
                            } else {
                                log_message('debug', 'Item ignorado - pro_id ou cti_preco vazio: ' . print_r($item, true));
                            }
                        }
                    } else {
                        log_message('debug', 'Nenhum item recebido no POST ou não é array');
                    }
                    
                    // Remover itens que não vieram no formulário
                    $itensAtuais = $this->Contratos_model->getItensByContratoId($id);
                    foreach ($itensAtuais as $itemAtual) {
                        if (!in_array($itemAtual->cti_id, $itensIdsForm)) {
                            $this->Contratos_model->deleteItem($itemAtual->cti_id);
                            log_message('debug', 'Item removido: cti_id=' . $itemAtual->cti_id);
                        }
                    }
                    
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
        
        // Carregar itens do contrato
        $this->data['itens'] = $this->Contratos_model->getItensByContratoId($id);
        
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

        // Carregar itens do contrato
        $this->data['itens'] = $this->Contratos_model->getItensByContratoId($id);

        $this->data['view'] = 'contratos/visualizarContrato';
        return $this->layout();
    }

    public function excluir($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dContrato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir contratos.');
            redirect(base_url());
        }

        // Aceitar ID via URL ou via POST
        if ($id == null) {
            $id = $this->input->post('id');
        }

        if ($id == null || $id == '') {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        $contrato = $this->Contratos_model->getById($id);
        
        if (!$contrato) {
            $this->session->set_flashdata('error', 'Contrato não encontrado.');
            redirect(base_url('index.php/contratos'));
        }

        // Remover anexo se existir
        if ($contrato->ctr_anexo && file_exists($contrato->ctr_anexo)) {
            unlink($contrato->ctr_anexo);
        }

        if ($this->Contratos_model->delete('contratos', 'ctr_id', $id)) {
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
        
        if (!$contrato || !$contrato->ctr_anexo) {
            $this->session->set_flashdata('error', 'Anexo não encontrado.');
            redirect(base_url('index.php/contratos/visualizar/' . $id));
        }

        if (!file_exists($contrato->ctr_anexo)) {
            $this->session->set_flashdata('error', 'Arquivo não encontrado.');
            redirect(base_url('index.php/contratos/visualizar/' . $id));
        }

        $this->load->helper('download');
        force_download($contrato->ctr_anexo, null);
    }

    public function buscarCliente()
    {
        $termo = $this->input->get('term');
        
        if (!$termo) {
            echo json_encode([]);
            return;
        }

        $this->db->select('pes_id, pes_nome, pes_razao_social, pes_cpfcnpj');
        $this->db->from('pessoas');
        $this->db->group_start();
        $this->db->like('pes_nome', $termo);
        $this->db->or_like('pes_razao_social', $termo);
        $this->db->or_like('pes_cpfcnpj', $termo);
        $this->db->group_end();
        $this->db->where('pes_situacao', 1);
        $this->db->limit(10);
        
        $pessoas = $this->db->get()->result();
        
        $resultado = [];
        foreach ($pessoas as $pessoa) {
            $label = $pessoa->pes_nome;
            if ($pessoa->pes_razao_social) {
                $label .= ' (' . $pessoa->pes_razao_social . ')';
            }
            $label .= ' - ' . $pessoa->pes_cpfcnpj;
            
            $resultado[] = [
                'id' => $pessoa->pes_id,
                'label' => $label,
                'value' => $pessoa->pes_nome,
                'nome' => $pessoa->pes_nome,
                'razao_social' => $pessoa->pes_razao_social,
                'cpfcnpj' => $pessoa->pes_cpfcnpj
            ];
        }
        
        echo json_encode($resultado);
    }

    /**
     * Autocomplete para buscar serviços (produtos com pro_tipo = 2)
     */
    public function autoCompleteServico()
    {
        $termo = $this->input->get('term');
        
        if (strlen($termo) < 2) {
            echo json_encode([]);
            return;
        }

        // Descobrir a chave primária da tabela produtos
        $primary_key_query = $this->db->query("SHOW KEYS FROM produtos WHERE Key_name = 'PRIMARY'");
        $primary_key = 'pro_id';
        if ($primary_key_query->num_rows() > 0) {
            $key_info = $primary_key_query->row();
            $primary_key = $key_info->Column_name;
        }

        $this->db->select("$primary_key as id, pro_descricao as label, pro_descricao as value, pro_preco_venda as preco");
        $this->db->from('produtos');
        $this->db->where('pro_tipo', 2); // Serviços
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->like('pro_descricao', $termo);
        $this->db->order_by('pro_descricao', 'asc');
        $this->db->limit(20);
        
        $servicos = $this->db->get()->result();
        
        $resultado = [];
        foreach ($servicos as $servico) {
            $resultado[] = [
                'id' => $servico->id,
                'label' => $servico->label,
                'value' => $servico->value,
                'preco' => floatval($servico->preco ?? 0)
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado);
    }
}

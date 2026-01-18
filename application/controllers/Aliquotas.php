<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Aliquotas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Aliquotas_model');
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vAliquota')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar alíquotas.');
            redirect(base_url());
        }

        $this->data['aliquotas'] = $this->Aliquotas_model->get('aliquotas', '*', '', 0, 0, false);
        $this->data['view'] = 'aliquotas/aliquotas';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aAliquota')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar alíquotas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('uf_origem', 'UF Origem', 'required|trim');
        $this->form_validation->set_rules('uf_destino', 'UF Destino', 'required|trim');
        $this->form_validation->set_rules('aliquota_origem', 'Alíquota Origem', 'required|trim|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('aliquota_destino', 'Alíquota Destino', 'required|trim|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $uf_origem = $this->input->post('uf_origem');
            $uf_destino = $this->input->post('uf_destino');
            $aliquota_origem = $this->input->post('aliquota_origem');
            $aliquota_destino = $this->input->post('aliquota_destino');

            // Verifica se já existe uma alíquota com a mesma combinação de UFs
            if ($uf_destino !== 'TODOS') {
                $aliquota_existente = $this->Aliquotas_model->getAliquota($uf_origem, $uf_destino);
                if ($aliquota_existente) {
                    $this->session->set_flashdata('error', 'Já existe uma alíquota cadastrada para esta combinação de UFs.');
                    redirect(base_url() . 'index.php/aliquotas/adicionar');
                    return;
                }
            }

            if ($uf_destino === 'TODOS') {
                // Se selecionou "Todos os Estados", adiciona para cada estado
                $ufs = $this->Aliquotas_model->getUFs();
                $success = true;
                $added = 0;

                foreach ($ufs as $uf => $nome) {
                    if ($uf !== $uf_origem) {
                        // Verifica se já existe uma alíquota para esta combinação
                        $aliquota_existente = $this->Aliquotas_model->getAliquota($uf_origem, $uf);
                        if (!$aliquota_existente) {
                            $data = [
                                'uf_origem' => $uf_origem,
                                'uf_destino' => $uf,
                                'aliquota_origem' => $aliquota_origem,
                                'aliquota_destino' => $aliquota_destino,
                                'created_at' => date('Y-m-d H:i:s')
                            ];
                            if ($this->Aliquotas_model->add($data)) {
                                $added++;
                            } else {
                                $success = false;
                            }
                        }
                    }
                }

                if ($success && $added > 0) {
                    $this->session->set_flashdata('success', 'Alíquotas adicionadas com sucesso para ' . $added . ' estado(s)!');
                    redirect(base_url() . 'index.php/aliquotas');
                } else if ($added == 0) {
                    $this->session->set_flashdata('error', 'Todas as alíquotas já estão cadastradas para este estado de origem.');
                    redirect(base_url() . 'index.php/aliquotas/adicionar');
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar adicionar algumas alíquotas.</div>';
                }
            } else {
                // Adiciona para um estado específico
                $data = [
                    'uf_origem' => $uf_origem,
                    'uf_destino' => $uf_destino,
                    'aliquota_origem' => $aliquota_origem,
                    'aliquota_destino' => $aliquota_destino,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($this->Aliquotas_model->add($data)) {
                    $this->session->set_flashdata('success', 'Alíquota adicionada com sucesso!');
                    redirect(base_url() . 'index.php/aliquotas');
                } else {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar adicionar a alíquota.</div>';
                }
            }
        }

        $this->data['ufs'] = $this->Aliquotas_model->getUFs();
        $this->data['view'] = 'aliquotas/adicionarAliquota';
        return $this->layout();
    }

    public function editar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eAliquota')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar alíquotas.');
            redirect(base_url());
        }

        if ($id == null) {
            $this->session->set_flashdata('error', 'Alíquota não encontrada.');
            redirect(base_url() . 'index.php/aliquotas');
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('uf_origem', 'UF Origem', 'required|trim');
        $this->form_validation->set_rules('uf_destino', 'UF Destino', 'required|trim');
        $this->form_validation->set_rules('aliquota_origem', 'Alíquota Origem', 'required|trim|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('aliquota_destino', 'Alíquota Destino', 'required|trim|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $uf_origem = $this->input->post('uf_origem');
            $uf_destino = $this->input->post('uf_destino');
            $aliquota_origem = $this->input->post('aliquota_origem');
            $aliquota_destino = $this->input->post('aliquota_destino');

            // Verifica se já existe outra alíquota com a mesma combinação de UFs (exceto a atual)
            $aliquota_existente = $this->Aliquotas_model->getAliquotaExceto($uf_origem, $uf_destino, $id);
            if ($aliquota_existente) {
                $this->session->set_flashdata('error', 'Já existe uma alíquota cadastrada para esta combinação de UFs.');
                redirect(current_url());
                return;
            }

            $data = [
                'uf_origem' => $uf_origem,
                'uf_destino' => $uf_destino,
                'aliquota_origem' => $aliquota_origem,
                'aliquota_destino' => $aliquota_destino,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Aliquotas_model->edit($data, $id)) {
                $this->session->set_flashdata('success', 'Alíquota editada com sucesso!');
                redirect(base_url() . 'index.php/aliquotas');
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar editar a alíquota.</div>';
            }
        }

        $this->data['result'] = $this->Aliquotas_model->getById($id);
        if (!$this->data['result']) {
            $this->session->set_flashdata('error', 'Alíquota não encontrada.');
            redirect(base_url() . 'index.php/aliquotas');
        }

        $this->data['ufs'] = $this->Aliquotas_model->getUFs();
        $this->data['view'] = 'aliquotas/editarAliquota';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dAliquota')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir alíquotas.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir alíquota. Parâmetro não encontrado.');
            redirect(base_url() . 'index.php/aliquotas');
            return;
        }

        // Verifica se a alíquota existe
        $aliquota = $this->Aliquotas_model->getById($id);
        if (!$aliquota) {
            $this->session->set_flashdata('error', 'Alíquota não encontrada.');
            redirect(base_url() . 'index.php/aliquotas');
            return;
        }

        // Tenta excluir a alíquota
        if ($this->Aliquotas_model->delete($id)) {
            $this->session->set_flashdata('success', 'Alíquota excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir alíquota.');
        }
        redirect(base_url() . 'index.php/aliquotas');
    }
} 
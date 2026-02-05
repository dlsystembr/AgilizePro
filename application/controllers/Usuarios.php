<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usuarios extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar os usuários.');
            redirect(base_url());
        }

        $this->load->helper('form');
        $this->load->model('usuarios_model');
        $this->data['menuUsuarios'] = 'Usuários';
        $this->data['menuConfiguracoes'] = 'Configurações';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = base_url() . 'index.php/usuarios/gerenciar/';
        $this->data['configuration']['total_rows'] = $this->usuarios_model->count('usuarios');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->usuarios_model->get($this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'usuarios/usuarios';

        return $this->layout();
    }

    public function adicionar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('usuarios') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'usu_nome' => set_value('nome'),
                'usu_email' => set_value('email'),
                'usu_senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'usu_data_expiracao' => set_value('dataExpiracao'),
                'usu_situacao' => set_value('situacao'),
                'usu_data_cadastro' => date('Y-m-d H:i:s'),
                'gre_id' => $this->session->userdata('ten_id'),
            ];

            if ($this->usuarios_model->add('usuarios', $data) == true) {
                $gpu_id = $this->input->post('gpu_id') ? (int) $this->input->post('gpu_id') : null;
                $emp_id = (int) $this->session->userdata('emp_id');
                if ($gpu_id && $emp_id && $this->db->table_exists('grupo_usuario_empresa')) {
                    $usu_id = $this->db->insert_id();
                    $this->db->replace('grupo_usuario_empresa', [
                        'usu_id' => $usu_id,
                        'gpu_id' => $gpu_id,
                        'emp_id' => $emp_id,
                        'uge_data_cadastro' => date('Y-m-d H:i:s'),
                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->session->set_flashdata('success', 'Usuário cadastrado com sucesso!');
                log_info('Adicionou um usuário.');
                redirect(site_url('usuarios/adicionar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $emp_id = (int) $this->session->userdata('emp_id');
        $this->data['grupos'] = [];
        if ($emp_id && $this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $emp_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
        }
        $this->data['view'] = 'usuarios/adicionarUsuario';

        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        $this->form_validation->set_rules('rg', 'rg', 'trim|required');
        $this->form_validation->set_rules('cpf', 'cpf', 'trim|required');
        $this->form_validation->set_rules('cep', 'cep', 'trim|required');
        $this->form_validation->set_rules('rua', 'Rua', 'trim|required');
        $this->form_validation->set_rules('numero', 'Número', 'trim|required');
        $this->form_validation->set_rules('bairro', 'Bairro', 'trim|required');
        $this->form_validation->set_rules('cidade', 'Cidade', 'trim|required');
        $this->form_validation->set_rules('estado', 'Estado', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('telefone', 'Telefone', 'trim|required');
        $this->form_validation->set_rules('situacao', 'Situação', 'trim|required');
        $this->form_validation->set_rules('gpu_id', 'Grupo de usuário', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            if ($this->input->post('idUsuarios') == 1 && $this->input->post('situacao') == 0) {
                $this->session->set_flashdata('error', 'O usuário super admin não pode ser desativado!');
                redirect(base_url() . 'index.php/usuarios/editar/' . $this->input->post('idUsuarios'));
            }

            $senha = $this->input->post('senha');
            if ($senha != null) {
                $senha = password_hash($senha, PASSWORD_DEFAULT);

                $data = [
                    'usu_nome' => $this->input->post('nome'),
                    'usu_email' => $this->input->post('email'),
                    'usu_senha' => $senha,
                    'usu_data_expiracao' => set_value('dataExpiracao'),
                    'usu_situacao' => $this->input->post('situacao'),
                    'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                    'gre_id' => $this->session->userdata('ten_id'),
                ];
            } else {
                $data = [
                    'usu_nome' => $this->input->post('nome'),
                    'usu_email' => $this->input->post('email'),
                    'usu_data_expiracao' => set_value('dataExpiracao'),
                    'usu_situacao' => $this->input->post('situacao'),
                    'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                    'gre_id' => $this->session->userdata('ten_id'),
                ];
            }

            $usu_id = (int) $this->input->post('usu_id');
            if (!$usu_id) {
                $usu_id = (int) $this->input->post('idUsuarios');
            }
            if ($this->usuarios_model->edit('usuarios', $data, 'usu_id', $usu_id) == true) {
                $gpu_id = $this->input->post('gpu_id') ? (int) $this->input->post('gpu_id') : null;
                $emp_id = (int) $this->session->userdata('emp_id');
                if ($gpu_id && $emp_id && $this->db->table_exists('grupo_usuario_empresa')) {
                    $uge = $this->db->get_where('grupo_usuario_empresa', ['usu_id' => $usu_id, 'emp_id' => $emp_id])->row();
                    if ($uge) {
                        $this->db->where('uge_id', $uge->uge_id)->update('grupo_usuario_empresa', [
                            'gpu_id' => $gpu_id,
                            'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                        ]);
                    } else {
                        $this->db->insert('grupo_usuario_empresa', [
                            'usu_id' => $usu_id,
                            'gpu_id' => $gpu_id,
                            'emp_id' => $emp_id,
                            'uge_data_cadastro' => date('Y-m-d H:i:s'),
                            'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
                $this->session->set_flashdata('success', 'Usuário editado com sucesso!');
                log_info('Alterou um usuário. ID: ' . $this->input->post('idUsuarios'));
                redirect(site_url('usuarios/editar/') . $usu_id);
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->usuarios_model->getById($this->uri->segment(3));
        $emp_id = (int) $this->session->userdata('emp_id');
        $this->data['grupos'] = [];
        $this->data['gpu_id_atual'] = null;
        if ($emp_id && $this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $emp_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
            $result = $this->data['result'];
            $usu_id = isset($result->usu_id) ? $result->usu_id : (isset($result->idUsuarios) ? $result->idUsuarios : null);
            if ($usu_id && $this->db->table_exists('grupo_usuario_empresa')) {
                $uge = $this->db->get_where('grupo_usuario_empresa', ['usu_id' => $usu_id, 'emp_id' => $emp_id])->row();
                if ($uge) {
                    $this->data['gpu_id_atual'] = (int) $uge->gpu_id;
                }
            }
        }

        $this->data['view'] = 'usuarios/editarUsuario';

        return $this->layout();
    }

    public function excluir()
    {
        $id = $this->uri->segment(3);
        $this->usuarios_model->delete('usuarios', 'usu_id', $id);

        log_info('Removeu um usuário. ID: ' . $id);

        redirect(site_url('usuarios/gerenciar/'));
    }
}

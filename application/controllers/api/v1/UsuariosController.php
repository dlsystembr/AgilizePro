<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class UsuariosController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('Authorization_Token');
        $this->load->model('Mapos_model');
        $this->load->model('usuarios_model');
    }

    public function index_get($id = '')
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'cUsuario')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Usuários',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $search = trim($this->get('search', true));

            if ($search) {
                $this->load->model('api_model');
                $usuarios = $this->api_model->searchUsuario($search);
            } else {
                $perPage = $this->get('perPage', true) ?: 20;
                $page = $this->get('page', true) ?: 0;
                $start = $page ? ($perPage * $page) : 0;

                $usuarios = $this->usuarios_model->get($perPage, $start);
            }

            if ($usuarios) {
                $this->response([
                    'status' => true,
                    'message' => 'Lista de Usuários',
                    'result' => $usuarios,
                ], REST_Controller::HTTP_OK);
            }
        }

        if ($id && is_numeric($id)) {
            $this->response([
                'status' => true,
                'message' => 'Detalhes do usuário',
                'result' => $this->usuarios_model->getById($id),
            ], REST_Controller::HTTP_OK);
        }

        $this->response([
            'status' => false,
            'message' => 'Nenhum usuário localizado.',
        ], REST_Controller::HTTP_OK);
    }

    public function index_post()
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'cUsuario')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Adicionar Usuários!',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $_POST = (array) json_decode(file_get_contents('php://input'), true);

        $this->load->library('form_validation');

        if ($this->form_validation->run('usuarios') == false) {
            $this->response([
                'status' => false,
                'message' => validation_errors(),
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $user = $this->logged_user();
        $gre_id = isset($user->usuario->gre_id) ? $user->usuario->gre_id : (isset($user->usuario->ten_id) ? $user->usuario->ten_id : null);
        if ($this->db->field_exists('usu_nome', 'usuarios')) {
            $data = [
                'usu_nome' => $this->post('nome', true),
                'usu_email' => $this->post('email', true),
                'usu_senha' => password_hash($this->post('senha', true), PASSWORD_DEFAULT),
                'usu_situacao' => (int) $this->post('situacao', true) ?: 1,
                'usu_data_cadastro' => date('Y-m-d H:i:s'),
                'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                'gre_id' => $gre_id,
            ];
        } else {
            $data = [
                'nome' => $this->post('nome', true),
                'rg' => $this->post('rg', true),
                'cpf' => $this->post('cpf', true),
                'cep' => $this->post('cep', true),
                'rua' => $this->post('rua', true),
                'numero' => $this->post('numero', true),
                'bairro' => $this->post('bairro', true),
                'cidade' => $this->post('cidade', true),
                'estado' => $this->post('estado', true),
                'email' => $this->post('email', true),
                'senha' => password_hash($this->post('senha', true), PASSWORD_DEFAULT),
                'telefone' => $this->post('telefone', true),
                'celular' => $this->post('celular', true),
                'dataExpiracao' => $this->post('dataExpiracao', true),
                'situacao' => $this->post('situacao', true),
                'ten_id' => $gre_id,
            ];
        }

        if ($this->usuarios_model->add('usuarios', $data)) {
            $usu_id = $this->db->insert_id();
            $gpu_id = (int) $this->post('gpu_id', true);
            $emp_id = (int) $this->session->userdata('emp_id');
            if ($gpu_id && $emp_id && $this->db->table_exists('grupo_usuario_empresa')) {
                $this->db->replace('grupo_usuario_empresa', [
                    'usu_id' => $usu_id,
                    'gpu_id' => $gpu_id,
                    'emp_id' => $emp_id,
                    'uge_data_cadastro' => date('Y-m-d H:i:s'),
                    'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                ]);
            }
            $this->load->model('api_model');
            $data = $this->api_model->lastRow('usuarios', 'usu_id');

            $this->response([
                'status' => true,
                'message' => 'Usuário adicionado com sucesso!',
                'result' => $data,
            ], REST_Controller::HTTP_OK);
        }
    }

    public function index_put($id)
    {
        $this->logged_user();

        if (! $id) {
            $this->response([
                'status' => false,
                'message' => 'Informe o ID do Usuário!',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($this->logged_user()->usuario->idUsuarios != $id) {
            if (! $this->permission->checkPermission($this->logged_user()->level, 'cUsuario')) {
                $this->response([
                    'status' => false,
                    'message' => 'Você não está autorizado a Editar Usuários!',
                ], REST_Controller::HTTP_UNAUTHORIZED);
            }
        }

        if (! $this->put(['nome'], true) ||
            ! $this->put(['rg'], true) ||
            ! $this->put(['cpf'], true) ||
            ! $this->put(['cep'], true) ||
            ! $this->put(['rua'], true) ||
            ! $this->put(['numero'], true) ||
            ! $this->put(['bairro'], true) ||
            ! $this->put(['cidade'], true) ||
            ! $this->put(['estado'], true) ||
            ! $this->put(['email'], true) ||
            ! $this->put(['telefone'], true) ||
            ! $this->put(['situacao'], true) ||
            ! $this->put(['gpu_id'], true)
        ) {
            $this->response([
                'status' => false,
                'message' => 'Preencha todos campos',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($id == 1 && $this->put('situacao', true) == 0) {
            $this->response([
                'status' => false,
                'message' => 'error', 'O usuário super admin não pode ser desativado!',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = [
            'usu_nome' => $this->put('nome', true),
            'usu_email' => $this->put('email', true),
            'usu_data_expiracao' => $this->put('dataExpiracao', true) ?: null,
            'usu_situacao' => $this->put('situacao', true),
            'usu_data_atualizacao' => date('Y-m-d H:i:s'),
        ];

        if ($this->put('senha', true)) {
            $data['usu_senha'] = password_hash($this->put('senha', true), PASSWORD_DEFAULT);
        }

        if ($this->usuarios_model->edit('usuarios', $data, 'usu_id', $id)) {
            $gpu_id = (int) $this->put('gpu_id', true);
            $emp_id = (int) $this->session->userdata('emp_id');
            if ($gpu_id && $emp_id && $this->db->table_exists('grupo_usuario_empresa')) {
                $uge = $this->db->get_where('grupo_usuario_empresa', ['usu_id' => $id, 'emp_id' => $emp_id])->row();
                if ($uge) {
                    $this->db->where('uge_id', $uge->uge_id)->update('grupo_usuario_empresa', [
                        'gpu_id' => $gpu_id,
                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->db->insert('grupo_usuario_empresa', [
                        'usu_id' => $id,
                        'gpu_id' => $gpu_id,
                        'emp_id' => $emp_id,
                        'uge_data_cadastro' => date('Y-m-d H:i:s'),
                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            $this->log_app('Alterou um usuário. ID: ' . $id);
            $this->response([
                'status' => true,
                'message' => 'Cliente editado com sucesso!',
                'result' => $this->usuarios_model->getById($id),
            ], REST_Controller::HTTP_OK);
        }

        $this->response([
            'status' => false,
            'message' => 'Não foi possível editar o Usuário.',
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function index_delete($id)
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'cUsuario') || (isset($this->logged_user()->usuario->usu_id) ? $this->logged_user()->usuario->usu_id : $this->logged_user()->usuario->idUsuarios) == $id) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Excluir Usuários!',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $this->response([
                'status' => false,
                'message' => 'Informe o ID do Usuário!',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($id == 1) {
            $this->response([
                'status' => false,
                'message' => 'error', 'O usuário super admin não pode ser deletado!',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->usuarios_model->delete('usuarios', 'usu_id', $id);

        $this->log_app('Removeu um usuário. ID: ' . $id);

        $this->response([
            'status' => true,
            'message' => 'Usuário excluído com sucesso!',
        ], REST_Controller::HTTP_OK);
    }

    /**
     * login function.
     *
     * @return void
     */
    public function login_post()
    {
        $_POST = (array) json_decode(file_get_contents('php://input'), true);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'E-mail', 'valid_email|required|trim');
        $this->form_validation->set_rules('password', 'Senha', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->response([
                'status' => false,
                'message' => strip_tags(validation_errors()),
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $this->load->model('Mapos_model');
        $email = $this->post('email', true);
        $password = $this->post('password', true);
        $user = $this->Mapos_model->check_credentials($email);

        if ($user) {
            // Verificar se acesso está expirado
            if ($this->chk_date($user->dataExpiracao)) {
                $this->response([
                    'status' => false,
                    'message' => 'Os dados de acesso estão incorretos!',
                ], REST_Controller::HTTP_UNAUTHORIZED);
            }

            $senha = isset($user->usu_senha) ? $user->usu_senha : $user->senha;
            $nome = isset($user->usu_nome) ? $user->usu_nome : $user->nome;
            $uid = isset($user->usu_id) ? $user->usu_id : $user->idUsuarios;
            $email = isset($user->usu_email) ? $user->usu_email : $user->email;
            // Verificar credenciais do usuário
            if (password_verify($password, $senha)) {
                $this->log_app('Efetuou login no sistema', $nome);
                // Permissões agora por grupo_usuario_empresa + grupo_usuario_permissoes; API retorna array vazio (compatibilidade)
                $token_data = [
                    'uid' => $uid,
                    'email' => $email,
                    'permissao' => 0,
                ];

                $result = [
                    'access_token' => $this->authorization_token->generateToken($token_data),
                    'permissions' => [],
                ];

                $this->response([
                    'status' => true,
                    'message' => 'Login realizado com sucesso!',
                    'result' => $result,
                ], REST_Controller::HTTP_OK);
            }

            $this->response([
                'status' => false,
                'message' => 'Os dados de acesso estão incorretos!',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $this->response([
            'status' => false,
            'message' => 'Os dados de acesso estão incorretos!',
        ], REST_Controller::HTTP_UNAUTHORIZED);
    }

    /**
     * reGenToken function.
     *
     * @return void
     */
    public function reGenToken_get()
    {
        $user = $this->logged_user(true)->usuario;

        $email = isset($user->usu_email) ? $user->usu_email : $user->email;
        $uid = isset($user->usu_id) ? $user->usu_id : $user->idUsuarios;
        if (! empty($email)) {
            if (! empty($user)) {
                // token regeneration (permissões agora por grupo; API retorna permissions vazio)
                $token_data = [
                    'uid' => $uid,
                    'email' => $email,
                    'permissao' => 0,
                ];

                $result = [
                    'access_token' => $this->authorization_token->generateToken($token_data),
                    'permissions' => [],
                ];

                $this->response([
                    'status' => true,
                    'message' => 'Login realizado com sucesso!',
                    'result' => $result,
                ], REST_Controller::HTTP_OK);
            }

            $this->response([
                'status' => false,
                'message' => 'Usuário não encontrado, verifique se suas credenciais estão corretas!',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $this->response([
            'status' => false,
            'message' => 'O x-api-key expirado é necessário para gerar um novo x-api-key!',
        ], REST_Controller::HTTP_OK);
    }

    public function conta_get()
    {
        $usuarioLogado = $this->logged_user();
        $usuarioLogado->usuario->url_image_user = ! is_null($usuarioLogado->usuario->url_image_user) ? base_url() . 'assets/userImage/' . $usuarioLogado->usuario->url_image_user : null;
        unset($usuarioLogado->usuario->senha);

        $this->response([
            'status' => true,
            'message' => 'Dados do Usuário!',
            'result' => $usuarioLogado,
        ], REST_Controller::HTTP_OK);
    }

    private function chk_date($data_banco)
    {
        $data_banco = new DateTime($data_banco);
        $data_hoje = new DateTime('now');

        return $data_banco < $data_hoje;
    }
}

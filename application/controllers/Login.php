<?php

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mapos_model');
    }

    public function index()
    {
        $this->load->view('mapos/login');
    }

    public function sair()
    {
        $this->session->sess_destroy();

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function verificarLogin()
    {
        header('Access-Control-Allow-Origin: ' . base_url());
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'E-mail', 'valid_email|required|trim');
        $this->form_validation->set_rules('senha', 'Senha', 'required|trim');
        if ($this->form_validation->run() == false) {
            $json = ['result' => false, 'message' => validation_errors()];
            echo json_encode($json);
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('senha');
            
            // Primeiro verificar se é super usuário
            $this->load->model('Usuarios_super_model');
            $super_user = $this->Usuarios_super_model->getByEmail($email);
            
            if ($super_user && password_verify($password, $super_user->USS_SENHA)) {
                // Verificar se acesso está expirado
                if ($super_user->USS_DATA_EXPIRACAO && $this->chk_date($super_user->USS_DATA_EXPIRACAO)) {
                    $json = ['result' => false, 'message' => 'A conta do super usuário está expirada.'];
                    echo json_encode($json);
                    exit();
                }
                
                // Login como super usuário
                $session_super_data = [
                    'nome_admin' => $super_user->USS_NOME,
                    'email_admin' => $super_user->USS_EMAIL,
                    'url_image_user_admin' => $super_user->USS_URL_IMAGE_USER,
                    'id_admin' => $super_user->USS_ID,
                    'logado' => true,
                    'is_super' => true
                ];
                $this->session->set_userdata($session_super_data);
                log_info('Super usuário efetuou login no sistema');
                $json = ['result' => true, 'is_super' => true, 'redirect' => base_url('index.php/super')];
                echo json_encode($json);
                exit();
            }
            
            // Se não for super, verificar usuário normal
            $this->load->model('Mapos_model');
            $user = $this->Mapos_model->check_credentials($email);

            if ($user) {
                // Verificar se acesso está expirado
                if ($this->chk_date($user->dataExpiracao)) {
                    $json = ['result' => false, 'message' => 'A conta do usuário está expirada, por favor entre em contato com o administrador do sistema.'];
                    echo json_encode($json);
                    exit();
                }

                // Verificar credenciais do usuário
                if (password_verify($password, $user->senha)) {
                    $session_admin_data = ['nome_admin' => $user->nome, 'email_admin' => $user->email, 'url_image_user_admin' => $user->url_image_user, 'id_admin' => $user->idUsuarios, 'permissao' => $user->permissoes_id, 'logado' => true, 'ten_id' => $user->ten_id];
                    $this->session->set_userdata($session_admin_data);
                    log_info('Efetuou login no sistema');
                    $json = ['result' => true, 'ten_id' => $user->ten_id];
                    echo json_encode($json);
                } else {
                    $json = ['result' => false, 'message' => 'Os dados de acesso estão incorretos.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
                    echo json_encode($json);
                }
            } else {
                $json = ['result' => false, 'message' => 'Usuário não encontrado, verifique se suas credenciais estão corretass.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
                echo json_encode($json);
            }
        }
        exit();
    }

    private function chk_date($data_banco)
    {
        $data_banco = new DateTime($data_banco);
        $data_hoje = new DateTime('now');

        return $data_banco < $data_hoje;
    }
}

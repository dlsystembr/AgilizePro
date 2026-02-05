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
            // Verificar se a tabela existe
            if ($this->db->table_exists('usuarios_super')) {
                // Buscar super usuário usando query SQL direta para garantir que funcione
                $email_escaped = $this->db->escape($email);
                $sql = "SELECT * FROM usuarios_super WHERE uss_email = {$email_escaped} AND uss_situacao = 1 LIMIT 1";
                $query = $this->db->query($sql);
                $super_user = $query->row();
                
                // Debug: log da query
                log_message('debug', 'Login - Email buscado: ' . $email);
                log_message('debug', 'Login - Query SQL: ' . $sql);
                log_message('debug', 'Login - Super user encontrado: ' . ($super_user ? 'SIM' : 'NÃO'));
                
                if ($super_user) {
                    // Debug: verificar senha
                    log_message('debug', 'Login - Verificando senha...');
                    log_message('debug', 'Login - Email recebido: ' . $email);
                    log_message('debug', 'Login - Senha recebida: ' . $password);
                    // Usar minúsculas para acessar propriedades do objeto
                    $uss_senha = isset($super_user->uss_senha) ? $super_user->uss_senha : (isset($super_user->uss_senha) ? $super_user->uss_senha : '');
                    log_message('debug', 'Login - Hash da senha no banco (completo): ' . $uss_senha);
                    log_message('debug', 'Login - Tamanho do hash: ' . strlen($uss_senha));
                    
                    // Verificar se o hash está vazio ou inválido
                    if (empty($uss_senha)) {
                        log_message('error', 'Login - Hash de senha está vazio!');
                        $json = ['result' => false, 'message' => 'Erro na configuração da senha do super usuário. Contate o administrador.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
                        echo json_encode($json);
                        exit();
                    }
                    
                    // Limpar espaços e caracteres especiais do hash
                    $hash_limpo = trim($uss_senha);
                    
                    // Verificar se o hash tem o formato correto (deve começar com $2y$)
                    if (substr($hash_limpo, 0, 4) !== '$2y$' && substr($hash_limpo, 0, 4) !== '$2a$' && substr($hash_limpo, 0, 4) !== '$2b$') {
                        log_message('error', 'Login - Hash não está no formato bcrypt válido! Início: ' . substr($hash_limpo, 0, 10));
                        $json = ['result' => false, 'message' => 'Formato de senha inválido no banco de dados.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
                        echo json_encode($json);
                        exit();
                    }
                    
                    // Verificar senha
                    $senha_verificada = password_verify($password, $hash_limpo);
                    log_message('debug', 'Login - Senha verificada: ' . ($senha_verificada ? 'SIM' : 'NÃO'));
                    
                    // Se não funcionar, tentar verificar se a senha está em texto plano (para debug)
                    if (!$senha_verificada && $password === $hash_limpo) {
                        log_message('error', 'Login - ATENÇÃO: Senha está em texto plano no banco!');
                    }
                    
                    // Debug adicional: tentar verificar com diferentes variações
                    if (!$senha_verificada) {
                        log_message('debug', 'Login - Tentando verificar com hash limpo: ' . substr($hash_limpo, 0, 30) . '...');
                        // Tentar com trim
                        $senha_verificada = password_verify(trim($password), trim($hash_limpo));
                        log_message('debug', 'Login - Após trim, senha verificada: ' . ($senha_verificada ? 'SIM' : 'NÃO'));
                    }
                    
                    // Verificar senha
                    if ($senha_verificada) {
                        // Verificar se acesso está expirado
                        $uss_data_expiracao = isset($super_user->uss_data_expiracao) ? $super_user->uss_data_expiracao : (isset($super_user->uss_data_expiracao) ? $super_user->uss_data_expiracao : null);
                        if ($uss_data_expiracao && $this->chk_date($uss_data_expiracao)) {
                            $json = ['result' => false, 'message' => 'A conta do super usuário está expirada.'];
                            echo json_encode($json);
                            exit();
                        }
                        
                        // Login como super usuário - usar minúsculas
                        $uss_nome = isset($super_user->uss_nome) ? $super_user->uss_nome : (isset($super_user->uss_nome) ? $super_user->uss_nome : '');
                        $uss_email = isset($super_user->uss_email) ? $super_user->uss_email : (isset($super_user->uss_email) ? $super_user->uss_email : '');
                        $uss_url_image = isset($super_user->uss_url_image_user) ? $super_user->uss_url_image_user : (isset($super_user->uss_url_image_user) ? $super_user->uss_url_image_user : '');
                        $uss_id = isset($super_user->uss_id) ? $super_user->uss_id : (isset($super_user->uss_id) ? $super_user->uss_id : 0);
                        
                        $session_super_data = [
                            'nome_admin' => $uss_nome,
                            'email_admin' => $uss_email,
                            'url_image_user_admin' => $uss_url_image,
                            'id_admin' => $uss_id,
                            'logado' => true,
                            'is_super' => true
                        ];
                        $this->session->set_userdata($session_super_data);
                        log_info('Super usuário efetuou login no sistema');
                        $json = ['result' => true, 'is_super' => true, 'redirect' => base_url('index.php/super')];
                        echo json_encode($json);
                        exit();
                    } else {
                        // Senha incorreta para super usuário - retornar erro específico
                        $json = ['result' => false, 'message' => 'Senha incorreta para super usuário.', 'MAPOS_TOKEN' => $this->security->get_csrf_hash()];
                        echo json_encode($json);
                        exit();
                    }
                } else {
                    // Super usuário não encontrado - continuar para verificar usuário normal
                    log_message('debug', 'Login - Super usuário não encontrado na tabela usuarios_super');
                }
            } else {
                log_message('debug', 'Login - Tabela usuarios_super não existe');
            }
            
            // Se não for super, verificar usuário normal
            $this->load->model('Mapos_model');
            $user = $this->Mapos_model->check_credentials($email);

            if ($user) {
                // Nova estrutura: usu_* e gre_id
                $data_expiracao = isset($user->usu_data_expiracao) ? $user->usu_data_expiracao : (isset($user->dataExpiracao) ? $user->dataExpiracao : null);
                $senha = isset($user->usu_senha) ? $user->usu_senha : (isset($user->senha) ? $user->senha : '');
                $nome = isset($user->usu_nome) ? $user->usu_nome : (isset($user->nome) ? $user->nome : '');
                $email = isset($user->usu_email) ? $user->usu_email : (isset($user->email) ? $user->email : '');
                $url_image = isset($user->usu_url_imagem) ? $user->usu_url_imagem : (isset($user->url_image_user) ? $user->url_image_user : '');
                $id_usuarios = isset($user->usu_id) ? $user->usu_id : (isset($user->idUsuarios) ? $user->idUsuarios : 0);
                $gre_id = isset($user->gre_id) ? $user->gre_id : (isset($user->ten_id) ? $user->ten_id : 0);
                $ten_id = $gre_id; // compatibilidade: sessão mantém ten_id = gre_id

                // Verificar se acesso está expirado
                if ($data_expiracao && $this->chk_date($data_expiracao)) {
                    $json = ['result' => false, 'message' => 'A conta do usuário está expirada, por favor entre em contato com o administrador do sistema.'];
                    echo json_encode($json);
                    exit();
                }

                // Verificar credenciais do usuário (permissões agora por grupo_usuario_empresa; permissao na sessão mantido por compatibilidade)
                if ($senha && password_verify($password, $senha)) {
                    $emp_id = null;
                    if ($this->db->table_exists('empresas')) {
                        $emp = $this->db->select('emp_id')->from('empresas')->where('gre_id', $gre_id)->limit(1)->get()->row();
                        if (!$emp && $this->db->field_exists('ten_id', 'empresas')) {
                            $emp = $this->db->select('emp_id')->from('empresas')->where('ten_id', $gre_id)->limit(1)->get()->row();
                        }
                        $emp_id = $emp ? (int) $emp->emp_id : null;
                    }
                    $session_admin_data = [
                        'nome_admin' => $nome,
                        'email_admin' => $email,
                        'url_image_user_admin' => $url_image,
                        'id_admin' => $id_usuarios,
                        'permissao' => 0,
                        'logado' => true,
                        'ten_id' => $ten_id,
                        'emp_id' => $emp_id
                    ];
                    $this->session->set_userdata($session_admin_data);
                    log_info('Efetuou login no sistema');
                    $json = ['result' => true, 'ten_id' => $ten_id];
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

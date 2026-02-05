<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Super extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Verificar se é super usuário
        if (!$this->session->userdata('is_super')) {
            redirect('login');
        }

        $this->load->model('Usuarios_super_model');
        $this->load->model('Usuarios_model');
    }

    public function index()
    {
        // Dashboard do painel super
        $this->data['total_tenants'] = $this->db->count_all('tenants');
        $this->data['total_usuarios'] = $this->db->count_all('usuarios');
        $this->data['total_super_usuarios'] = $this->db->count_all('usuarios_super');

        // Grupos empresariais e empresas (nova estrutura)
        $this->data['total_grupos_empresariais'] = $this->db->table_exists('grupos_empresariais') ? $this->db->count_all('grupos_empresariais') : 0;
        $this->data['total_empresas'] = $this->db->table_exists('empresas') ? $this->db->count_all('empresas') : 0;

        // Listar últimos tenants (legado)
        $this->db->order_by('ten_data_cadastro', 'DESC');
        $this->db->limit(10);
        $this->data['ultimos_tenants'] = $this->db->get('tenants')->result();

        // Listar últimos grupos empresariais
        if ($this->db->table_exists('grupos_empresariais')) {
            $this->db->order_by('gre_data_cadastro', 'DESC');
            $this->db->limit(10);
            $this->data['ultimos_grupos_empresariais'] = $this->db->get('grupos_empresariais')->result();
        } else {
            $this->data['ultimos_grupos_empresariais'] = [];
        }

        $this->data['view'] = 'super/dashboard';
        $this->load->view('super/layout', $this->data);
    }

    /**
     * Proxy para busca de CNPJ na API pública (publica.cnpj.ws).
     * Usado pelo formulário de empresas no Super para preencher dados pelo CNPJ.
     * Uso: GET index.php/super/buscarCnpjApi/24982773000189 ou ?cnpj=24982773000189
     */
    public function buscarCnpjApi()
    {
        header('Content-Type: application/json; charset=utf-8');
        $cnpj = $this->input->get('cnpj') ?: $this->uri->segment(3);
        $cnpj = preg_replace('/\D/', '', (string) $cnpj);
        if (strlen($cnpj) !== 14) {
            echo json_encode(['erro' => 'CNPJ inválido. Informe 14 dígitos.']);
            return;
        }
        $url = 'https://publica.cnpj.ws/cnpj/' . $cnpj;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            echo json_encode(['erro' => 'Erro ao conectar na API: ' . $err]);
            return;
        }
        if ($httpCode !== 200) {
            echo json_encode(['erro' => 'API retornou status ' . $httpCode, 'razao_social' => null]);
            return;
        }
        echo $response ?: json_encode(['erro' => 'Resposta vazia da API']);
    }

    // ========== GERENCIAR TENANTS ==========
    public function tenants()
    {
        $this->load->library('pagination');

        $pesquisa = $this->input->get('pesquisa');
        $per_page = 20;
        $page = $this->input->get('page') ?: 1;
        $start = ($page - 1) * $per_page;

        $this->data['configuration']['base_url'] = base_url('index.php/super/tenants');
        $this->data['configuration']['total_rows'] = $this->db->count_all('tenants');
        $this->data['configuration']['per_page'] = $per_page;
        $this->data['configuration']['use_page_numbers'] = TRUE;
        $this->data['configuration']['page_query_string'] = TRUE;
        $this->data['configuration']['query_string_segment'] = 'page';

        $this->pagination->initialize($this->data['configuration']);

        $this->db->select('*');
        $this->db->from('tenants');
        if ($pesquisa) {
            $this->db->group_start();
            $this->db->like('ten_nome', $pesquisa);
            $this->db->or_like('ten_cnpj', $pesquisa);
            $this->db->or_like('ten_email', $pesquisa);
            $this->db->group_end();
        }
        $this->db->order_by('ten_data_cadastro', 'DESC');
        $this->db->limit($per_page, $start);

        $this->data['results'] = $this->db->get()->result();
        $this->data['search'] = $pesquisa;
        $this->data['view'] = 'super/tenants';
        $this->load->view('super/layout', $this->data);
    }

    public function adicionarTenant()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('ten_nome', 'Nome do Tenant', 'required|trim');
        $this->form_validation->set_rules('ten_cnpj', 'cnpj', 'trim');
        $this->form_validation->set_rules('ten_email', 'E-mail', 'valid_email|trim');
        $this->form_validation->set_rules('ten_telefone', 'Telefone', 'trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'ten_nome' => $this->input->post('ten_nome'),
                'ten_cnpj' => $this->input->post('ten_cnpj'),
                'ten_email' => $this->input->post('ten_email'),
                'ten_telefone' => $this->input->post('ten_telefone'),
                'ten_data_cadastro' => date('Y-m-d H:i:s'),
            ];

            if ($this->db->insert('tenants', $data)) {
                $ten_id = $this->db->insert_id();

                // Criar usuário se os campos foram preenchidos (sistema usa grupos de usuário; sem tabela permissoes)
                $usuario_nome = $this->input->post('usuario_nome');
                $usuario_email = $this->input->post('usuario_email');
                $usuario_senha = $this->input->post('usuario_senha');

                if (!empty($usuario_nome) && !empty($usuario_email) && !empty($usuario_senha)) {
                    $col_gre = $this->db->field_exists('gre_id', 'usuarios') ? 'gre_id' : 'ten_id';
                    $usuario_data = [
                        'usu_nome' => $usuario_nome,
                        'usu_email' => $usuario_email,
                        'usu_senha' => password_hash($usuario_senha, PASSWORD_DEFAULT),
                        'usu_situacao' => 1,
                        'usu_data_cadastro' => date('Y-m-d H:i:s'),
                        'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                        'usu_data_expiracao' => '3000-01-01',
                        $col_gre => $ten_id,
                    ];
                    if (!$this->db->field_exists('usu_nome', 'usuarios')) {
                        $usuario_data = [
                            'nome' => $usuario_nome,
                            'email' => $usuario_email,
                            'senha' => password_hash($usuario_senha, PASSWORD_DEFAULT),
                            'situacao' => 1,
                            'dataCadastro' => date('Y-m-d'),
                            'dataExpiracao' => '3000-01-01',
                            'ten_id' => $ten_id,
                        ];
                    }

                    if ($this->db->insert('usuarios', $usuario_data)) {
                        $usu_id = $this->db->insert_id();
                        // Vincular a um grupo padrão da primeira empresa do tenant (se existir grupo_usuario e empresas)
                        if ($this->db->table_exists('grupo_usuario_empresa') && $this->db->table_exists('grupo_usuario') && $this->db->table_exists('empresas')) {
                            $emp = $this->db->select('emp_id')->from('empresas')->where('gre_id', $ten_id)->limit(1)->get()->row();
                            if ($emp) {
                                $gpu = $this->db->select('gpu_id')->from('grupo_usuario')->where('emp_id', $emp->emp_id)->where('gpu_situacao', 1)->limit(1)->get()->row();
                                if ($gpu) {
                                    $this->db->insert('grupo_usuario_empresa', [
                                        'usu_id' => $usu_id,
                                        'gpu_id' => $gpu->gpu_id,
                                        'emp_id' => $emp->emp_id,
                                        'uge_data_cadastro' => date('Y-m-d H:i:s'),
                                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                                    ]);
                                }
                            }
                        }
                        $this->session->set_flashdata('success', 'Tenant adicionado com sucesso! Usuário administrador criado.');
                        log_info('Super usuário adicionou um tenant com usuário. Tenant ID: ' . $ten_id . ', Usuário: ' . $usuario_email);
                    } else {
                        $this->session->set_flashdata('success', 'Tenant adicionado com sucesso! Erro ao criar usuário.');
                        log_info('Super usuário adicionou um tenant. ID: ' . $ten_id . ' (erro ao criar usuário)');
                    }
                } else {
                    $this->session->set_flashdata('success', 'Tenant adicionado com sucesso!');
                    log_info('Super usuário adicionou um tenant. ID: ' . $ten_id);
                }

                redirect(base_url('index.php/super/tenants'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'super/adicionarTenant';
        $this->load->view('super/layout', $this->data);
    }

    public function editarTenant($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Tenant não encontrado.');
            redirect(base_url('index.php/super/tenants'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('ten_nome', 'Nome do Tenant', 'required|trim');
        $this->form_validation->set_rules('ten_cnpj', 'cnpj', 'trim');
        $this->form_validation->set_rules('ten_email', 'E-mail', 'valid_email|trim');
        $this->form_validation->set_rules('ten_telefone', 'Telefone', 'trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'ten_nome' => $this->input->post('ten_nome'),
                'ten_cnpj' => $this->input->post('ten_cnpj'),
                'ten_email' => $this->input->post('ten_email'),
                'ten_telefone' => $this->input->post('ten_telefone'),
            ];

            $this->db->where('ten_id', $id);
            if ($this->db->update('tenants', $data)) {
                $this->session->set_flashdata('success', 'Tenant editado com sucesso!');
                log_info('Super usuário editou um tenant. ID ' . $id);
                redirect(base_url('index.php/super/tenants'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->db->get_where('tenants', ['ten_id' => $id])->row();
        $this->data['view'] = 'super/editarTenant';
        $this->load->view('super/layout', $this->data);
    }

    public function excluirTenant()
    {
        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir tenant.');
            redirect(site_url('super/tenants/'));
        }

        // Verificar se há registros vinculados
        $tables = ['usuarios', 'clientes', 'produtos', 'vendas', 'os'];
        $has_records = false;
        foreach ($tables as $table) {
            $count = $this->db->where('ten_id', $id)->count_all_results($table);
            if ($count > 0) {
                $has_records = true;
                break;
            }
        }

        if ($has_records) {
            $this->session->set_flashdata('error', 'Não é possível excluir o tenant pois existem registros vinculados a ele.');
            redirect(site_url('super/tenants/'));
        }

        $this->db->where('ten_id', $id);
        $this->db->delete('tenants');
        log_info('Super usuário removeu um tenant. ID ' . $id);

        $this->session->set_flashdata('success', 'Tenant excluído com sucesso!');
        redirect(site_url('super/tenants/'));
    }

    // ========== GRUPOS EMPRESARIAIS (id + nome + datas) ==========
    public function gruposEmpresariais()
    {
        $this->load->library('pagination');

        $pesquisa = $this->input->get('pesquisa');
        $per_page = 20;
        $page = $this->input->get('page') ?: 1;
        $start = ($page - 1) * $per_page;

        $this->data['configuration']['base_url'] = base_url('index.php/super/gruposEmpresariais');
        $this->data['configuration']['total_rows'] = $this->db->count_all('grupos_empresariais');
        $this->data['configuration']['per_page'] = $per_page;
        $this->data['configuration']['use_page_numbers'] = TRUE;
        $this->data['configuration']['page_query_string'] = TRUE;
        $this->data['configuration']['query_string_segment'] = 'page';

        $this->pagination->initialize($this->data['configuration']);

        $this->db->select('*');
        $this->db->from('grupos_empresariais');
        if ($pesquisa) {
            $this->db->like('gre_nome', $pesquisa);
        }
        $this->db->order_by('gre_data_cadastro', 'DESC');
        $this->db->limit($per_page, $start);

        $this->data['results'] = $this->db->get()->result();
        $this->data['search'] = $pesquisa;
        $this->data['view'] = 'super/gruposEmpresariais';
        $this->load->view('super/layout', $this->data);
    }

    public function adicionarGrupoEmpresarial()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('gre_nome', 'Nome do Grupo Empresarial', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $agora = date('Y-m-d H:i:s');
            $data = [
                'gre_nome' => $this->input->post('gre_nome'),
                'gre_situacao' => $this->input->post('gre_situacao') ? 1 : 0,
                'gre_data_cadastro' => $agora,
                'gre_data_atualizacao' => $agora,
            ];

            if ($this->db->insert('grupos_empresariais', $data)) {
                $gre_id = $this->db->insert_id();
                $this->session->set_flashdata('success', 'Grupo empresarial adicionado com sucesso!');
                log_info('Super usuário adicionou grupo empresarial. ID: ' . $gre_id);
                redirect(base_url('index.php/super/gruposEmpresariais'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'super/adicionarGrupoEmpresarial';
        $this->load->view('super/layout', $this->data);
    }

    public function editarGrupoEmpresarial($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('gre_nome', 'Nome do Grupo Empresarial', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'gre_nome' => $this->input->post('gre_nome'),
                'gre_situacao' => $this->input->post('gre_situacao') ? 1 : 0,
                'gre_data_atualizacao' => date('Y-m-d H:i:s'),
            ];

            $this->db->where('gre_id', $id);
            if ($this->db->update('grupos_empresariais', $data)) {
                $this->session->set_flashdata('success', 'Grupo empresarial editado com sucesso!');
                log_info('Super usuário editou grupo empresarial. ID ' . $id);
                redirect(base_url('index.php/super/gruposEmpresariais'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->db->get_where('grupos_empresariais', ['gre_id' => $id])->row();
        $this->data['view'] = 'super/editarGrupoEmpresarial';
        $this->load->view('super/layout', $this->data);
    }

    public function excluirGrupoEmpresarial()
    {
        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir grupo empresarial.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $count = $this->db->where('gre_id', $id)->count_all_results('empresas');
        if ($count > 0) {
            $this->session->set_flashdata('error', 'Não é possível excluir o grupo pois existem empresas vinculadas. Exclua as empresas primeiro.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $this->db->where('gre_id', $id);
        $this->db->delete('grupos_empresariais');
        log_info('Super usuário removeu grupo empresarial. ID ' . $id);

        $this->session->set_flashdata('success', 'Grupo empresarial excluído com sucesso!');
        redirect(base_url('index.php/super/gruposEmpresariais'));
    }

    // ========== EMPRESAS (dentro do grupo) ==========
    public function empresas($grupo_id = null)
    {
        if ($grupo_id == null) {
            $this->session->set_flashdata('error', 'Grupo empresarial não informado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $this->load->library('pagination');

        $pesquisa = $this->input->get('pesquisa');
        $per_page = 20;
        $page = $this->input->get('page') ?: 1;
        $start = ($page - 1) * $per_page;

        $this->data['configuration']['base_url'] = base_url("index.php/super/empresas/{$grupo_id}");
        $this->db->where('gre_id', $grupo_id);
        $this->data['configuration']['total_rows'] = $this->db->count_all_results('empresas');
        $this->data['configuration']['per_page'] = $per_page;
        $this->data['configuration']['use_page_numbers'] = TRUE;
        $this->data['configuration']['page_query_string'] = TRUE;
        $this->data['configuration']['query_string_segment'] = 'page';

        $this->pagination->initialize($this->data['configuration']);

        $this->db->select('*');
        $this->db->from('empresas');
        $this->db->where('gre_id', $grupo_id);
        if ($pesquisa) {
            $this->db->group_start();
            $this->db->like('emp_razao_social', $pesquisa);
            $this->db->or_like('emp_nome_fantasia', $pesquisa);
            $this->db->or_like('emp_cnpj', $pesquisa);
            $this->db->or_like('emp_email', $pesquisa);
            $this->db->group_end();
        }
        $this->db->order_by('emp_data_cadastro', 'DESC');
        $this->db->limit($per_page, $start);

        $this->data['results'] = $this->db->get()->result();
        $this->data['grupo'] = $grupo;
        $this->data['search'] = $pesquisa;
        $this->data['view'] = 'super/empresas';
        $this->load->view('super/layout', $this->data);
    }

    public function adicionarEmpresa($grupo_id = null)
    {
        if ($grupo_id == null) {
            $this->session->set_flashdata('error', 'Grupo empresarial não informado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('emp_cnpj', 'CNPJ', 'required|trim');
        $this->form_validation->set_rules('emp_razao_social', 'Razão Social', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $logoPath = '';
            if (!empty($_FILES['userfile']['name'])) {
                $this->load->library('upload');
                $config['upload_path'] = './assets/logos/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = true;
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

            $agora = date('Y-m-d H:i:s');
            $ten_id = $this->input->post('ten_id') ?: 1;
            $data = [
                'gre_id' => $grupo_id,
                'ten_id' => $ten_id,
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
                'emp_ativo' => $this->input->post('emp_ativo') ? 1 : 0,
                'emp_data_cadastro' => $agora,
                'emp_data_atualizacao' => $agora,
            ];

            if ($this->db->insert('empresas', $data)) {
                $emp_id = $this->db->insert_id();
                $men_ids = $this->input->post('men_id');
                if (is_array($men_ids) && $this->db->table_exists('menu_empresa')) {
                    $agora = date('Y-m-d H:i:s');
                    foreach ($men_ids as $men_id) {
                        $men_id = (int) $men_id;
                        if ($men_id > 0) {
                            $this->db->insert('menu_empresa', [
                                'emp_id' => $emp_id,
                                'men_id' => $men_id,
                                'mep_data_cadastro' => $agora,
                            ]);
                        }
                    }
                }
                $this->session->set_flashdata('success', 'Empresa adicionada com sucesso!');
                log_info('Super usuário adicionou empresa ao grupo ' . $grupo_id);
                redirect(base_url("index.php/super/empresas/{$grupo_id}"));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['grupo'] = $grupo;
        $this->data['estados'] = $this->db->table_exists('estados') ? $this->db->order_by('est_uf', 'ASC')->get('estados')->result() : [];
        $this->data['menus'] = $this->db->table_exists('menus') ? $this->db->order_by('men_ordem', 'ASC')->get_where('menus', ['men_situacao' => 1])->result() : [];
        $this->data['view'] = 'super/adicionarEmpresa';
        $this->load->view('super/layout', $this->data);
    }

    public function editarEmpresa($grupo_id = null, $empresa_id = null)
    {
        if ($grupo_id == null || $empresa_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('emp_cnpj', 'CNPJ', 'required|trim');
        $this->form_validation->set_rules('emp_razao_social', 'Razão Social', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $logoPath = $this->input->post('EMP_LOGO_PATH_ATUAL');
            if (!empty($_FILES['userfile']['name'])) {
                $this->load->library('upload');
                $config['upload_path'] = './assets/logos/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = true;
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0755, true);
                }
                $this->upload->initialize($config);
                if ($this->upload->do_upload('userfile')) {
                    $logoAntigo = $this->input->post('EMP_LOGO_PATH_ATUAL');
                    if ($logoAntigo && file_exists(FCPATH . $logoAntigo)) {
                        @unlink(FCPATH . $logoAntigo);
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
                'emp_data_atualizacao' => date('Y-m-d H:i:s'),
            ];
            $data['emp_ativo'] = $this->input->post('emp_ativo') ? 1 : 0;

            $this->db->where('emp_id', $empresa_id);
            $this->db->where('gre_id', $grupo_id);
            if ($this->db->update('empresas', $data)) {
                if ($this->db->table_exists('menu_empresa')) {
                    $men_ids = $this->input->post('men_id');
                    // Só atualizar menus se o formulário enviou men_id (evita limpar permissões quando men_id não vem no POST)
                    if (is_array($men_ids)) {
                        $this->db->where('emp_id', $empresa_id);
                        $this->db->delete('menu_empresa');
                        $agora = date('Y-m-d H:i:s');
                        foreach ($men_ids as $men_id) {
                            $men_id = (int) $men_id;
                            if ($men_id > 0) {
                                $this->db->insert('menu_empresa', [
                                    'emp_id' => $empresa_id,
                                    'men_id' => $men_id,
                                    'mep_data_cadastro' => $agora,
                                ]);
                            }
                        }
                    }
                }
                $this->session->set_flashdata('success', 'Empresa editada com sucesso!');
                log_info('Super usuário editou empresa ' . $empresa_id . ' do grupo ' . $grupo_id);
                redirect(base_url("index.php/super/empresas/{$grupo_id}"));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        $this->data['grupo'] = $grupo;
        $this->data['estados'] = $this->db->table_exists('estados') ? $this->db->order_by('est_uf', 'ASC')->get('estados')->result() : [];
        $this->data['menus'] = $this->db->table_exists('menus') ? $this->db->order_by('men_ordem', 'ASC')->get_where('menus', ['men_situacao' => 1])->result() : [];
        $menus_empresa = [];
        if ($this->db->table_exists('menu_empresa')) {
            $rows = $this->db->select('men_id')->get_where('menu_empresa', ['emp_id' => $empresa_id])->result();
            foreach ($rows as $r) {
                $menus_empresa[] = (int) $r->men_id;
            }
        }
        $this->data['menus_empresa'] = $menus_empresa;
        $this->data['view'] = 'super/editarEmpresa';
        $this->load->view('super/layout', $this->data);
    }

    /**
     * Tela para configurar os menus permitidos para uma empresa (acessível pela listagem de empresas).
     */
    public function menusEmpresa($grupo_id = null, $empresa_id = null)
    {
        if ($grupo_id == null || $empresa_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $empresa = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        if (!$empresa) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url("index.php/super/empresas/{$grupo_id}"));
        }

        $this->data['grupo'] = $grupo;
        $this->data['empresa'] = $empresa;
        $this->data['menus'] = $this->db->table_exists('menus') ? $this->db->order_by('men_ordem', 'ASC')->get_where('menus', ['men_situacao' => 1])->result() : [];
        $menus_empresa = [];
        if ($this->db->table_exists('menu_empresa')) {
            $rows = $this->db->select('men_id')->get_where('menu_empresa', ['emp_id' => $empresa_id])->result();
            foreach ($rows as $r) {
                $menus_empresa[] = (int) $r->men_id;
            }
        }
        $this->data['menus_empresa'] = $menus_empresa;
        $this->data['view'] = 'super/menusEmpresa';
        $this->load->view('super/layout', $this->data);
    }

    /**
     * Salva os menus permitidos para a empresa (POST da tela menusEmpresa).
     */
    public function salvarMenusEmpresa($grupo_id = null, $empresa_id = null)
    {
        if ($grupo_id == null || $empresa_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        $empresa = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        if (!$empresa) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url("index.php/super/empresas/{$grupo_id}"));
        }

        if ($this->db->table_exists('menu_empresa')) {
            $men_ids = $this->input->post('men_id');
            // Só substituir quando men_id foi enviado (array); se não veio no POST, não limpar o que já estava salvo
            if (is_array($men_ids)) {
                $this->db->where('emp_id', $empresa_id);
                $this->db->delete('menu_empresa');
                $agora = date('Y-m-d H:i:s');
                foreach ($men_ids as $men_id) {
                    $men_id = (int) $men_id;
                    if ($men_id > 0) {
                        $this->db->insert('menu_empresa', [
                            'emp_id' => $empresa_id,
                            'men_id' => $men_id,
                            'mep_data_cadastro' => $agora,
                        ]);
                    }
                }
            }
        }

        $this->session->set_flashdata('success', 'Menus da empresa atualizados com sucesso!');
        redirect(base_url("index.php/super/empresas/{$grupo_id}"));
    }

    public function excluirEmpresa()
    {
        $id = $this->input->post('id');
        $grupo_id = $this->input->post('grupo_id');
        if ($id == null || $grupo_id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir empresa.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }

        // Desvincula do grupo (gre_id = null) em vez de excluir, para não quebrar referências
        $this->db->where('emp_id', $id);
        $this->db->where('gre_id', $grupo_id);
        $this->db->update('empresas', ['gre_id' => null, 'emp_data_atualizacao' => date('Y-m-d H:i:s')]);
        log_info('Super usuário removeu empresa do grupo. ID ' . $id . ' do grupo ' . $grupo_id);

        $this->session->set_flashdata('success', 'Empresa removida do grupo com sucesso!');
        redirect(base_url("index.php/super/empresas/{$grupo_id}"));
    }

    // ========== GERENCIAR USUÁRIOS DA EMPRESA ==========
    /**
     * Lista usuários vinculados à empresa (grupo_usuario_empresa.emp_id).
     */
    public function usuariosEmpresa($grupo_id = null, $empresa_id = null)
    {
        if ($grupo_id == null || $empresa_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $empresa = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        if (!$empresa) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url("index.php/super/empresas/{$grupo_id}"));
        }

        $pesquisa = $this->input->get('pesquisa');
        $per_page = 20;
        $page = $this->input->get('page') ?: 1;
        $start = ($page - 1) * $per_page;

        $col_nome = $this->db->field_exists('usu_nome', 'usuarios') ? 'usu_nome' : 'nome';
        $col_email = $this->db->field_exists('usu_email', 'usuarios') ? 'usu_email' : 'email';

        $this->db->from('usuarios');
        if ($this->db->table_exists('grupo_usuario_empresa') && $this->db->table_exists('grupo_usuario')) {
            $this->db->join('grupo_usuario_empresa', 'grupo_usuario_empresa.usu_id = usuarios.usu_id AND grupo_usuario_empresa.emp_id = ' . (int) $empresa_id, 'inner');
            $this->db->join('grupo_usuario', 'grupo_usuario.gpu_id = grupo_usuario_empresa.gpu_id', 'left');
        }
        $this->db->where('usuarios.gre_id', $grupo_id);
        if ($pesquisa) {
            $this->db->group_start();
            $this->db->like('usuarios.' . $col_nome, $pesquisa);
            $this->db->or_like('usuarios.' . $col_email, $pesquisa);
            $this->db->group_end();
        }
        $total = $this->db->count_all_results('', true);

        $this->db->select('usuarios.*');
        if ($this->db->table_exists('grupo_usuario_empresa') && $this->db->table_exists('grupo_usuario')) {
            $this->db->select('grupo_usuario.gpu_nome as permissao, grupo_usuario_empresa.gpu_id as gpu_id');
            $this->db->join('grupo_usuario_empresa', 'grupo_usuario_empresa.usu_id = usuarios.usu_id AND grupo_usuario_empresa.emp_id = ' . (int) $empresa_id, 'inner');
            $this->db->join('grupo_usuario', 'grupo_usuario.gpu_id = grupo_usuario_empresa.gpu_id', 'left');
        }
        $this->db->from('usuarios');
        $this->db->where('usuarios.gre_id', $grupo_id);
        if ($pesquisa) {
            $this->db->group_start();
            $this->db->like('usuarios.' . $col_nome, $pesquisa);
            $this->db->or_like('usuarios.' . $col_email, $pesquisa);
            $this->db->group_end();
        }
        $this->db->order_by('usuarios.' . $col_nome, 'ASC');
        $this->db->limit($per_page, $start);
        $this->data['results'] = $this->db->get()->result();

        $this->load->library('pagination');
        $this->data['configuration'] = [
            'base_url' => base_url("index.php/super/usuariosEmpresa/{$grupo_id}/{$empresa_id}"),
            'total_rows' => $total,
            'per_page' => $per_page,
            'use_page_numbers' => true,
            'page_query_string' => true,
            'query_string_segment' => 'page',
        ];
        $this->pagination->initialize($this->data['configuration']);

        $this->data['grupo'] = $grupo;
        $this->data['empresa'] = $empresa;
        $this->data['search'] = $pesquisa;
        $this->data['view'] = 'super/usuariosEmpresa';
        $this->load->view('super/layout', $this->data);
    }

    /**
     * Formulário para adicionar usuário à empresa (POST salva usuário + vínculo grupo_usuario_empresa).
     */
    public function adicionarUsuarioEmpresa($grupo_id = null, $empresa_id = null)
    {
        if ($grupo_id == null || $empresa_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $empresa = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        if (!$empresa) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url("index.php/super/empresas/{$grupo_id}"));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|trim');
        $this->form_validation->set_rules('senha', 'Senha', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('gpu_id', 'Grupo de usuário', 'required|trim');

        if ($this->form_validation->run() !== false) {
            $col_gre = $this->db->field_exists('gre_id', 'usuarios') ? 'gre_id' : 'ten_id';
            $data = [
                'usu_nome' => $this->input->post('nome'),
                'usu_email' => $this->input->post('email'),
                'usu_senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'usu_situacao' => (int) $this->input->post('situacao'),
                'usu_data_cadastro' => date('Y-m-d H:i:s'),
                'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                'usu_data_expiracao' => $this->input->post('dataExpiracao') ?: null,
                $col_gre => $grupo_id,
            ];
            if (!$this->db->field_exists('usu_nome', 'usuarios')) {
                $data = [
                    'nome' => $this->input->post('nome'),
                    'email' => $this->input->post('email'),
                    'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                    'situacao' => (int) $this->input->post('situacao'),
                    'dataExpiracao' => $this->input->post('dataExpiracao'),
                    'dataCadastro' => date('Y-m-d'),
                    'gre_id' => $grupo_id,
                ];
            }
            $this->db->insert('usuarios', $data);
            if ($this->db->affected_rows() === 1) {
                $usu_id = $this->db->insert_id();
                $gpu_id = (int) $this->input->post('gpu_id');
                if ($gpu_id && $this->db->table_exists('grupo_usuario_empresa')) {
                    $this->db->insert('grupo_usuario_empresa', [
                        'usu_id' => $usu_id,
                        'gpu_id' => $gpu_id,
                        'emp_id' => $empresa_id,
                        'uge_data_cadastro' => date('Y-m-d H:i:s'),
                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->session->set_flashdata('success', 'Usuário cadastrado com sucesso!');
                log_info('Super usuário adicionou usuário à empresa ' . $empresa_id . ' do grupo ' . $grupo_id);
                redirect(base_url("index.php/super/usuariosEmpresa/{$grupo_id}/{$empresa_id}"));
            }
            $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro ao cadastrar.</p></div>';
        }

        $this->data['grupos'] = [];
        if ($this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $empresa_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
        }
        $this->data['grupo'] = $grupo;
        $this->data['empresa'] = $empresa;
        $this->data['view'] = 'super/adicionarUsuarioEmpresa';
        $this->load->view('super/layout', $this->data);
    }

    /**
     * Formulário para editar usuário da empresa (POST atualiza usuario + grupo_usuario_empresa para esta empresa).
     */
    public function editarUsuarioEmpresa($grupo_id = null, $empresa_id = null, $usuario_id = null)
    {
        if ($grupo_id == null || $empresa_id == null || $usuario_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $empresa = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        if (!$empresa) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url("index.php/super/empresas/{$grupo_id}"));
        }
        $usuario = $this->db->get_where('usuarios', ['usu_id' => $usuario_id, 'gre_id' => $grupo_id])->row();
        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuário não encontrado.');
            redirect(base_url("index.php/super/usuariosEmpresa/{$grupo_id}/{$empresa_id}"));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|trim');
        $this->form_validation->set_rules('gpu_id', 'Grupo de usuário', 'required|trim');

        if ($this->form_validation->run() !== false) {
            $data = [
                'usu_nome' => $this->input->post('nome'),
                'usu_email' => $this->input->post('email'),
                'usu_situacao' => (int) $this->input->post('situacao'),
                'usu_data_expiracao' => $this->input->post('dataExpiracao') ?: null,
                'usu_data_atualizacao' => date('Y-m-d H:i:s'),
            ];
            $senha = $this->input->post('senha');
            if ($senha !== null && $senha !== '') {
                $data['usu_senha'] = password_hash($senha, PASSWORD_DEFAULT);
            }
            $this->db->where('usu_id', $usuario_id);
            $this->db->update('usuarios', $data);

            $gpu_id = (int) $this->input->post('gpu_id');
            if ($gpu_id && $this->db->table_exists('grupo_usuario_empresa')) {
                $uge = $this->db->get_where('grupo_usuario_empresa', ['usu_id' => $usuario_id, 'emp_id' => $empresa_id])->row();
                if ($uge) {
                    $this->db->where('uge_id', $uge->uge_id)->update('grupo_usuario_empresa', [
                        'gpu_id' => $gpu_id,
                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->db->insert('grupo_usuario_empresa', [
                        'usu_id' => $usuario_id,
                        'gpu_id' => $gpu_id,
                        'emp_id' => $empresa_id,
                        'uge_data_cadastro' => date('Y-m-d H:i:s'),
                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            $this->session->set_flashdata('success', 'Usuário atualizado com sucesso!');
            log_info('Super usuário editou usuário ' . $usuario_id . ' da empresa ' . $empresa_id);
            redirect(base_url("index.php/super/usuariosEmpresa/{$grupo_id}/{$empresa_id}"));
        }

        $this->data['grupos'] = [];
        $this->data['gpu_id_atual'] = null;
        if ($this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $empresa_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
            if ($this->db->table_exists('grupo_usuario_empresa')) {
                $uge = $this->db->get_where('grupo_usuario_empresa', ['usu_id' => $usuario_id, 'emp_id' => $empresa_id])->row();
                if ($uge) {
                    $this->data['gpu_id_atual'] = (int) $uge->gpu_id;
                }
            }
        }
        $this->data['result'] = $usuario;
        $this->data['grupo'] = $grupo;
        $this->data['empresa'] = $empresa;
        $this->data['view'] = 'super/editarUsuarioEmpresa';
        $this->load->view('super/layout', $this->data);
    }

    /**
     * Tela de permissões do grupo (para a empresa). Permite configurar visualizar, editar, deletar, alterar, relatório por menu.
     */
    public function permissoesGrupoEmpresa($grupo_id = null, $empresa_id = null, $gpu_id = null)
    {
        if ($grupo_id == null || $empresa_id == null || $gpu_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $grupo = $this->db->get_where('grupos_empresariais', ['gre_id' => $grupo_id])->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo empresarial não encontrado.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $empresa = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        if (!$empresa) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url("index.php/super/empresas/{$grupo_id}"));
        }
        $grupoUsuario = $this->db->get_where('grupo_usuario', ['gpu_id' => $gpu_id, 'emp_id' => $empresa_id])->row();
        if (!$grupoUsuario) {
            $this->session->set_flashdata('error', 'Grupo de usuário não encontrado para esta empresa.');
            redirect(base_url("index.php/super/usuariosEmpresa/{$grupo_id}/{$empresa_id}"));
        }

        if (!$this->db->table_exists('menu_empresa') || !$this->db->table_exists('menus')) {
            $this->session->set_flashdata('error', 'Tabelas de menu não configuradas.');
            redirect(base_url("index.php/super/usuariosEmpresa/{$grupo_id}/{$empresa_id}"));
        }

        $this->db->select('me.mep_id, m.men_nome, m.men_identificador, m.men_ordem');
        $this->db->from('menu_empresa me');
        $this->db->join('menus m', 'm.men_id = me.men_id');
        $this->db->where('me.emp_id', $empresa_id);
        $this->db->order_by('m.men_ordem', 'ASC');
        $menus_empresa = $this->db->get()->result();

        $menus_cadastro = [];
        $menus_relatorio = [];
        foreach ($menus_empresa as $me) {
            if (strpos($me->men_identificador, 'relatorio_') === 0) {
                $menus_relatorio[] = $me;
            } else {
                $menus_cadastro[] = $me;
            }
        }

        $permissoes_atuais = [];
        if ($this->db->table_exists('grupo_usuario_permissoes')) {
            $this->db->from('grupo_usuario_permissoes');
            $this->db->where('gpu_id', $gpu_id);
            foreach ($this->db->get()->result() as $p) {
                $permissoes_atuais[$p->mep_id] = [
                    'gup_visualizar' => (int) $p->gup_visualizar,
                    'gup_editar'     => (int) $p->gup_editar,
                    'gup_deletar'    => (int) $p->gup_deletar,
                    'gup_alterar'    => (int) $p->gup_alterar,
                    'gup_relatorio'  => (int) $p->gup_relatorio,
                ];
            }
        }

        $this->data['grupo'] = $grupoUsuario;
        $this->data['grupo_empresarial'] = $grupo;
        $this->data['empresa'] = $empresa;
        $this->data['menus_cadastro'] = $menus_cadastro;
        $this->data['menus_relatorio'] = $menus_relatorio;
        $this->data['permissoes_atuais'] = $permissoes_atuais;
        $this->data['view'] = 'super/permissoesGrupoEmpresa';
        $this->load->view('super/layout', $this->data);
    }

    /**
     * Salva as permissões do grupo (POST: gpu_id, grupo_id, empresa_id, perm[mep_id][visualizar|editar|...]).
     */
    public function salvarPermissoesGrupoEmpresa()
    {
        $grupo_id = (int) $this->input->post('grupo_id');
        $empresa_id = (int) $this->input->post('empresa_id');
        $gpu_id = (int) $this->input->post('gpu_id');
        if (!$grupo_id || !$empresa_id || !$gpu_id) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/gruposEmpresariais'));
        }
        $empresa = $this->db->get_where('empresas', ['emp_id' => $empresa_id, 'gre_id' => $grupo_id])->row();
        if (!$empresa) {
            $this->session->set_flashdata('error', 'Empresa não encontrada.');
            redirect(base_url("index.php/super/empresas/{$grupo_id}"));
        }
        $grupoUsuario = $this->db->get_where('grupo_usuario', ['gpu_id' => $gpu_id, 'emp_id' => $empresa_id])->row();
        if (!$grupoUsuario) {
            $this->session->set_flashdata('error', 'Grupo de usuário não encontrado.');
            redirect(base_url("index.php/super/usuariosEmpresa/{$grupo_id}/{$empresa_id}"));
        }

        $perm = $this->input->post('perm');
        if (!is_array($perm)) {
            $perm = [];
        }

        $now = date('Y-m-d H:i:s');
        $menus_da_empresa = [];
        $this->db->select('mep_id');
        $this->db->from('menu_empresa');
        $this->db->where('emp_id', $empresa_id);
        foreach ($this->db->get()->result() as $r) {
            $menus_da_empresa[(int) $r->mep_id] = true;
        }

        if (!$this->db->table_exists('grupo_usuario_permissoes')) {
            $this->session->set_flashdata('error', 'Tabela de permissões não existe.');
            redirect(base_url("index.php/super/permissoesGrupoEmpresa/{$grupo_id}/{$empresa_id}/{$gpu_id}"));
        }

        $hasDataCadastro = $this->db->field_exists('gup_data_cadastro', 'grupo_usuario_permissoes');

        foreach ($perm as $mep_id => $flags) {
            $mep_id = (int) $mep_id;
            if (!isset($menus_da_empresa[$mep_id])) {
                continue;
            }
            $v = isset($flags['visualizar']) ? 1 : 0;
            $e = isset($flags['editar']) ? 1 : 0;
            $d = isset($flags['deletar']) ? 1 : 0;
            $a = isset($flags['alterar']) ? 1 : 0;
            $r = isset($flags['relatorio']) ? 1 : 0;

            $this->db->from('grupo_usuario_permissoes');
            $this->db->where('gpu_id', $gpu_id);
            $this->db->where('mep_id', $mep_id);
            $existe = $this->db->get()->row();
            if ($existe) {
                $this->db->where('gpu_id', $gpu_id);
                $this->db->where('mep_id', $mep_id);
                $this->db->update('grupo_usuario_permissoes', [
                    'gup_visualizar' => $v,
                    'gup_editar'     => $e,
                    'gup_deletar'    => $d,
                    'gup_alterar'    => $a,
                    'gup_relatorio'  => $r,
                    'gup_data_atualizacao' => $now,
                ]);
            } else {
                $insert = [
                    'gpu_id' => $gpu_id,
                    'mep_id' => $mep_id,
                    'gup_visualizar' => $v,
                    'gup_editar'     => $e,
                    'gup_deletar'    => $d,
                    'gup_alterar'    => $a,
                    'gup_relatorio'  => $r,
                    'gup_data_atualizacao' => $now,
                ];
                if ($hasDataCadastro) {
                    $insert['gup_data_cadastro'] = $now;
                }
                $this->db->insert('grupo_usuario_permissoes', $insert);
            }
        }

        $this->session->set_flashdata('success', 'Permissões do grupo salvas com sucesso!');
        log_info('Super salvou permissões do grupo. GPU_ID: ' . $gpu_id . ', Empresa: ' . $empresa_id);
        redirect(base_url("index.php/super/permissoesGrupoEmpresa/{$grupo_id}/{$empresa_id}/{$gpu_id}"));
    }

    // ========== GERENCIAR USUÁRIOS DO TENANT ==========
    public function usuariosTenant($tenant_id = null)
    {
        if ($tenant_id == null) {
            $this->session->set_flashdata('error', 'Tenant não informado.');
            redirect(base_url('index.php/super/tenants'));
        }

        $tenant = $this->db->get_where('tenants', ['ten_id' => $tenant_id])->row();
        if (!$tenant) {
            $this->session->set_flashdata('error', 'Tenant não encontrado.');
            redirect(base_url('index.php/super/tenants'));
        }

        $this->load->library('pagination');

        $pesquisa = $this->input->get('pesquisa');
        $per_page = 20;
        $page = $this->input->get('page') ?: 1;
        $start = ($page - 1) * $per_page;

        $this->data['configuration']['base_url'] = base_url("index.php/super/usuariosTenant/{$tenant_id}");
        $this->data['configuration']['total_rows'] = $this->Usuarios_model->count();
        $this->data['configuration']['per_page'] = $per_page;
        $this->data['configuration']['use_page_numbers'] = TRUE;
        $this->data['configuration']['page_query_string'] = TRUE;
        $this->data['configuration']['query_string_segment'] = 'page';

        $this->pagination->initialize($this->data['configuration']);

        // Buscar usuários do tenant específico (permissão = grupo; sem tabela permissoes)
        $col_nome = $this->db->field_exists('usu_nome', 'usuarios') ? 'usu_nome' : 'nome';
        $col_email = $this->db->field_exists('usu_email', 'usuarios') ? 'usu_email' : 'email';
        $col_gre = $this->db->field_exists('gre_id', 'usuarios') ? 'gre_id' : 'ten_id';
        $this->db->select('usuarios.*');
        if ($this->db->table_exists('grupo_usuario_empresa') && $this->db->table_exists('grupo_usuario')) {
            $emp = $this->db->select('emp_id')->from('empresas')->where('gre_id', $tenant_id)->limit(1)->get()->row();
            if ($emp) {
                $this->db->select('grupo_usuario.gpu_nome as permissao');
                $this->db->join('grupo_usuario_empresa', 'grupo_usuario_empresa.usu_id = usuarios.usu_id AND grupo_usuario_empresa.emp_id = ' . (int) $emp->emp_id, 'left');
                $this->db->join('grupo_usuario', 'grupo_usuario.gpu_id = grupo_usuario_empresa.gpu_id', 'left');
            }
        }
        $this->db->from('usuarios');
        $this->db->where('usuarios.' . $col_gre, $tenant_id);
        if ($pesquisa) {
            $this->db->group_start();
            $this->db->like('usuarios.' . $col_nome, $pesquisa);
            $this->db->or_like('usuarios.' . $col_email, $pesquisa);
            $this->db->group_end();
        }
        $this->db->order_by('usuarios.' . $col_nome, 'ASC');
        $this->db->limit($per_page, $start);

        $this->data['results'] = $this->db->get()->result();
        $this->data['tenant'] = $tenant;
        $this->data['search'] = $pesquisa;
        $this->data['view'] = 'super/usuariosTenant';
        $this->load->view('super/layout', $this->data);
    }

    public function adicionarUsuarioTenant($tenant_id = null)
    {
        if ($tenant_id == null) {
            $this->session->set_flashdata('error', 'Tenant não informado.');
            redirect(base_url('index.php/super/tenants'));
        }

        $tenant = $this->db->get_where('tenants', ['ten_id' => $tenant_id])->row();
        if (!$tenant) {
            $this->session->set_flashdata('error', 'Tenant não encontrado.');
            redirect(base_url('index.php/super/tenants'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|trim');
        $this->form_validation->set_rules('senha', 'Senha', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('cpf', 'cpf', 'required|trim');
        $this->form_validation->set_rules('telefone', 'Telefone', 'required|trim');
        $this->form_validation->set_rules('gpu_id', 'Grupo de usuário', 'required|trim');

        if ($this->form_validation->run('usuarios') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $col_gre = $this->db->field_exists('gre_id', 'usuarios') ? 'gre_id' : 'ten_id';
            $data = [
                'usu_nome' => set_value('nome'),
                'usu_email' => set_value('email'),
                'usu_senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'usu_situacao' => set_value('situacao'),
                'usu_data_cadastro' => date('Y-m-d H:i:s'),
                'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                'usu_data_expiracao' => set_value('dataExpiracao') ?: null,
                $col_gre => $tenant_id,
            ];
            if (!$this->db->field_exists('usu_nome', 'usuarios')) {
                $data = [
                    'nome' => set_value('nome'),
                    'email' => set_value('email'),
                    'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                    'situacao' => set_value('situacao'),
                    'dataExpiracao' => set_value('dataExpiracao'),
                    'dataCadastro' => date('Y-m-d'),
                    'ten_id' => $tenant_id,
                ];
            }

            if ($this->Usuarios_model->add('usuarios', $data) == true) {
                $usu_id = $this->db->insert_id();
                $gpu_id = (int) $this->input->post('gpu_id');
                $emp = $this->db->select('emp_id')->from('empresas')->where('gre_id', $tenant_id)->limit(1)->get()->row();
                if ($gpu_id && $emp && $this->db->table_exists('grupo_usuario_empresa')) {
                    $this->db->replace('grupo_usuario_empresa', [
                        'usu_id' => $usu_id,
                        'gpu_id' => $gpu_id,
                        'emp_id' => $emp->emp_id,
                        'uge_data_cadastro' => date('Y-m-d H:i:s'),
                        'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->session->set_flashdata('success', 'Usuário cadastrado com sucesso!');
                log_info('Super usuário adicionou um usuário ao tenant ' . $tenant_id);
                redirect(base_url("index.php/super/usuariosTenant/{$tenant_id}"));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $emp = $this->db->table_exists('empresas') ? $this->db->select('emp_id')->from('empresas')->where('gre_id', $tenant_id)->limit(1)->get()->row() : null;
        $this->data['grupos'] = [];
        if ($emp && $this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $emp->emp_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
        }
        $this->data['tenant'] = $tenant;
        $this->data['view'] = 'super/adicionarUsuarioTenant';
        $this->load->view('super/layout', $this->data);
    }

    public function editarUsuarioTenant($tenant_id = null, $usuario_id = null)
    {
        if ($tenant_id == null || $usuario_id == null) {
            $this->session->set_flashdata('error', 'Parâmetros inválidos.');
            redirect(base_url('index.php/super/tenants'));
        }

        $tenant = $this->db->get_where('tenants', ['ten_id' => $tenant_id])->row();
        if (!$tenant) {
            $this->session->set_flashdata('error', 'Tenant não encontrado.');
            redirect(base_url('index.php/super/tenants'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|trim');
        $this->form_validation->set_rules('gpu_id', 'Grupo de usuário', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'usu_nome' => $this->input->post('nome'),
                'usu_email' => $this->input->post('email'),
                'usu_data_expiracao' => $this->input->post('dataExpiracao') ?: null,
                'usu_situacao' => $this->input->post('situacao'),
                'usu_data_atualizacao' => date('Y-m-d H:i:s'),
                'gre_id' => $tenant_id,
            ];

            $senha = $this->input->post('senha');
            if ($senha != null && $senha != '') {
                $data['usu_senha'] = password_hash($senha, PASSWORD_DEFAULT);
            }

            if ($this->Usuarios_model->edit('usuarios', $data, 'usu_id', $usuario_id) == true) {
                $gpu_id = (int) $this->input->post('gpu_id');
                $emp = $this->db->select('emp_id')->from('empresas')->where('gre_id', $tenant_id)->limit(1)->get()->row();
                if ($gpu_id && $emp && $this->db->table_exists('grupo_usuario_empresa')) {
                    $uge = $this->db->get_where('grupo_usuario_empresa', ['usu_id' => $usuario_id, 'emp_id' => $emp->emp_id])->row();
                    if ($uge) {
                        $this->db->where('uge_id', $uge->uge_id)->update('grupo_usuario_empresa', [
                            'gpu_id' => $gpu_id,
                            'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                        ]);
                    } else {
                        $this->db->insert('grupo_usuario_empresa', [
                            'usu_id' => $usuario_id,
                            'gpu_id' => $gpu_id,
                            'emp_id' => $emp->emp_id,
                            'uge_data_cadastro' => date('Y-m-d H:i:s'),
                            'uge_data_atualizacao' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
                $this->session->set_flashdata('success', 'Usuário editado com sucesso!');
                log_info('Super usuário editou um usuário do tenant ' . $tenant_id);
                redirect(base_url("index.php/super/usuariosTenant/{$tenant_id}"));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $emp = $this->db->table_exists('empresas') ? $this->db->select('emp_id')->from('empresas')->where('gre_id', $tenant_id)->limit(1)->get()->row() : null;
        $this->data['grupos'] = [];
        $this->data['gpu_id_atual'] = null;
        if ($emp && $this->db->table_exists('grupo_usuario')) {
            $this->data['grupos'] = $this->db->select('gpu_id, gpu_nome')->from('grupo_usuario')
                ->where('emp_id', $emp->emp_id)->where('gpu_situacao', 1)->order_by('gpu_nome', 'ASC')->get()->result();
            if ($this->db->table_exists('grupo_usuario_empresa')) {
                $uge = $this->db->get_where('grupo_usuario_empresa', ['usu_id' => $usuario_id, 'emp_id' => $emp->emp_id])->row();
                if ($uge) {
                    $this->data['gpu_id_atual'] = (int) $uge->gpu_id;
                }
            }
        }
        $this->data['result'] = $this->Usuarios_model->getById($usuario_id);
        $this->data['tenant'] = $tenant;
        $this->data['view'] = 'super/editarUsuarioTenant';
        $this->load->view('super/layout', $this->data);
    }

    // ========== GERENCIAR PERMISSÕES DE MENU DO TENANT ==========
    public function permissoesMenu($tenant_id = null)
    {
        if ($tenant_id == null) {
            $this->session->set_flashdata('error', 'Tenant não informado.');
            redirect(base_url('index.php/super/tenants'));
        }

        $tenant = $this->db->get_where('tenants', ['ten_id' => $tenant_id])->row();
        if (!$tenant) {
            $this->session->set_flashdata('error', 'Tenant não encontrado.');
            redirect(base_url('index.php/super/tenants'));
        }

        // Carregar todas as permissões do sistema
        $this->load->config('permission');
        $all_permissions = $this->config->item('permission');

        // Agrupar permissões por módulo
        $menus_agrupados = [];
        foreach ($all_permissions as $codigo => $nome) {
            // Extrair o módulo (ex: 'Cliente' de 'vCliente', 'FaturamentoEntrada' de 'vFaturamentoEntrada')
            // Remove prefixos: v, a, e, d, c, r
            $modulo = preg_replace('/^[vaedcr]/', '', $codigo);
            if ($modulo == 'Sistema') {
                $modulo = 'Configuracao';
            }

            // Se não removeu nada, pode ser que não tenha prefixo (caso raro)
            if ($modulo == $codigo && strlen($codigo) > 0) {
                // Tentar identificar módulo de outra forma (ex: ClassificacaoFiscal)
                // Se começar com letra minúscula seguida de maiúscula, é um módulo composto
                if (preg_match('/^[a-z]+([A-Z][a-zA-Z]+)$/', $codigo, $matches)) {
                    $modulo = $matches[1];
                } else {
                    // Se não conseguir identificar, usar o código completo
                    $modulo = $codigo;
                }
            }

            // Garantir que o módulo começa com maiúscula (para consistência)
            if (strlen($modulo) > 0) {
                $modulo = ucfirst($modulo);
            }

            if (!isset($menus_agrupados[$modulo])) {
                $menus_agrupados[$modulo] = [];
            }
            $menus_agrupados[$modulo][$codigo] = $nome;
        }

        // Usar TODAS as permissões do config como disponíveis (sistema não usa mais tabela permissoes)
        $permissoes_sistema = array_keys($all_permissions);

        // Buscar permissões já configuradas para este tenant
        $this->db->where('tpm_ten_id', $tenant_id);
        $this->db->where('tpm_ativo', 1);
        $permissoes_configuradas = $this->db->get('tenant_permissoes_menu')->result();

        // Verificar quais módulos estão habilitados (se pelo menos uma permissão do módulo estiver ativa)
        $modulos_habilitados = [];
        foreach ($permissoes_configuradas as $perm) {
            $codigo = $perm->tpm_permissao;
            // Extrair módulo da mesma forma que foi feito no agrupamento
            $modulo = preg_replace('/^[vaedcr]/', '', $codigo);

            if ($modulo == $codigo && strlen($codigo) > 0) {
                if (preg_match('/^[a-z]+([A-Z][a-zA-Z]+)$/', $codigo, $matches)) {
                    $modulo = $matches[1];
                } else {
                    $modulo = $codigo;
                }
            }

            // Garantir que o módulo começa com maiúscula (para consistência)
            if (strlen($modulo) > 0) {
                $modulo = ucfirst($modulo);
            }
            // Verificar se o módulo existe no agrupamento antes de adicionar
            if (isset($menus_agrupados[$modulo]) && !in_array($modulo, $modulos_habilitados)) {
                $modulos_habilitados[] = $modulo;
            }
        }

        if ($this->input->post()) {
            // Salvar permissões por módulo
            $this->db->where('tpm_ten_id', $tenant_id);
            $this->db->delete('tenant_permissoes_menu');

            $modulos_post = $this->input->post('modulos');

            log_message('debug', 'Módulos recebidos: ' . print_r($modulos_post, true));

            $permissoes_salvas = 0;
            $modulos_salvos = 0;
            $erros = [];

            if (is_array($modulos_post) && !empty($modulos_post)) {
                foreach ($modulos_post as $modulo => $habilitado) {
                    // Verificar se o módulo está habilitado (valor = 1)
                    if ($habilitado == '1' || $habilitado === 1 || $habilitado === '1') {
                        // Buscar todas as permissões deste módulo
                        if (isset($menus_agrupados[$modulo]) && is_array($menus_agrupados[$modulo])) {
                            foreach ($menus_agrupados[$modulo] as $codigo_permissao => $nome_permissao) {
                                // Verificar se a permissão existe no sistema
                                if (in_array($codigo_permissao, $permissoes_sistema)) {
                                    $data = [
                                        'tpm_ten_id' => $tenant_id,
                                        'tpm_menu_codigo' => $codigo_permissao,
                                        'tpm_permissao' => $codigo_permissao,
                                        'tpm_ativo' => 1,
                                        'tpm_data_cadastro' => date('Y-m-d H:i:s'),
                                    ];

                                    $insert_result = $this->db->insert('tenant_permissoes_menu', $data);

                                    if ($insert_result) {
                                        $permissoes_salvas++;
                                    } else {
                                        $db_error = $this->db->error();
                                        // Se for erro de duplicata, ignorar (pode acontecer em race conditions)
                                        if (isset($db_error['code']) && $db_error['code'] == 1062) {
                                            // Duplicata - atualizar para ativo
                                            $this->db->where('tpm_ten_id', $tenant_id);
                                            $this->db->where('tpm_permissao', $codigo_permissao);
                                            $this->db->update('tenant_permissoes_menu', ['tpm_ativo' => 1]);
                                            $permissoes_salvas++;
                                        } else {
                                            $erro_msg = isset($db_error['message']) ? $db_error['message'] : 'Erro desconhecido';
                                            log_message('error', "Erro ao inserir permissão {$codigo_permissao}: {$erro_msg}");
                                            $erros[] = "Erro ao salvar permissão {$codigo_permissao}: {$erro_msg}";
                                        }
                                    }
                                }
                            }
                            $modulos_salvos++;
                        }
                    }
                }
            } else {
                log_message('debug', 'Nenhum módulo foi enviado ou array vazio');
            }

            log_message('debug', "Total de módulos salvos: {$modulos_salvos}, Total de permissões salvas: {$permissoes_salvas}");

            if (!empty($erros)) {
                $this->session->set_flashdata('error', 'Algumas permissões não puderam ser salvas. Verifique os logs.');
            } else {
                $this->session->set_flashdata('success', "Permissões de menu salvas com sucesso! ({$modulos_salvos} módulos, {$permissoes_salvas} permissões)");
            }

            log_info('Super usuário atualizou permissões de menu do tenant ' . $tenant_id);
            redirect(base_url("index.php/super/permissoesMenu/{$tenant_id}"));
        }

        $this->data['tenant'] = $tenant;
        $this->data['menus_agrupados'] = $menus_agrupados;
        $this->data['permissoes_sistema'] = $permissoes_sistema;
        $this->data['modulos_habilitados'] = $modulos_habilitados;
        $this->data['all_permissions'] = $all_permissions;
        $this->data['view'] = 'super/permissoesMenu';
        $this->load->view('super/layout', $this->data);
    }

    // ========== GERENCIAR SUPER USUÁRIOS ==========
    public function superUsuarios()
    {
        $this->load->library('pagination');

        $pesquisa = $this->input->get('pesquisa');
        $per_page = 20;
        $page = $this->input->get('page') ?: 1;
        $start = ($page - 1) * $per_page;

        $this->data['configuration']['base_url'] = base_url('index.php/super/superUsuarios');
        $this->data['configuration']['total_rows'] = $this->Usuarios_super_model->count();
        $this->data['configuration']['per_page'] = $per_page;
        $this->data['configuration']['use_page_numbers'] = TRUE;
        $this->data['configuration']['page_query_string'] = TRUE;
        $this->data['configuration']['query_string_segment'] = 'page';

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->Usuarios_super_model->get($per_page, $start);
        $this->data['search'] = $pesquisa;
        $this->data['view'] = 'super/superUsuarios';
        $this->load->view('super/layout', $this->data);
    }

    public function adicionarSuperUsuario()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('uss_nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('uss_email', 'E-mail', 'required|valid_email|trim|is_unique[usuarios_super.uss_email]');
        $this->form_validation->set_rules('uss_senha', 'Senha', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('uss_cpf', 'cpf', 'required|trim');
        $this->form_validation->set_rules('uss_telefone', 'Telefone', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'uss_nome' => $this->input->post('uss_nome'),
                'uss_rg' => $this->input->post('uss_rg'),
                'uss_cpf' => $this->input->post('uss_cpf'),
                'uss_email' => $this->input->post('uss_email'),
                'uss_senha' => password_hash($this->input->post('uss_senha'), PASSWORD_DEFAULT),
                'uss_telefone' => $this->input->post('uss_telefone'),
                'uss_celular' => $this->input->post('uss_celular'),
                'uss_situacao' => $this->input->post('uss_situacao') ?: 1,
                'uss_data_cadastro' => date('Y-m-d'),
                'uss_data_expiracao' => $this->input->post('uss_data_expiracao') ?: null,
            ];

            if ($this->Usuarios_super_model->add($data)) {
                $this->session->set_flashdata('success', 'Super usuário cadastrado com sucesso!');
                log_info('Super usuário adicionou outro super usuário.');
                redirect(base_url('index.php/super/superUsuarios'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'super/adicionarSuperUsuario';
        $this->load->view('super/layout', $this->data);
    }

    public function editarSuperUsuario($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Super usuário não encontrado.');
            redirect(base_url('index.php/super/superUsuarios'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('uss_nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('uss_email', 'E-mail', 'required|valid_email|trim');
        $this->form_validation->set_rules('uss_cpf', 'cpf', 'required|trim');
        $this->form_validation->set_rules('uss_telefone', 'Telefone', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'uss_nome' => $this->input->post('uss_nome'),
                'uss_rg' => $this->input->post('uss_rg'),
                'uss_cpf' => $this->input->post('uss_cpf'),
                'uss_email' => $this->input->post('uss_email'),
                'uss_telefone' => $this->input->post('uss_telefone'),
                'uss_celular' => $this->input->post('uss_celular'),
                'uss_situacao' => $this->input->post('uss_situacao') ?: 1,
                'uss_data_expiracao' => $this->input->post('uss_data_expiracao') ?: null,
            ];

            $senha = $this->input->post('uss_senha');
            if ($senha != null && $senha != '') {
                $data['uss_senha'] = password_hash($senha, PASSWORD_DEFAULT);
            }

            if ($this->Usuarios_super_model->edit($data, $id)) {
                $this->session->set_flashdata('success', 'Super usuário editado com sucesso!');
                log_info('Super usuário editou outro super usuário. ID ' . $id);
                redirect(base_url('index.php/super/superUsuarios'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->Usuarios_super_model->getById($id);
        $this->data['view'] = 'super/editarSuperUsuario';
        $this->load->view('super/layout', $this->data);
    }

    public function excluirSuperUsuario()
    {
        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir super usuário.');
            redirect(site_url('super/superUsuarios/'));
        }

        // Não permitir excluir a si mesmo
        if ($id == $this->session->userdata('id_admin')) {
            $this->session->set_flashdata('error', 'Você não pode excluir a si mesmo.');
            redirect(site_url('super/superUsuarios/'));
        }

        $this->Usuarios_super_model->delete($id);
        log_info('Super usuário removeu outro super usuário. ID ' . $id);

        $this->session->set_flashdata('success', 'Super usuário excluído com sucesso!');
        redirect(site_url('super/superUsuarios/'));
    }
}


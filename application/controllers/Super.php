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
        $this->load->model('permissoes_model');
    }

    public function index()
    {
        // Dashboard do painel super
        $this->data['total_tenants'] = $this->db->count_all('tenants');
        $this->data['total_usuarios'] = $this->db->count_all('usuarios');
        $this->data['total_super_usuarios'] = $this->db->count_all('usuarios_super');

        // Listar últimos tenants
        $this->db->order_by('ten_data_cadastro', 'DESC');
        $this->db->limit(10);
        $this->data['ultimos_tenants'] = $this->db->get('tenants')->result();

        $this->data['view'] = 'super/dashboard';
        $this->load->view('super/layout', $this->data);
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

                // Criar permissão padrão para o tenant
                $permissoes_padrao = [
                    'vCliente' => 1,
                    'aCliente' => 1,
                    'eCliente' => 1,
                    'dCliente' => 1,
                    'vPessoa' => 1,
                    'aPessoa' => 1,
                    'ePessoa' => 1,
                    'dPessoa' => 1,
                    'vProduto' => 1,
                    'aProduto' => 1,
                    'eProduto' => 1,
                    'dProduto' => 1,
                    'vServico' => 1,
                    'aServico' => 1,
                    'eServico' => 1,
                    'dServico' => 1,
                    'vOs' => 1,
                    'aOs' => 1,
                    'eOs' => 1,
                    'dOs' => 1,
                    'vVenda' => 1,
                    'aVenda' => 1,
                    'eVenda' => 1,
                    'dVenda' => 1,
                    'vLancamento' => 1,
                    'aLancamento' => 1,
                    'eLancamento' => 1,
                    'dLancamento' => 1,
                    'cUsuario' => 1,
                    'cEmitente' => 1,
                    'cPermissao' => 1,
                    'cSistema' => 1,
                ];

                $permissao_data = [
                    'nome' => 'Permissão Padrão - ' . $data['ten_nome'],
                    'data' => date('Y-m-d'),
                    'permissoes' => serialize($permissoes_padrao),
                    'situacao' => 1,
                    'ten_id' => $ten_id,
                ];

                $this->db->insert('permissoes', $permissao_data);
                $permissao_id = $this->db->insert_id();

                // Habilitar todas as permissões padrão para o tenant em tenant_permissoes_menu
                foreach ($permissoes_padrao as $codigo_perm => $valor) {
                    if ($valor == 1) {
                        $perm_menu_data = [
                            'tpm_ten_id' => $ten_id,
                            'tpm_menu_codigo' => $codigo_perm,
                            'tpm_permissao' => $codigo_perm,
                            'tpm_ativo' => 1,
                            'tpm_data_cadastro' => date('Y-m-d H:i:s'),
                        ];
                        $this->db->insert('tenant_permissoes_menu', $perm_menu_data);
                    }
                }

                // Criar usuário se os campos foram preenchidos
                $usuario_nome = $this->input->post('usuario_nome');
                $usuario_email = $this->input->post('usuario_email');
                $usuario_senha = $this->input->post('usuario_senha');

                if (!empty($usuario_nome) && !empty($usuario_email) && !empty($usuario_senha)) {
                    $usuario_data = [
                        'nome' => $usuario_nome,
                        'email' => $usuario_email,
                        'senha' => password_hash($usuario_senha, PASSWORD_DEFAULT),
                        'cpf' => '000.000.000-00', // CPF padrão, pode ser alterado depois
                        'cep' => '00000-000', // CEP padrão
                        'telefone' => $data['ten_telefone'] ?: '(00) 0000-0000',
                        'situacao' => 1,
                        'dataCadastro' => date('Y-m-d'),
                        'dataExpiracao' => '3000-01-01', // Data de expiração distante
                        'permissoes_id' => $permissao_id, // Usar a permissão padrão criada
                        'ten_id' => $ten_id,
                    ];

                    if ($this->db->insert('usuarios', $usuario_data)) {
                        $this->session->set_flashdata('success', 'Tenant adicionado com sucesso! Permissão padrão e usuário administrador criados.');
                        log_info('Super usuário adicionou um tenant com usuário. Tenant ID: ' . $ten_id . ', Usuário: ' . $usuario_email);
                    } else {
                        $this->session->set_flashdata('success', 'Tenant adicionado com sucesso! Permissão padrão criada. Erro ao criar usuário.');
                        log_info('Super usuário adicionou um tenant. ID: ' . $ten_id . ' (erro ao criar usuário)');
                    }
                } else {
                    $this->session->set_flashdata('success', 'Tenant adicionado com sucesso! Permissão padrão criada.');
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

        // Buscar usuários do tenant específico
        $this->db->select('usuarios.*, permissoes.nome as permissao');
        $this->db->from('usuarios');
        $this->db->join('permissoes', 'permissoes.idPermissao = usuarios.permissoes_id', 'left');
        $this->db->where('usuarios.ten_id', $tenant_id);
        if ($pesquisa) {
            $this->db->group_start();
            $this->db->like('usuarios.nome', $pesquisa);
            $this->db->or_like('usuarios.email', $pesquisa);
            $this->db->group_end();
        }
        $this->db->order_by('usuarios.nome', 'ASC');
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
        $this->form_validation->set_rules('permissoes_id', 'Permissões', 'required|trim');

        if ($this->form_validation->run('usuarios') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nome' => set_value('nome'),
                'rg' => set_value('rg'),
                'cpf' => set_value('cpf'),
                'cep' => set_value('cep') ?: '00000-000',
                'rua' => set_value('rua'),
                'numero' => set_value('numero'),
                'bairro' => set_value('bairro'),
                'cidade' => set_value('cidade'),
                'estado' => set_value('estado'),
                'email' => set_value('email'),
                'senha' => password_hash($this->input->post('senha'), PASSWORD_DEFAULT),
                'telefone' => set_value('telefone'),
                'celular' => set_value('celular'),
                'dataExpiracao' => set_value('dataExpiracao'),
                'situacao' => set_value('situacao'),
                'permissoes_id' => $this->input->post('permissoes_id'),
                'dataCadastro' => date('Y-m-d'),
                'ten_id' => $tenant_id,
            ];

            if ($this->Usuarios_model->add('usuarios', $data) == true) {
                $this->session->set_flashdata('success', 'Usuário cadastrado com sucesso!');
                log_info('Super usuário adicionou um usuário ao tenant ' . $tenant_id);
                redirect(base_url("index.php/super/usuariosTenant/{$tenant_id}"));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->load->model('permissoes_model');
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.idPermissao,permissoes.nome');
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
        $this->form_validation->set_rules('cpf', 'cpf', 'required|trim');
        $this->form_validation->set_rules('telefone', 'Telefone', 'required|trim');
        $this->form_validation->set_rules('permissoes_id', 'Permissões', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'nome' => $this->input->post('nome'),
                'rg' => $this->input->post('rg'),
                'cpf' => $this->input->post('cpf'),
                'cep' => $this->input->post('cep'),
                'rua' => $this->input->post('rua'),
                'numero' => $this->input->post('numero'),
                'bairro' => $this->input->post('bairro'),
                'cidade' => $this->input->post('cidade'),
                'estado' => $this->input->post('estado'),
                'email' => $this->input->post('email'),
                'telefone' => $this->input->post('telefone'),
                'celular' => $this->input->post('celular'),
                'dataExpiracao' => $this->input->post('dataExpiracao'),
                'situacao' => $this->input->post('situacao'),
                'permissoes_id' => $this->input->post('permissoes_id'),
                'ten_id' => $tenant_id,
            ];

            $senha = $this->input->post('senha');
            if ($senha != null && $senha != '') {
                $data['senha'] = password_hash($senha, PASSWORD_DEFAULT);
            }

            if ($this->Usuarios_model->edit('usuarios', $data, 'idUsuarios', $usuario_id) == true) {
                $this->session->set_flashdata('success', 'Usuário editado com sucesso!');
                log_info('Super usuário editou um usuário do tenant ' . $tenant_id);
                redirect(base_url("index.php/super/usuariosTenant/{$tenant_id}"));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->load->model('permissoes_model');
        $this->data['permissoes'] = $this->permissoes_model->getActive('permissoes', 'permissoes.idPermissao,permissoes.nome');
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

        // Usar TODAS as permissões do config como disponíveis
        // O super admin pode habilitar qualquer permissão definida no config
        $permissoes_sistema = array_keys($all_permissions);

        // Opcional: também verificar quais permissões já foram usadas em algum perfil
        // Isso ajuda a identificar permissões que realmente existem no sistema
        $this->db->select('permissoes');
        $this->db->from('permissoes');
        $this->db->where('situacao', 1);
        $permissoes_db = $this->db->get()->result();

        $permissoes_usadas = [];
        foreach ($permissoes_db as $perm) {
            $perm_array = @unserialize($perm->permissoes);
            if ($perm_array === false) {
                $perm_array = json_decode($perm->permissoes, true);
            }

            if (is_array($perm_array)) {
                foreach ($perm_array as $key => $value) {
                    if ($value == 1 && !in_array($key, $permissoes_usadas)) {
                        $permissoes_usadas[] = $key;
                    }
                }
            }
        }

        // Se encontrou permissões usadas, adicionar às disponíveis (para garantir que todas estejam)
        if (!empty($permissoes_usadas)) {
            $permissoes_sistema = array_unique(array_merge($permissoes_sistema, $permissoes_usadas));
        }

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


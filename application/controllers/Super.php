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
        $this->form_validation->set_rules('ten_cnpj', 'CNPJ', 'trim');
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
                $this->session->set_flashdata('success', 'Tenant adicionado com sucesso!');
                log_info('Super usuário adicionou um tenant.');
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
        $this->form_validation->set_rules('ten_cnpj', 'CNPJ', 'trim');
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
        $this->form_validation->set_rules('cpf', 'CPF', 'required|trim');
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
        $this->form_validation->set_rules('cpf', 'CPF', 'required|trim');
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

        // Lista de menus disponíveis no sistema
        $menus = [
            'vCliente' => 'Visualizar Clientes',
            'aCliente' => 'Adicionar Clientes',
            'eCliente' => 'Editar Clientes',
            'dCliente' => 'Excluir Clientes',
            'vProduto' => 'Visualizar Produtos',
            'aProduto' => 'Adicionar Produtos',
            'eProduto' => 'Editar Produtos',
            'dProduto' => 'Excluir Produtos',
            'vServico' => 'Visualizar Serviços',
            'aServico' => 'Adicionar Serviços',
            'eServico' => 'Editar Serviços',
            'dServico' => 'Excluir Serviços',
            'vOs' => 'Visualizar OS',
            'aOs' => 'Adicionar OS',
            'eOs' => 'Editar OS',
            'dOs' => 'Excluir OS',
            'vVenda' => 'Visualizar Vendas',
            'aVenda' => 'Adicionar Vendas',
            'eVenda' => 'Editar Vendas',
            'dVenda' => 'Excluir Vendas',
            'vFinanceiro' => 'Visualizar Financeiro',
            'aFinanceiro' => 'Adicionar Financeiro',
            'eFinanceiro' => 'Editar Financeiro',
            'dFinanceiro' => 'Excluir Financeiro',
            'vPessoa' => 'Visualizar Pessoas',
            'aPessoa' => 'Adicionar Pessoas',
            'ePessoa' => 'Editar Pessoas',
            'dPessoa' => 'Excluir Pessoas',
            'vNfecom' => 'Visualizar NFCom',
            'aNfecom' => 'Adicionar NFCom',
            'eNfecom' => 'Editar NFCom',
            'dNfecom' => 'Excluir NFCom',
            'cAuditoria' => 'Visualizar Auditoria',
            'rFinanceiro' => 'Relatórios Financeiro',
            'rCliente' => 'Relatórios Clientes',
            'rProduto' => 'Relatórios Produtos',
            'rOs' => 'Relatórios OS',
            'rVenda' => 'Relatórios Vendas',
            'vOperacaoComercial' => 'Visualizar Operação Comercial',
            'aOperacaoComercial' => 'Adicionar Operação Comercial',
            'eOperacaoComercial' => 'Editar Operação Comercial',
            'dOperacaoComercial' => 'Excluir Operação Comercial',
            'vNcm' => 'Visualizar NCM',
            'aNcm' => 'Adicionar NCM',
            'eNcm' => 'Editar NCM',
            'dNcm' => 'Excluir NCM',
        ];

        // Buscar permissões já configuradas para este tenant
        $this->db->where('TPM_TEN_ID', $tenant_id);
        $permissoes_configuradas = $this->db->get('tenant_permissoes_menu')->result();
        
        $permissoes_array = [];
        foreach ($permissoes_configuradas as $perm) {
            $permissoes_array[$perm->TPM_PERMISSAO] = $perm->TPM_ATIVO;
        }

        if ($this->input->post()) {
            // Salvar permissões
            $this->db->where('TPM_TEN_ID', $tenant_id);
            $this->db->delete('tenant_permissoes_menu');

            $permissoes_post = $this->input->post('permissoes');
            if (is_array($permissoes_post)) {
                foreach ($permissoes_post as $menu_codigo => $permissoes_menu) {
                    if (is_array($permissoes_menu)) {
                        foreach ($permissoes_menu as $permissao => $ativo) {
                            if ($ativo == '1') {
                                $this->db->insert('tenant_permissoes_menu', [
                                    'TPM_TEN_ID' => $tenant_id,
                                    'TPM_MENU_CODIGO' => $permissao,
                                    'TPM_PERMISSAO' => $permissao,
                                    'TPM_ATIVO' => 1,
                                    'TPM_DATA_CADASTRO' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        }
                    }
                }
            }

            $this->session->set_flashdata('success', 'Permissões de menu salvas com sucesso!');
            log_info('Super usuário atualizou permissões de menu do tenant ' . $tenant_id);
            redirect(base_url("index.php/super/permissoesMenu/{$tenant_id}"));
        }

        $this->data['tenant'] = $tenant;
        $this->data['menus'] = $menus;
        $this->data['permissoes_configuradas'] = $permissoes_array;
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

        $this->form_validation->set_rules('USS_NOME', 'Nome', 'required|trim');
        $this->form_validation->set_rules('USS_EMAIL', 'E-mail', 'required|valid_email|trim|is_unique[usuarios_super.USS_EMAIL]');
        $this->form_validation->set_rules('USS_SENHA', 'Senha', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('USS_CPF', 'CPF', 'required|trim');
        $this->form_validation->set_rules('USS_TELEFONE', 'Telefone', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'USS_NOME' => $this->input->post('USS_NOME'),
                'USS_RG' => $this->input->post('USS_RG'),
                'USS_CPF' => $this->input->post('USS_CPF'),
                'USS_EMAIL' => $this->input->post('USS_EMAIL'),
                'USS_SENHA' => password_hash($this->input->post('USS_SENHA'), PASSWORD_DEFAULT),
                'USS_TELEFONE' => $this->input->post('USS_TELEFONE'),
                'USS_CELULAR' => $this->input->post('USS_CELULAR'),
                'USS_SITUACAO' => $this->input->post('USS_SITUACAO') ?: 1,
                'USS_DATA_CADASTRO' => date('Y-m-d'),
                'USS_DATA_EXPIRACAO' => $this->input->post('USS_DATA_EXPIRACAO') ?: null,
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

        $this->form_validation->set_rules('USS_NOME', 'Nome', 'required|trim');
        $this->form_validation->set_rules('USS_EMAIL', 'E-mail', 'required|valid_email|trim');
        $this->form_validation->set_rules('USS_CPF', 'CPF', 'required|trim');
        $this->form_validation->set_rules('USS_TELEFONE', 'Telefone', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'USS_NOME' => $this->input->post('USS_NOME'),
                'USS_RG' => $this->input->post('USS_RG'),
                'USS_CPF' => $this->input->post('USS_CPF'),
                'USS_EMAIL' => $this->input->post('USS_EMAIL'),
                'USS_TELEFONE' => $this->input->post('USS_TELEFONE'),
                'USS_CELULAR' => $this->input->post('USS_CELULAR'),
                'USS_SITUACAO' => $this->input->post('USS_SITUACAO') ?: 1,
                'USS_DATA_EXPIRACAO' => $this->input->post('USS_DATA_EXPIRACAO') ?: null,
            ];

            $senha = $this->input->post('USS_SENHA');
            if ($senha != null && $senha != '') {
                $data['USS_SENHA'] = password_hash($senha, PASSWORD_DEFAULT);
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


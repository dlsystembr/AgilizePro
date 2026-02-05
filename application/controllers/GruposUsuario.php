<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Grupos de Usuário — CRUD por empresa (emp_id da sessão).
 * Cada grupo pertence a uma empresa; listagem e cadastro filtrados pela empresa logada.
 */
class GruposUsuario extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerenciar grupos de usuário.');
            redirect(base_url());
        }

        $this->load->helper(['form']);
        $this->load->model('Mapos_model', 'mapos_model');
        $this->data['menuGruposUsuario'] = 'Grupos de Usuário';
        $this->data['menuConfiguracoes'] = 'Configurações';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada. Faça login novamente.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $this->db->where('emp_id', $emp_id);
        $this->data['configuration']['total_rows'] = $this->db->count_all_results('grupo_usuario');

        $this->data['configuration']['base_url'] = site_url('gruposUsuario/gerenciar/');
        $this->pagination->initialize($this->data['configuration']);

        $this->db->select('gpu_id, gpu_nome, gpu_descricao, gpu_situacao, gpu_data_cadastro, gpu_data_atualizacao');
        $this->db->from('grupo_usuario');
        $this->db->where('emp_id', $emp_id);
        $this->db->order_by('gpu_nome', 'ASC');
        $this->db->limit($this->data['configuration']['per_page'], $this->uri->segment(3));
        $this->data['results'] = $this->db->get()->result();

        $this->data['view'] = 'grupo_usuario/grupo_usuario';
        return $this->layout();
    }

    public function adicionar()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada. Faça login novamente.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('gpu_nome', 'Nome', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('gpu_descricao', 'Descrição', 'trim|max_length[255]');
        $this->form_validation->set_rules('gpu_situacao', 'Situação', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() === false) {
            $this->data['custom_error'] = validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false;
        } else {
            $data = [
                'emp_id' => $emp_id,
                'gpu_nome' => $this->input->post('gpu_nome'),
                'gpu_descricao' => $this->input->post('gpu_descricao') ?: null,
                'gpu_situacao' => (int) $this->input->post('gpu_situacao'),
                'gpu_data_cadastro' => date('Y-m-d H:i:s'),
                'gpu_data_atualizacao' => date('Y-m-d H:i:s'),
            ];
            if ($this->mapos_model->add('grupo_usuario', $data)) {
                $this->session->set_flashdata('success', 'Grupo de usuário adicionado com sucesso!');
                log_info('Adicionou um grupo de usuário.');
                redirect(site_url('gruposUsuario/gerenciar'));
            }
            $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao salvar.</div>';
        }

        $this->data['view'] = 'grupo_usuario/adicionarGrupo';
        return $this->layout();
    }

    public function editar()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada. Faça login novamente.');
            redirect(base_url());
        }

        $gpu_id = (int) $this->uri->segment(3);
        if (!$gpu_id) {
            $this->session->set_flashdata('error', 'Grupo não informado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('gpu_nome', 'Nome', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('gpu_descricao', 'Descrição', 'trim|max_length[255]');
        $this->form_validation->set_rules('gpu_situacao', 'Situação', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() === true) {
            $data = [
                'gpu_nome' => $this->input->post('gpu_nome'),
                'gpu_descricao' => $this->input->post('gpu_descricao') ?: null,
                'gpu_situacao' => (int) $this->input->post('gpu_situacao'),
                'gpu_data_atualizacao' => date('Y-m-d H:i:s'),
            ];
            $this->db->where('gpu_id', $gpu_id);
            $this->db->where('emp_id', $emp_id);
            $this->db->update('grupo_usuario', $data);
            if ($this->db->affected_rows() >= 0) {
                $this->session->set_flashdata('success', 'Grupo de usuário atualizado com sucesso!');
                log_info('Editou grupo de usuário. ID: ' . $gpu_id);
                redirect(site_url('gruposUsuario/gerenciar'));
            }
            $this->data['custom_error'] = '<div class="alert alert-danger">Grupo não encontrado ou sem alterações.';
        }

        $this->db->from('grupo_usuario');
        $this->db->where('gpu_id', $gpu_id);
        $this->db->where('emp_id', $emp_id);
        $this->data['result'] = $this->db->get()->row();
        if (!$this->data['result']) {
            $this->session->set_flashdata('error', 'Grupo não encontrado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->data['view'] = 'grupo_usuario/editarGrupo';
        return $this->layout();
    }

    public function excluir()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada.');
            redirect(base_url());
        }

        $gpu_id = (int) $this->input->post('id');
        if (!$gpu_id) {
            $this->session->set_flashdata('error', 'Grupo não informado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->from('grupo_usuario');
        $this->db->where('gpu_id', $gpu_id);
        $this->db->where('emp_id', $emp_id);
        $row = $this->db->get()->row();
        if (!$row) {
            $this->session->set_flashdata('error', 'Grupo não encontrado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        if ($this->mapos_model->delete('grupo_usuario', 'gpu_id', $gpu_id)) {
            $this->session->set_flashdata('success', 'Grupo de usuário excluído com sucesso!');
            log_info('Excluiu grupo de usuário. ID: ' . $gpu_id);
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir grupo.');
        }
        redirect(site_url('gruposUsuario/gerenciar'));
    }

    /**
     * Tela de permissões do grupo: menus da empresa (menu_empresa) com checkboxes
     * visualizar, editar, deletar, alterar, relatório.
     */
    public function permissoes()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada. Faça login novamente.');
            redirect(base_url());
        }

        $gpu_id = (int) $this->uri->segment(3);
        if (!$gpu_id) {
            $this->session->set_flashdata('error', 'Grupo não informado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->from('grupo_usuario');
        $this->db->where('gpu_id', $gpu_id);
        $this->db->where('emp_id', $emp_id);
        $grupo = $this->db->get()->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo não encontrado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        if (!$this->db->table_exists('menu_empresa') || !$this->db->table_exists('menus')) {
            $this->session->set_flashdata('error', 'Tabelas de menu não configuradas. Configure os menus da empresa no Super.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->select('me.mep_id, m.men_nome, m.men_identificador, m.men_ordem');
        $this->db->from('menu_empresa me');
        $this->db->join('menus m', 'm.men_id = me.men_id');
        $this->db->where('me.emp_id', $emp_id);
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

        $this->db->from('grupo_usuario_permissoes');
        $this->db->where('gpu_id', $gpu_id);
        $perms_rows = $this->db->get()->result();
        $permissoes_atuais = [];
        foreach ($perms_rows as $p) {
            $permissoes_atuais[$p->mep_id] = [
                'gup_visualizar' => (int) $p->gup_visualizar,
                'gup_editar'     => (int) $p->gup_editar,
                'gup_deletar'    => (int) $p->gup_deletar,
                'gup_alterar'    => (int) $p->gup_alterar,
                'gup_relatorio'  => (int) $p->gup_relatorio,
            ];
        }

        $this->data['grupo'] = $grupo;
        $this->data['menus_cadastro'] = $menus_cadastro;
        $this->data['menus_relatorio'] = $menus_relatorio;
        $this->data['permissoes_atuais'] = $permissoes_atuais;
        $this->data['view'] = 'grupo_usuario/permissoesGrupo';
        return $this->layout();
    }

    /**
     * Salva as permissões do grupo (POST: gpu_id, perm[mep_id][visualizar|editar|deletar|alterar|relatorio]).
     */
    public function salvarPermissoes()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada.');
            redirect(base_url());
        }

        $gpu_id = (int) $this->input->post('gpu_id');
        if (!$gpu_id) {
            $this->session->set_flashdata('error', 'Grupo não informado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->from('grupo_usuario');
        $this->db->where('gpu_id', $gpu_id);
        $this->db->where('emp_id', $emp_id);
        if (!$this->db->get()->row()) {
            $this->session->set_flashdata('error', 'Grupo não encontrado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $perm = $this->input->post('perm');
        if (!is_array($perm)) {
            $perm = [];
        }

        $now = date('Y-m-d H:i:s');
        $menus_da_empresa = [];
        $this->db->select('mep_id');
        $this->db->from('menu_empresa');
        $this->db->where('emp_id', $emp_id);
        foreach ($this->db->get()->result() as $r) {
            $menus_da_empresa[(int) $r->mep_id] = true;
        }

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
                $this->db->insert('grupo_usuario_permissoes', [
                    'gpu_id' => $gpu_id,
                    'mep_id' => $mep_id,
                    'gup_visualizar' => $v,
                    'gup_editar'     => $e,
                    'gup_deletar'    => $d,
                    'gup_alterar'    => $a,
                    'gup_relatorio'  => $r,
                    'gup_data_cadastro' => $now,
                    'gup_data_atualizacao' => $now,
                ]);
            }
        }

        $this->session->set_flashdata('success', 'Permissões do grupo salvas com sucesso!');
        log_info('Salvou permissões do grupo de usuário. GPU_ID: ' . $gpu_id);
        redirect(site_url('gruposUsuario/permissoes/' . $gpu_id));
    }

    /**
     * Usuários vinculados ao grupo (empresa atual). Lista e permite adicionar/remover.
     */
    public function usuarios()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada. Faça login novamente.');
            redirect(base_url());
        }

        $gpu_id = (int) $this->uri->segment(3);
        if (!$gpu_id) {
            $this->session->set_flashdata('error', 'Grupo não informado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->from('grupo_usuario');
        $this->db->where('gpu_id', $gpu_id);
        $this->db->where('emp_id', $emp_id);
        $grupo = $this->db->get()->row();
        if (!$grupo) {
            $this->session->set_flashdata('error', 'Grupo não encontrado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        if (!$this->db->table_exists('grupo_usuario_empresa')) {
            $this->session->set_flashdata('error', 'Tabela grupo_usuario_empresa não existe.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->select('uge.usu_id');
        $this->db->from('grupo_usuario_empresa uge');
        $this->db->where('uge.gpu_id', $gpu_id);
        $this->db->where('uge.emp_id', $emp_id);
        $rows = $this->db->get()->result();
        $ids_no_grupo = [];
        foreach ($rows as $r) {
            $ids_no_grupo[(int) $r->usu_id] = true;
        }

        $gre_id = null;
        $emp = $this->db->select('gre_id')->from('empresas')->where('emp_id', $emp_id)->get()->row();
        if ($emp) {
            $gre_id = $emp->gre_id;
        }
        if ($this->db->field_exists('ten_id', 'empresas') && ($gre_id === null || !$this->db->field_exists('gre_id', 'empresas'))) {
            $emp = $this->db->select('ten_id')->from('empresas')->where('emp_id', $emp_id)->get()->row();
            if ($emp && isset($emp->ten_id)) {
                $gre_id = $emp->ten_id;
            }
        }

        $usuarios_todos = [];
        if ($gre_id !== null) {
            $this->db->select('u.usu_id, u.usu_nome, u.usu_email');
            $this->db->from('usuarios u');
            if ($this->db->field_exists('gre_id', 'usuarios')) {
                $this->db->where('u.gre_id', $gre_id);
            } elseif ($this->db->field_exists('ten_id', 'usuarios')) {
                $this->db->where('u.ten_id', $gre_id);
            } else {
                $this->db->where('1 = 0');
            }
            if ($this->db->field_exists('usu_situacao', 'usuarios')) {
                $this->db->where('u.usu_situacao', 1);
            } elseif ($this->db->field_exists('situacao', 'usuarios')) {
                $this->db->where('u.situacao', 1);
            }
            $this->db->order_by('u.usu_nome', 'ASC');
            $usuarios_todos = $this->db->get()->result();
        }

        $this->data['grupo'] = $grupo;
        $this->data['usuarios_todos'] = $usuarios_todos;
        $this->data['ids_no_grupo'] = $ids_no_grupo;
        $this->data['view'] = 'grupo_usuario/usuariosGrupo';
        return $this->layout();
    }

    /**
     * Salva vínculos: usuários marcados (usu_ids[]) ficam no grupo; demais são removidos (POST: gpu_id, usu_ids[]).
     */
    public function salvarUsuariosGrupo()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada.');
            redirect(base_url());
        }

        $gpu_id = (int) $this->input->post('gpu_id');
        if (!$gpu_id) {
            $this->session->set_flashdata('error', 'Grupo não informado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->from('grupo_usuario');
        $this->db->where('gpu_id', $gpu_id);
        $this->db->where('emp_id', $emp_id);
        if (!$this->db->get()->row()) {
            $this->session->set_flashdata('error', 'Grupo não encontrado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $usu_ids = $this->input->post('usu_ids');
        if (!is_array($usu_ids)) {
            $usu_ids = [];
        }
        $usu_ids = array_map('intval', $usu_ids);
        $usu_ids = array_filter($usu_ids);

        $now = date('Y-m-d H:i:s');

        foreach ($usu_ids as $usu_id) {
            if (!$usu_id) {
                continue;
            }
            $this->db->from('grupo_usuario_empresa');
            $this->db->where('usu_id', $usu_id);
            $this->db->where('emp_id', $emp_id);
            $existe = $this->db->get()->row();
            if ($existe) {
                $this->db->where('uge_id', $existe->uge_id);
                $this->db->update('grupo_usuario_empresa', [
                    'gpu_id' => $gpu_id,
                    'uge_data_atualizacao' => $now,
                ]);
            } else {
                $this->db->insert('grupo_usuario_empresa', [
                    'usu_id' => $usu_id,
                    'gpu_id' => $gpu_id,
                    'emp_id' => $emp_id,
                    'uge_data_cadastro' => $now,
                    'uge_data_atualizacao' => $now,
                ]);
            }
        }

        $this->db->from('grupo_usuario_empresa');
        $this->db->where('gpu_id', $gpu_id);
        $this->db->where('emp_id', $emp_id);
        $vinculos = $this->db->get()->result();
        foreach ($vinculos as $v) {
            if (!in_array((int) $v->usu_id, $usu_ids, true)) {
                $this->db->where('uge_id', $v->uge_id);
                $this->db->delete('grupo_usuario_empresa');
            }
        }

        $this->session->set_flashdata('success', 'Vínculos salvos com sucesso!');
        log_info('Salvou usuários do grupo. GPU_ID: ' . $gpu_id);
        redirect(site_url('gruposUsuario/usuarios/' . $gpu_id));
    }

    /**
     * Remove usuário do grupo (POST: uge_id).
     */
    public function removerUsuarioGrupo()
    {
        $emp_id = $this->session->userdata('emp_id');
        if (!$emp_id) {
            $this->session->set_flashdata('error', 'Nenhuma empresa selecionada.');
            redirect(base_url());
        }

        $uge_id = (int) $this->input->post('uge_id');
        if (!$uge_id) {
            $this->session->set_flashdata('error', 'Vínculo não informado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->from('grupo_usuario_empresa');
        $this->db->where('uge_id', $uge_id);
        $this->db->where('emp_id', $emp_id);
        $row = $this->db->get()->row();
        if (!$row) {
            $this->session->set_flashdata('error', 'Vínculo não encontrado.');
            redirect(site_url('gruposUsuario/gerenciar'));
        }

        $this->db->where('uge_id', $uge_id);
        $this->db->delete('grupo_usuario_empresa');
        $this->session->set_flashdata('success', 'Usuário removido do grupo.');
        log_info('Removeu usuário do grupo. UGE_ID: ' . $uge_id);
        redirect(site_url('gruposUsuario/usuarios/' . $row->gpu_id));
    }
}

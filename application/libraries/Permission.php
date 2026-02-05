<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Permission Class
 *
 * Controle de permissões baseado em grupo de usuário (grupo_usuario_empresa + grupo_usuario_permissoes).
 * Substitui o antigo sistema por perfil (tabela permissoes).
 *
 * v... Visualizar -> gup_visualizar
 * e... Editar -> gup_editar
 * d... Deletar -> gup_deletar
 * r... Relatório -> gup_relatorio
 * a... Alterar -> gup_alterar
 */
class Permission
{
    public function __construct()
    {
        log_message('debug', 'Permission Class Initialized');
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /**
     * Verifica se o usuário tem permissão para a atividade.
     * $idPermissao é ignorado (mantido por compatibilidade com chamadas existentes).
     */
    public function checkPermission($idPermissao = null, $atividade = null)
    {
        $CI = &get_instance();
        $is_super = $CI->session->userdata('is_super');

        if ($is_super) {
            return true;
        }

        if ($atividade === null || $atividade === '') {
            return false;
        }

        $usu_id = $CI->session->userdata('id_admin');
        $emp_id = $CI->session->userdata('emp_id');

        if (!$usu_id || !$emp_id) {
            return false;
        }

        // Novo sistema: permissão por grupo (grupo_usuario_empresa + grupo_usuario_permissoes + menu_empresa)
        if ($CI->db->table_exists('grupo_usuario_empresa') && $CI->db->table_exists('grupo_usuario_permissoes')
            && $CI->db->table_exists('menu_empresa') && $CI->db->table_exists('menus')) {

            $gpu_id = $CI->db->select('gpu_id')->from('grupo_usuario_empresa')
                ->where('usu_id', $usu_id)
                ->where('emp_id', $emp_id)
                ->limit(1)
                ->get()->row();

            if (!$gpu_id) {
                return false;
            }

            $gpu_id = (int) $gpu_id->gpu_id;

            // Menu que exige esta permissão: men_permissao pode ser vUsuario, vPessoa, etc.
            // Atividades c/e/d/a/r (ex: cUsuario) não têm menu próprio; usar o menu "v" correspondente (vUsuario)
            $men_permissao_busca = $atividade;
            if (strlen($atividade) > 1 && !in_array(substr($atividade, 0, 1), ['v'], true)) {
                $men_permissao_busca = 'v' . substr($atividade, 1);
            }
            $men = $CI->db->select('men_id')->from('menus')
                ->where('men_permissao', $men_permissao_busca)
                ->where('men_situacao', 1)
                ->order_by('men_ordem', 'ASC')
                ->limit(1)
                ->get()->row();

            if (!$men) {
                return false;
            }

            $mep = $CI->db->select('mep_id')->from('menu_empresa')
                ->where('men_id', $men->men_id)
                ->where('emp_id', $emp_id)
                ->limit(1)
                ->get()->row();

            if (!$mep) {
                return false;
            }

            $gup = $CI->db->select('gup_visualizar, gup_editar, gup_deletar, gup_alterar, gup_relatorio')
                ->from('grupo_usuario_permissoes')
                ->where('gpu_id', $gpu_id)
                ->where('mep_id', $mep->mep_id)
                ->limit(1)
                ->get()->row();

            if (!$gup) {
                return false;
            }

            $prefix = substr($atividade, 0, 1);
            switch ($prefix) {
                case 'v':
                    return !empty($gup->gup_visualizar);
                case 'e':
                    return !empty($gup->gup_editar);
                case 'd':
                    return !empty($gup->gup_deletar);
                case 'r':
                    return !empty($gup->gup_relatorio);
                case 'a':
                case 'c':
                    return !empty($gup->gup_alterar);
                default:
                    return !empty($gup->gup_visualizar);
            }
        }

        return false;
    }
}

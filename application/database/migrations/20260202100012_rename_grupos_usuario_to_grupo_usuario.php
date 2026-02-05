<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renomeia grupos_usuario para grupo_usuario e grupos_usuario_permissoes para grupo_usuario_permissoes.
 * Para bases que já executaram as migrations com os nomes antigos.
 */
class Migration_Rename_grupos_usuario_to_grupo_usuario extends CI_Migration {

    public function up()
    {
        if ($this->db->table_exists('grupos_usuario') && !$this->db->table_exists('grupo_usuario')) {
            $this->db->query('RENAME TABLE `grupos_usuario` TO `grupo_usuario`');
        }
        if ($this->db->table_exists('grupos_usuario_permissoes') && !$this->db->table_exists('grupo_usuario_permissoes')) {
            $this->db->query('RENAME TABLE `grupos_usuario_permissoes` TO `grupo_usuario_permissoes`');
        }
        if ($this->db->table_exists('menus')) {
            $this->db->where('men_identificador', 'grupos_usuario');
            $this->db->update('menus', ['men_identificador' => 'grupo_usuario']);
        }
    }

    public function down()
    {
        if ($this->db->table_exists('menus')) {
            $this->db->where('men_identificador', 'grupo_usuario');
            $this->db->update('menus', ['men_identificador' => 'grupos_usuario']);
        }
        if ($this->db->table_exists('grupo_usuario_permissoes') && !$this->db->table_exists('grupos_usuario_permissoes')) {
            $this->db->query('RENAME TABLE `grupo_usuario_permissoes` TO `grupos_usuario_permissoes`');
        }
        if ($this->db->table_exists('grupo_usuario') && !$this->db->table_exists('grupos_usuario')) {
            $this->db->query('RENAME TABLE `grupo_usuario` TO `grupos_usuario`');
        }
    }
}

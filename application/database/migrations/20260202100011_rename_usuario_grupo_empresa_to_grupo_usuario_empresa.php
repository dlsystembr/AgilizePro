<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renomeia a tabela usuario_grupo_empresa para grupo_usuario_empresa.
 * Para bases que já executaram a migration 20260202100010 com o nome antigo.
 */
class Migration_Rename_usuario_grupo_empresa_to_grupo_usuario_empresa extends CI_Migration {

    public function up()
    {
        if ($this->db->table_exists('usuario_grupo_empresa') && !$this->db->table_exists('grupo_usuario_empresa')) {
            $this->db->query('RENAME TABLE `usuario_grupo_empresa` TO `grupo_usuario_empresa`');
        }
    }

    public function down()
    {
        if ($this->db->table_exists('grupo_usuario_empresa') && !$this->db->table_exists('usuario_grupo_empresa')) {
            $this->db->query('RENAME TABLE `grupo_usuario_empresa` TO `usuario_grupo_empresa`');
        }
    }
}

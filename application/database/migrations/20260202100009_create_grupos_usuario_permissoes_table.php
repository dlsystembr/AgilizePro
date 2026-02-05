<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cria a tabela grupos_usuario_permissoes (por empresa).
 * Uma linha por (grupo, empresa, recurso) com colunas booleanas:
 * visualizar, editar, deletar, alterar, relatorio.
 */
class Migration_Create_grupos_usuario_permissoes_table extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists('grupos_usuario_permissoes')) {
            $this->dbforge->add_field([
                'gup_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ],
                'gpu_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE,
                ],
                'mep_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE,
                    'comment' => 'Menu liberado para a empresa (menu_empresa); recurso = menu',
                ],
                'gup_visualizar' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 0,
                ],
                'gup_editar' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 0,
                ],
                'gup_deletar' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 0,
                ],
                'gup_alterar' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 0,
                ],
                'gup_relatorio' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 0,
                ],
                'gup_data_cadastro' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'gup_data_atualizacao' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);
            $this->dbforge->add_key('gup_id', TRUE);
            $this->dbforge->create_table('grupo_usuario_permissoes', TRUE);
            $this->db->query('ALTER TABLE `grupo_usuario_permissoes` ADD UNIQUE KEY `uk_gup_gpu_mep` (`gpu_id`, `mep_id`)');
            if ($this->db->table_exists('grupo_usuario')) {
                $this->db->query('ALTER TABLE `grupo_usuario_permissoes` ADD CONSTRAINT `fk_gup_grupo_usuario` FOREIGN KEY (`gpu_id`) REFERENCES `grupo_usuario` (`gpu_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
            if ($this->db->table_exists('menu_empresa')) {
                $this->db->query('ALTER TABLE `grupo_usuario_permissoes` ADD CONSTRAINT `fk_gup_menu_empresa` FOREIGN KEY (`mep_id`) REFERENCES `menu_empresa` (`mep_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('grupo_usuario_permissoes')) {
            foreach (['fk_gup_menu_empresa', 'fk_gup_grupo_usuario'] as $fk) {
                try {
                    $this->db->query("ALTER TABLE `grupo_usuario_permissoes` DROP FOREIGN KEY `{$fk}`");
                } catch (Exception $e) { }
            }
            $this->dbforge->drop_table('grupo_usuario_permissoes', TRUE);
        }
    }
}

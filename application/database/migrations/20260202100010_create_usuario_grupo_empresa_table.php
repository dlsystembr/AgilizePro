<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cria a tabela grupo_usuario_empresa.
 * Vincula usuário a um grupo de usuário POR EMPRESA.
 * Regra: por empresa (emp_id) o usuário só pode estar em UM grupo; em outra empresa pode estar em outro grupo.
 * Tudo limitado ao mesmo GRE (grupo empresarial).
 */
class Migration_Create_usuario_grupo_empresa_table extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists('grupo_usuario_empresa')) {
            $this->dbforge->add_field([
                'uge_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ],
                'usu_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => FALSE,
                    'null' => FALSE,
                    'comment' => 'Usuário; mesmo tipo de usuarios.usu_id',
                ],
                'gpu_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE,
                    'comment' => 'Grupo de Usuário',
                ],
                'emp_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => FALSE,
                    'null' => FALSE,
                    'comment' => 'Empresa; mesmo tipo de empresas.emp_id',
                ],
                'uge_data_cadastro' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'uge_data_atualizacao' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);
            $this->dbforge->add_key('uge_id', TRUE);
            $this->dbforge->create_table('grupo_usuario_empresa', TRUE);
            $this->db->query('ALTER TABLE `grupo_usuario_empresa` ADD UNIQUE KEY `uk_uge_usu_emp` (`usu_id`, `emp_id`)');

            if ($this->db->table_exists('usuarios')) {
                $this->db->query('ALTER TABLE `grupo_usuario_empresa` ADD CONSTRAINT `fk_uge_usuarios` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
            if ($this->db->table_exists('grupos_usuario')) {
                $this->db->query('ALTER TABLE `grupo_usuario_empresa` ADD CONSTRAINT `fk_uge_grupos_usuario` FOREIGN KEY (`gpu_id`) REFERENCES `grupos_usuario` (`gpu_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
            if ($this->db->table_exists('empresas')) {
                $this->db->query('ALTER TABLE `grupo_usuario_empresa` ADD CONSTRAINT `fk_uge_empresas` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('grupo_usuario_empresa')) {
            foreach (['fk_uge_empresas', 'fk_uge_grupo_usuario', 'fk_uge_usuarios'] as $fk) {
                try {
                    $this->db->query("ALTER TABLE `grupo_usuario_empresa` DROP FOREIGN KEY `{$fk}`");
                } catch (Exception $e) { /* ignora */ }
            }
            $this->dbforge->drop_table('grupo_usuario_empresa', TRUE);
        }
    }
}

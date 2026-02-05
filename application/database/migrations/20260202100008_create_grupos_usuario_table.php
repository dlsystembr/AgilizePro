<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cria a tabela grupo_usuario (Grupo de Usuário) por EMPRESA.
 * Cada grupo pertence a uma empresa (emp_id). O GRE é obtido via empresas.gre_id quando necessário.
 */
class Migration_Create_grupos_usuario_table extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists('grupo_usuario')) {
            $this->dbforge->add_field([
                'gpu_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ],
                'emp_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => FALSE,
                    'null' => FALSE,
                    'comment' => 'Empresa (grupos são por empresa); mesmo tipo de empresas.emp_id',
                ],
                'gpu_nome' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => FALSE,
                ],
                'gpu_descricao' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE,
                ],
                'gpu_situacao' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 1,
                    'comment' => '1=ativo, 0=inativo',
                ],
                'gpu_data_cadastro' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'gpu_data_atualizacao' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);
            $this->dbforge->add_key('gpu_id', TRUE);
            $this->dbforge->create_table('grupo_usuario', TRUE);

            if ($this->db->table_exists('empresas')) {
                $this->db->query('ALTER TABLE `grupo_usuario` ADD CONSTRAINT `fk_grupo_usuario_empresa` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('grupo_usuario')) {
            try {
                $this->db->query('ALTER TABLE `grupo_usuario` DROP FOREIGN KEY `fk_grupo_usuario_empresa`');
            } catch (Exception $e) { /* ignora */ }
            $this->dbforge->drop_table('grupo_usuario', TRUE);
        }
    }
}

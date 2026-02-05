<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Adiciona emp_gre_id na tabela empresas existente (vínculo com grupo empresarial).
 * A tabela empresas já existe no sistema; não cria nova tabela.
 */
class Migration_Add_emp_gre_id_to_empresas extends CI_Migration {

    public function up()
    {
        if ($this->db->table_exists('empresas') && !$this->db->field_exists('emp_gre_id', 'empresas')) {
            $this->dbforge->add_column('empresas', array(
                'emp_gre_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            ));
            $this->db->query('ALTER TABLE `empresas` ADD KEY `idx_empresas_emp_gre_id` (`emp_gre_id`)');
            if ($this->db->table_exists('grupos_empresariais')) {
                $this->db->query('ALTER TABLE `empresas` ADD CONSTRAINT `fk_empresas_grupo` FOREIGN KEY (`emp_gre_id`) REFERENCES `grupos_empresariais` (`gre_id`) ON DELETE SET NULL ON UPDATE NO ACTION');
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('empresas') && $this->db->field_exists('emp_gre_id', 'empresas')) {
            try {
                $this->db->query('ALTER TABLE `empresas` DROP FOREIGN KEY `fk_empresas_grupo`');
            } catch (Exception $e) { /* ignora se não existir */ }
            try {
                $this->db->query('ALTER TABLE `empresas` DROP KEY `idx_empresas_emp_gre_id`');
            } catch (Exception $e) { /* ignora */ }
            $this->dbforge->drop_column('empresas', 'emp_gre_id');
        }
    }
}

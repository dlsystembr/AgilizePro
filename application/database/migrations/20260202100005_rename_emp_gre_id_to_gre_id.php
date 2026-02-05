<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renomeia emp_gre_id para gre_id na tabela empresas (regra: FK mantém nome da coluna da tabela pai).
 */
class Migration_Rename_emp_gre_id_to_gre_id extends CI_Migration {

    public function up()
    {
        if ($this->db->table_exists('empresas') && $this->db->field_exists('emp_gre_id', 'empresas')) {
            try {
                $this->db->query('ALTER TABLE `empresas` DROP FOREIGN KEY `fk_empresas_grupo`');
            } catch (Exception $e) { /* ignora */ }
            $this->db->query('ALTER TABLE `empresas` CHANGE `emp_gre_id` `gre_id` INT(11) UNSIGNED NULL DEFAULT NULL');
            $this->db->query('ALTER TABLE `empresas` ADD KEY `idx_empresas_gre_id` (`gre_id`)');
            if ($this->db->table_exists('grupos_empresariais')) {
                $this->db->query('ALTER TABLE `empresas` ADD CONSTRAINT `fk_empresas_grupo` FOREIGN KEY (`gre_id`) REFERENCES `grupos_empresariais` (`gre_id`) ON DELETE SET NULL ON UPDATE NO ACTION');
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('empresas') && $this->db->field_exists('gre_id', 'empresas')) {
            try {
                $this->db->query('ALTER TABLE `empresas` DROP FOREIGN KEY `fk_empresas_grupo`');
            } catch (Exception $e) { /* ignora */ }
            try {
                $this->db->query('ALTER TABLE `empresas` DROP KEY `idx_empresas_gre_id`');
            } catch (Exception $e) { /* ignora */ }
            $this->db->query('ALTER TABLE `empresas` CHANGE `gre_id` `emp_gre_id` INT(11) UNSIGNED NULL DEFAULT NULL');
            $this->db->query('ALTER TABLE `empresas` ADD KEY `idx_empresas_emp_gre_id` (`emp_gre_id`)');
            if ($this->db->table_exists('grupos_empresariais')) {
                $this->db->query('ALTER TABLE `empresas` ADD CONSTRAINT `fk_empresas_grupo` FOREIGN KEY (`emp_gre_id`) REFERENCES `grupos_empresariais` (`gre_id`) ON DELETE SET NULL ON UPDATE NO ACTION');
            }
        }
    }
}

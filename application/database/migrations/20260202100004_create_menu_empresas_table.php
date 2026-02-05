<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cria a tabela menu_empresa (ligação N:N entre empresas e menus).
 * Regras: tabela de ligação no SINGULAR; FKs mantêm nome da coluna da tabela pai (emp_id, men_id).
 */
class Migration_Create_menu_empresas_table extends CI_Migration {

    public function up()
    {
        // Compatibilidade: se existir tabela antiga menu_empresas (plural + prefixo nas FKs), renomear
        if ($this->db->table_exists('menu_empresas') && !$this->db->table_exists('menu_empresa')) {
            $this->db->query('RENAME TABLE `menu_empresas` TO `menu_empresa`');
            if ($this->db->field_exists('mep_emp_id', 'menu_empresa')) {
                $this->db->query('ALTER TABLE `menu_empresa` CHANGE `mep_emp_id` `emp_id` INT(11) NOT NULL');
            }
            if ($this->db->field_exists('mep_men_id', 'menu_empresa')) {
                $this->db->query('ALTER TABLE `menu_empresa` CHANGE `mep_men_id` `men_id` INT(11) UNSIGNED NOT NULL');
            }
            try {
                $this->db->query('ALTER TABLE `menu_empresa` DROP FOREIGN KEY `fk_mep_emp`');
            } catch (Exception $e) { /* ignora */ }
            try {
                $this->db->query('ALTER TABLE `menu_empresa` DROP FOREIGN KEY `fk_mep_men`');
            } catch (Exception $e) { /* ignora */ }
            $this->db->query('ALTER TABLE `menu_empresa` ADD CONSTRAINT `fk_menu_empresa_emp` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            $this->db->query('ALTER TABLE `menu_empresa` ADD CONSTRAINT `fk_menu_empresa_men` FOREIGN KEY (`men_id`) REFERENCES `menus` (`men_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            return;
        }

        if (!$this->db->table_exists('menu_empresa')) {
            $this->dbforge->add_field(array(
                'mep_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                    'null' => FALSE,
                ),
                'emp_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => FALSE,
                    'null' => FALSE,
                ),
                'men_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => FALSE,
                ),
                'mep_data_cadastro' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ),
            ));
            $this->dbforge->add_key('mep_id', TRUE);
            $this->dbforge->add_key(array('emp_id', 'men_id'), FALSE, TRUE);
            $this->dbforge->create_table('menu_empresa', TRUE);

            if ($this->db->table_exists('empresas') && $this->db->table_exists('menus')) {
                $this->db->query('ALTER TABLE `menu_empresa` ADD CONSTRAINT `fk_menu_empresa_emp` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
                $this->db->query('ALTER TABLE `menu_empresa` ADD CONSTRAINT `fk_menu_empresa_men` FOREIGN KEY (`men_id`) REFERENCES `menus` (`men_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
        }
    }

    public function down()
    {
        if ($this->db->table_exists('menu_empresa')) {
            try {
                $this->db->query('ALTER TABLE `menu_empresa` DROP FOREIGN KEY `fk_menu_empresa_emp`');
            } catch (Exception $e) { /* ignora */ }
            try {
                $this->db->query('ALTER TABLE `menu_empresa` DROP FOREIGN KEY `fk_menu_empresa_men`');
            } catch (Exception $e) { /* ignora */ }
            $this->dbforge->drop_table('menu_empresa', TRUE);
        }
    }
}

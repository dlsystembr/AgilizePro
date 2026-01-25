<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_tenant_permissoes_menu_table extends CI_Migration {

    public function up()
    {
        // Criar tabela tenant_permissoes_menu para controlar quais menus cada tenant pode acessar
        $this->dbforge->add_field(array(
            'tpm_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'tpm_ten_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'tpm_menu_codigo' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE,
            ),
            'tpm_permissao' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE,
                'comment' => 'Código da permissão do menu (ex: vCliente, aCliente, etc)',
            ),
            'tpm_ativo' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 1,
            ),
            'tpm_data_cadastro' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
        ));
        
        $this->dbforge->add_key('tpm_id', TRUE);
        $this->dbforge->create_table('tenant_permissoes_menu', TRUE);
        
        // Adicionar foreign key para tenants
        $this->db->query('ALTER TABLE `tenant_permissoes_menu` 
            ADD CONSTRAINT `fk_tenant_permissoes_menu_tenant` 
            FOREIGN KEY (`tpm_ten_id`) 
            REFERENCES `tenants` (`ten_id`) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE');
        
        // Criar índice único para evitar duplicatas
        $this->db->query('ALTER TABLE `tenant_permissoes_menu` 
            ADD UNIQUE INDEX `uk_tenant_menu_permissao` (`tpm_ten_id`, `tpm_menu_codigo`, `tpm_permissao`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `tenant_permissoes_menu` DROP FOREIGN KEY `fk_tenant_permissoes_menu_tenant`');
        $this->dbforge->drop_table('tenant_permissoes_menu', TRUE);
    }
}


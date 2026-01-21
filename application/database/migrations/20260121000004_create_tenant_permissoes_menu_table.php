<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_tenant_permissoes_menu_table extends CI_Migration {

    public function up()
    {
        // Criar tabela tenant_permissoes_menu para controlar quais menus cada tenant pode acessar
        $this->dbforge->add_field(array(
            'TPM_ID' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'TPM_TEN_ID' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'TPM_MENU_CODIGO' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE,
            ),
            'TPM_PERMISSAO' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE,
                'comment' => 'Código da permissão do menu (ex: vCliente, aCliente, etc)',
            ),
            'TPM_ATIVO' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 1,
            ),
            'TPM_DATA_CADASTRO' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
        ));
        
        $this->dbforge->add_key('TPM_ID', TRUE);
        $this->dbforge->create_table('tenant_permissoes_menu', TRUE);
        
        // Adicionar foreign key para tenants
        $this->db->query('ALTER TABLE `tenant_permissoes_menu` 
            ADD CONSTRAINT `fk_tenant_permissoes_menu_tenant` 
            FOREIGN KEY (`TPM_TEN_ID`) 
            REFERENCES `tenants` (`ten_id`) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE');
        
        // Criar índice único para evitar duplicatas
        $this->db->query('ALTER TABLE `tenant_permissoes_menu` 
            ADD UNIQUE INDEX `uk_tenant_menu_permissao` (`TPM_TEN_ID`, `TPM_MENU_CODIGO`, `TPM_PERMISSAO`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `tenant_permissoes_menu` DROP FOREIGN KEY `fk_tenant_permissoes_menu_tenant`');
        $this->dbforge->drop_table('tenant_permissoes_menu', TRUE);
    }
}


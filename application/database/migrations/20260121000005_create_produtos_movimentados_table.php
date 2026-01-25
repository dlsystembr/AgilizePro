<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_produtos_movimentados_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'PDM_ID' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'PDM_QTDE' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => 'Quantidade movimentada'
            ),
            'PDM_TIPO' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE,
                'comment' => 'Tipo de movimentação: ENTRADA ou SAIDA'
            ),
            'ITF_ID' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
                'comment' => 'ID do item faturado (FK para itens_faturados)'
            ),
            'PDM_DATA' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
                'comment' => 'Data da movimentação'
            ),
        ));
        
        $this->dbforge->add_key('PDM_ID', TRUE);
        $this->dbforge->create_table('produtos_movimentados', TRUE);
        
        // Adicionar foreign key para itens_faturados
        $this->db->query('ALTER TABLE `produtos_movimentados` 
            ADD CONSTRAINT `fk_produtos_movimentados_itens_faturados` 
            FOREIGN KEY (`ITF_ID`) 
            REFERENCES `itens_faturados` (`ITF_ID`) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE');
        
        // Adicionar índice para melhor performance nas consultas
        $this->db->query('CREATE INDEX `idx_produtos_movimentados_itf` ON `produtos_movimentados` (`ITF_ID`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('produtos_movimentados', TRUE);
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_produtos_movimentados_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'pdm_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'pdm_qtde' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE,
                'default' => 0.00,
                'comment' => 'Quantidade movimentada'
            ),
            'pdm_tipo' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE,
                'comment' => 'Tipo de movimentação: ENTRADA ou SAIDA'
            ),
            'itf_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
                'comment' => 'ID do item faturado (FK para itens_faturados)'
            ),
            'pdm_data' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
                'comment' => 'Data da movimentação'
            ),
        ));
        
        $this->dbforge->add_key('pdm_id', TRUE);
        $this->dbforge->create_table('produtos_movimentados', TRUE);
        
        // Adicionar foreign key para itens_faturados
        $this->db->query('ALTER TABLE `produtos_movimentados` 
            ADD CONSTRAINT `fk_produtos_movimentados_itens_faturados` 
            FOREIGN KEY (`itf_id`) 
            REFERENCES `itens_faturados` (`itf_id`) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE');
        
        // Adicionar índice para melhor performance nas consultas
        $this->db->query('CREATE INDEX `idx_produtos_movimentados_itf` ON `produtos_movimentados` (`itf_id`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('produtos_movimentados', TRUE);
    }
}


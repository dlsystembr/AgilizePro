<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_operacao_comercial_to_vendas extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('vendas', [
            'operacao_comercial_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'clientes_id'
            ]
        ]);

        // Adiciona a chave estrangeira
        $this->db->query('ALTER TABLE vendas ADD CONSTRAINT fk_vendas_operacao_comercial FOREIGN KEY (operacao_comercial_id) REFERENCES operacao_comercial(id) ON DELETE NO ACTION ON UPDATE NO ACTION');
    }

    public function down()
    {
        // Remove a chave estrangeira primeiro
        $this->db->query('ALTER TABLE vendas DROP FOREIGN KEY fk_vendas_operacao_comercial');
        
        // Remove a coluna
        $this->dbforge->drop_column('vendas', 'operacao_comercial_id');
    }
} 
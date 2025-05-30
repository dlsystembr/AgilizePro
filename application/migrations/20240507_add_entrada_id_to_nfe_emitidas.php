<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_entrada_id_to_nfe_emitidas extends CI_Migration
{
    public function up()
    {
        // Adiciona a coluna entrada_id
        $this->dbforge->add_column('nfe_emitidas', [
            'entrada_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'id'
            ]
        ]);

        // Adiciona índice para melhorar performance
        $this->db->query('ALTER TABLE nfe_emitidas ADD INDEX idx_entrada_id (entrada_id)');
    }

    public function down()
    {
        // Remove o índice primeiro
        $this->db->query('ALTER TABLE nfe_emitidas DROP INDEX idx_entrada_id');
        
        // Remove a coluna
        $this->dbforge->drop_column('nfe_emitidas', 'entrada_id');
    }
} 
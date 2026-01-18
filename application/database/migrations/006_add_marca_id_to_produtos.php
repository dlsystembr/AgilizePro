<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_marca_id_to_produtos extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('produtos', [
            'mrc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'ncm_id'
            ]
        ]);

        // Adiciona a chave estrangeira
        $this->db->query('ALTER TABLE produtos ADD CONSTRAINT fk_produtos_marcas FOREIGN KEY (mrc_id) REFERENCES marcas(mrc_id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Remove a chave estrangeira
        $this->db->query('ALTER TABLE produtos DROP FOREIGN KEY fk_produtos_marcas');
        
        // Remove a coluna
        $this->dbforge->drop_column('produtos', 'mrc_id');
    }
} 
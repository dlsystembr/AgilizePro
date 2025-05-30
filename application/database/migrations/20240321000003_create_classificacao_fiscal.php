<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_classificacao_fiscal extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'operacao_comercial_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'cst' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
                'null' => false,
            ],
            'natureza_contribuinte' => [
                'type' => 'ENUM',
                'constraint' => ['inscrito', 'nao_inscrito'],
                'default' => 'nao_inscrito',
                'null' => false,
            ],
            'cfop' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
                'null' => false,
            ],
            'destinacao' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_field('CONSTRAINT `classificacao_fiscal_operacao_comercial_fk` FOREIGN KEY (operacao_comercial_id) REFERENCES operacao_comercial (id) ON DELETE CASCADE');
        
        $this->dbforge->create_table('classificacao_fiscal');
    }

    public function down()
    {
        $this->dbforge->drop_table('classificacao_fiscal');
    }
} 
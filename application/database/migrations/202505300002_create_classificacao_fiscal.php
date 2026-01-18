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
                'unsigned' => true,
            ],
            'cst' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
            ],
            'natureza_contribuinte' => [
                'type' => 'ENUM("inscrito","nao_inscrito")',
                'default' => 'nao_inscrito',
            ],
            'cfop' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
            ],
            'destinacao' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (operacao_comercial_id) REFERENCES operacao_comercial(id) ON DELETE CASCADE');
        $this->dbforge->create_table('classificacao_fiscal');
    }

    public function down()
    {
        $this->dbforge->drop_table('classificacao_fiscal');
    }
} 
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_aliquotas extends CI_Migration
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
            'cst' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
                'null' => false,
            ],
            'destinacao' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'natureza_contribuinte' => [
                'type' => 'ENUM',
                'constraint' => ['inscrito', 'nao_inscrito'],
                'default' => 'nao_inscrito',
                'null' => false,
            ],
            'aliquota_icms' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
            ],
            'aliquota_icms_st' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
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
        $this->dbforge->create_table('aliquotas');
    }

    public function down()
    {
        $this->dbforge->drop_table('aliquotas');
    }
} 
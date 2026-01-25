<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_contratos_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'ctr_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pes_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'ctr_numero' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => false,
            ],
            'ctr_data_inicio' => [
                'type' => 'date',
                'null' => false,
            ],
            'ctr_data_fim' => [
                'type' => 'date',
                'null' => true,
            ],
            'ctr_tipo_assinante' => [
                'type' => 'ENUM',
                'constraint' => ['1', '2', '3', '4', '5', '6', '7', '8', '99'],
                'null' => false,
                'comment' => '1-Comercial, 2-Industrial, 3-Residencia/PF, 4-Produtor Rural, 5-Orgão Público Estadual, 6-Prestador de Telecom, 7-Missões Diplomáticas, 8-Igrejas e Templos, 99-Outros',
            ],
            'ctr_anexo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'ctr_observacao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ctr_situacao' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
                'comment' => '1-Ativo, 0-Inativo',
            ],
            'ctr_data_cadastro' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'ctr_data_alteracao' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->dbforge->add_key('ctr_id', true);
        $this->dbforge->add_key('pes_id');
        $this->dbforge->add_key('ctr_numero');
        $this->dbforge->create_table('contratos');
        
        // Adicionar constraint de foreign key
        $this->db->query('ALTER TABLE `contratos` ADD CONSTRAINT `fk_contratos_pessoas` FOREIGN KEY (`pes_id`) REFERENCES `pessoas` (`pes_id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        
        // Adicionar índice único para número do contrato
        $this->db->query('ALTER TABLE `contratos` ADD UNIQUE INDEX `idx_ctr_numero` (`ctr_numero`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `contratos` DROP FOREIGN KEY `fk_contratos_pessoas`');
        $this->dbforge->drop_table('contratos');
    }
}

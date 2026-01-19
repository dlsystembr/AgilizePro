<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_contratos_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'CTR_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'PES_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'CTR_NUMERO' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => false,
            ],
            'CTR_DATA_INICIO' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'CTR_DATA_FIM' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'CTR_TIPO_ASSINANTE' => [
                'type' => 'ENUM',
                'constraint' => ['1', '2', '3', '4', '5', '6', '7', '8', '99'],
                'null' => false,
                'comment' => '1-Comercial, 2-Industrial, 3-Residencia/PF, 4-Produtor Rural, 5-Orgão Público Estadual, 6-Prestador de Telecom, 7-Missões Diplomáticas, 8-Igrejas e Templos, 99-Outros',
            ],
            'CTR_ANEXO' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'CTR_OBSERVACAO' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'CTR_SITUACAO' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
                'comment' => '1-Ativo, 0-Inativo',
            ],
            'CTR_DATA_CADASTRO' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'CTR_DATA_ALTERACAO' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->dbforge->add_key('CTR_ID', true);
        $this->dbforge->add_key('PES_ID');
        $this->dbforge->add_key('CTR_NUMERO');
        $this->dbforge->create_table('contratos');
        
        // Adicionar constraint de foreign key
        $this->db->query('ALTER TABLE `contratos` ADD CONSTRAINT `fk_contratos_pessoas` FOREIGN KEY (`PES_ID`) REFERENCES `pessoas` (`PES_ID`) ON DELETE RESTRICT ON UPDATE CASCADE');
        
        // Adicionar índice único para número do contrato
        $this->db->query('ALTER TABLE `contratos` ADD UNIQUE INDEX `idx_ctr_numero` (`CTR_NUMERO`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `contratos` DROP FOREIGN KEY `fk_contratos_pessoas`');
        $this->dbforge->drop_table('contratos');
    }
}

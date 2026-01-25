<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Recreate_classificacao_fiscal_dual extends CI_Migration
{
    public function up()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->dbforge->drop_table('classificacao_fiscal', true);

        // Cria tabela com colunas novas (CLF_*) e também legadas para compatibilidade
        $fields = [
            'clf_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'CLF_OPC_ID' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
            'clf_cst' => [ 'type' => 'VARCHAR', 'constraint' => 2, 'null' => true ],
            'clf_csosn' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
            'CLF_NATUREZA_CONTRIB' => [ 'type' => 'ENUM("inscrito","nao_inscrito")', 'default' => 'nao_inscrito', 'null' => false ],
            'clf_cfop' => [ 'type' => 'VARCHAR', 'constraint' => 4, 'null' => false ],
            'clf_destinacao' => [ 'type' => 'VARCHAR', 'constraint' => 100, 'null' => false ],
            'clf_objetivo_comercial' => [ 'type' => 'ENUM("consumo","revenda")', 'default' => 'consumo', 'null' => false ],
            'CLF_TIPO_ICMS' => [ 'type' => 'ENUM("normal","st")', 'default' => 'normal', 'null' => false ],
            'clf_data_inclusao' => [ 'type' => 'DATETIME', 'null' => true ],
            'clf_data_alteracao' => [ 'type' => 'DATETIME', 'null' => true ],

            // Colunas legadas para compatibilidade
            'operacao_comercial_id' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
            'cst' => [ 'type' => 'VARCHAR', 'constraint' => 2, 'null' => true ],
            'csosn' => [ 'type' => 'VARCHAR', 'constraint' => 3, 'null' => true ],
            'natureza_contribuinte' => [ 'type' => 'ENUM("inscrito","nao_inscrito")', 'default' => 'nao_inscrito', 'null' => true ],
            'cfop' => [ 'type' => 'VARCHAR', 'constraint' => 4, 'null' => true ],
            'destinacao' => [ 'type' => 'VARCHAR', 'constraint' => 100, 'null' => true ],
            'objetivo_comercial' => [ 'type' => 'ENUM("consumo","revenda")', 'default' => 'consumo', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('clf_id', true);
        $this->dbforge->create_table('classificacao_fiscal', true);

        // FK apenas na coluna nova padronizada
        $this->db->query('ALTER TABLE `classificacao_fiscal` ADD CONSTRAINT `clf_opc_fk` FOREIGN KEY (`CLF_OPC_ID`) REFERENCES `operacao_comercial`(`opc_id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->dbforge->drop_table('classificacao_fiscal', true);
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }
}



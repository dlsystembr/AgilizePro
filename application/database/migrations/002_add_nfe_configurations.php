<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_nfe_configurations extends CI_Migration
{
    public function up()
    {
        // Add new columns to configuracoes table
        $fields = array(
            'ambiente' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 2,
                'comment' => '1 = Produção, 2 = Homologação'
            ),
            'versao_nfe' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => '4.00'
            ),
            'tipo_impressao_danfe' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1 = Normal, 2 = DANFE Simplificado'
            ),
            'orientacao_danfe' => array(
                'type' => 'CHAR',
                'constraint' => 1,
                'default' => 'P',
                'comment' => 'P = Retrato, L = Paisagem'
            ),
            'csc' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ),
            'csc_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            )
        );

        $this->dbforge->add_column('configuracoes', $fields);
    }

    public function down()
    {
        // Remove the columns if needed
        $this->dbforge->drop_column('configuracoes', 'ambiente');
        $this->dbforge->drop_column('configuracoes', 'versao_nfe');
        $this->dbforge->drop_column('configuracoes', 'tipo_impressao_danfe');
        $this->dbforge->drop_column('configuracoes', 'orientacao_danfe');
        $this->dbforge->drop_column('configuracoes', 'csc');
        $this->dbforge->drop_column('configuracoes', 'csc_id');
    }
} 
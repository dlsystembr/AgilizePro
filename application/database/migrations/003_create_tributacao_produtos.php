<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_tributacao_produtos extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'nome_configuracao' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => FALSE
            ),
            'cst_ipi_saida' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE
            ),
            'aliq_ipi_saida' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE
            ),
            'cst_pis_saida' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE
            ),
            'aliq_pis_saida' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE
            ),
            'cst_cofins_saida' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE
            ),
            'aliq_cofins_saida' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => FALSE
            ),
            'aliq_red_icms' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE
            ),
            'aliq_iva' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE
            ),
            'aliq_rd_icms_st' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE
            ),
            'regime_fiscal_tributario' => array(
                'type' => 'ENUM',
                'constraint' => array('ICMS Normal (Tributado)', 'Substituição Tributária'),
                'null' => FALSE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
                'default' => 'CURRENT_TIMESTAMP'
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('tributacao_produto');
    }

    public function down()
    {
        $this->dbforge->drop_table('tributacao_produto');
    }
} 
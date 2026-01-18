<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_tributacao_produto extends CI_Migration
{
    public function up()
    {
        // Criar tabela tributacao_produto
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
            'regime_fiscal_tributario' => array(
                'type' => 'ENUM',
                'constraint' => array('ICMS Normal (Tributado)', 'Substituição Tributária'),
                'null' => FALSE
            ),
            'aliq_red_icms' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
                'default' => 0.00
            ),
            'aliq_iva' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
                'default' => 0.00
            ),
            'aliq_rd_icms_st' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
                'default' => 0.00
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

        // Adicionar coluna tributacao_produto_id na tabela produtos
        $fields = array(
            'tributacao_produto_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'after' => 'entrada'
            )
        );
        $this->dbforge->add_column('produtos', $fields);

        // Inserir tributação padrão
        $data = array(
            'nome_configuracao' => 'Configuração Padrão',
            'cst_ipi_saida' => '999',
            'aliq_ipi_saida' => 0.00,
            'cst_pis_saida' => '01',
            'aliq_pis_saida' => 0.65,
            'cst_cofins_saida' => '01',
            'aliq_cofins_saida' => 3.00,
            'regime_fiscal_tributario' => 'ICMS Normal (Tributado)',
            'aliq_red_icms' => 0.00,
            'aliq_iva' => 0.00,
            'aliq_rd_icms_st' => 0.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('tributacao_produto', $data);
    }

    public function down()
    {
        $this->dbforge->drop_column('produtos', 'tributacao_produto_id');
        $this->dbforge->drop_table('tributacao_produto');
    }
} 
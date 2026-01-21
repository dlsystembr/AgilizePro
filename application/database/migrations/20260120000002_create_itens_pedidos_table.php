<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create ITENS_PEDIDOS table
 * 
 * Tabela de itens de pedidos (substitui itens_de_vendas)
 * Prefixo: ITP_
 * Tipo: PLURAL (tabela de detalhe)
 */
class Migration_Create_itens_pedidos_table extends CI_Migration
{
    public function up()
    {
        // Criar tabela ITENS_PEDIDOS
        $this->dbforge->add_field([
            'ITP_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'ITP_SUBTOTAL' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'ITP_QUANTIDADE' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'ITP_PRECO' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'PDS_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
                'comment' => 'FK para PEDIDOS (mantém nome original)'
            ],
            'PRO_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'comment' => 'FK para produtos (mantém nome original)'
            ]
        ]);

        $this->dbforge->add_key('ITP_ID', TRUE);
        $this->dbforge->create_table('ITENS_PEDIDOS', TRUE);

        // Adicionar índices
        $this->db->query('CREATE INDEX idx_itens_pedidos_pds_id ON ITENS_PEDIDOS(PDS_ID)');
        $this->db->query('CREATE INDEX idx_itens_pedidos_pro_id ON ITENS_PEDIDOS(PRO_ID)');

        // Adicionar foreign keys
        $this->db->query('
            ALTER TABLE ITENS_PEDIDOS
            ADD CONSTRAINT fk_itens_pedidos_pedidos
            FOREIGN KEY (PDS_ID) REFERENCES PEDIDOS(PDS_ID)
            ON DELETE CASCADE ON UPDATE NO ACTION
        ');

        $this->db->query('
            ALTER TABLE ITENS_PEDIDOS
            ADD CONSTRAINT fk_itens_pedidos_produtos
            FOREIGN KEY (PRO_ID) REFERENCES produtos(idProdutos)
            ON DELETE NO ACTION ON UPDATE NO ACTION
        ');
    }

    public function down()
    {
        // Remover foreign keys primeiro
        $this->db->query('ALTER TABLE ITENS_PEDIDOS DROP FOREIGN KEY fk_itens_pedidos_pedidos');
        $this->db->query('ALTER TABLE ITENS_PEDIDOS DROP FOREIGN KEY fk_itens_pedidos_produtos');
        
        // Remover tabela
        $this->dbforge->drop_table('ITENS_PEDIDOS', TRUE);
    }
}

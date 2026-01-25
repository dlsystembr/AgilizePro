<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create PEDIDOS table
 * 
 * Tabela principal de pedidos (substitui vendas)
 * Prefixo: PDS_
 * Tipo: PLURAL (tabela principal)
 */
class Migration_Create_pedidos_table extends CI_Migration
{
    public function up()
    {
        // Criar tabela PEDIDOS
        $this->dbforge->add_field([
            'pds_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'pds_data' => [
                'type' => 'date',
                'null' => TRUE
            ],
            'pds_valor_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'pds_desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'pds_valor_desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'pds_tipo_desconto' => [
                'type' => 'VARCHAR',
                'constraint' => 8,
                'null' => TRUE
            ],
            'pds_faturado' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => TRUE,
                'default' => 0
            ],
            'pds_observacoes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'pds_observacoes_cliente' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'pds_status' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE
            ],
            'pds_garantia' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'pds_tipo' => [
                'type' => 'ENUM',
                'constraint' => ['COMPRA', 'VENDA'],
                'null' => FALSE,
                'default' => 'VENDA'
            ],
            'pds_operacao_comercial' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'pes_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'comment' => 'FK para pessoas (cliente - mantém nome original)'
            ],
            'usu_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'FK para usuarios (mantém nome original)'
            ],
            'lan_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'FK para lancamentos (mantém nome original)'
            ]
        ]);

        $this->dbforge->add_key('pds_id', TRUE);
        $this->dbforge->create_table('PEDIDOS', TRUE);

        // Adicionar índices
        $this->db->query('CREATE INDEX idx_pedidos_pes_id ON PEDIDOS(pes_id)');
        $this->db->query('CREATE INDEX idx_pedidos_usu_id ON PEDIDOS(usu_id)');
        $this->db->query('CREATE INDEX idx_pedidos_lan_id ON PEDIDOS(lan_id)');
        $this->db->query('CREATE INDEX idx_pedidos_tipo ON PEDIDOS(pds_tipo)');
        $this->db->query('CREATE INDEX idx_pedidos_status ON PEDIDOS(pds_status)');

        // Adicionar foreign keys (se as tabelas referenciadas existirem)
        $this->db->query('
            ALTER TABLE PEDIDOS
            ADD CONSTRAINT fk_pedidos_pessoas
            FOREIGN KEY (pes_id) REFERENCES pessoas(pes_id)
            ON DELETE NO ACTION ON UPDATE NO ACTION
        ');

        $this->db->query('
            ALTER TABLE PEDIDOS
            ADD CONSTRAINT fk_pedidos_usuarios
            FOREIGN KEY (usu_id) REFERENCES usuarios(idUsuarios)
            ON DELETE NO ACTION ON UPDATE NO ACTION
        ');

        $this->db->query('
            ALTER TABLE PEDIDOS
            ADD CONSTRAINT fk_pedidos_lancamentos
            FOREIGN KEY (lan_id) REFERENCES lancamentos(idLancamentos)
            ON DELETE NO ACTION ON UPDATE NO ACTION
        ');
    }

    public function down()
    {
        // Remover foreign keys primeiro
        $this->db->query('ALTER TABLE PEDIDOS DROP FOREIGN KEY fk_pedidos_pessoas');
        $this->db->query('ALTER TABLE PEDIDOS DROP FOREIGN KEY fk_pedidos_usuarios');
        $this->db->query('ALTER TABLE PEDIDOS DROP FOREIGN KEY fk_pedidos_lancamentos');
        
        // Remover tabela
        $this->dbforge->drop_table('PEDIDOS', TRUE);
    }
}

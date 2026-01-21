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
            'PDS_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'PDS_DATA' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'PDS_VALOR_TOTAL' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'PDS_DESCONTO' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'PDS_VALOR_DESCONTO' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => 0
            ],
            'PDS_TIPO_DESCONTO' => [
                'type' => 'VARCHAR',
                'constraint' => 8,
                'null' => TRUE
            ],
            'PDS_FATURADO' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => TRUE,
                'default' => 0
            ],
            'PDS_OBSERVACOES' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'PDS_OBSERVACOES_CLIENTE' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'PDS_STATUS' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE
            ],
            'PDS_GARANTIA' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'PDS_TIPO' => [
                'type' => 'ENUM',
                'constraint' => ['COMPRA', 'VENDA'],
                'null' => FALSE,
                'default' => 'VENDA'
            ],
            'PDS_OPERACAO_COMERCIAL' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'PES_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
                'comment' => 'FK para pessoas (cliente - mantém nome original)'
            ],
            'USU_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'FK para usuarios (mantém nome original)'
            ],
            'LAN_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'comment' => 'FK para lancamentos (mantém nome original)'
            ]
        ]);

        $this->dbforge->add_key('PDS_ID', TRUE);
        $this->dbforge->create_table('PEDIDOS', TRUE);

        // Adicionar índices
        $this->db->query('CREATE INDEX idx_pedidos_pes_id ON PEDIDOS(PES_ID)');
        $this->db->query('CREATE INDEX idx_pedidos_usu_id ON PEDIDOS(USU_ID)');
        $this->db->query('CREATE INDEX idx_pedidos_lan_id ON PEDIDOS(LAN_ID)');
        $this->db->query('CREATE INDEX idx_pedidos_tipo ON PEDIDOS(PDS_TIPO)');
        $this->db->query('CREATE INDEX idx_pedidos_status ON PEDIDOS(PDS_STATUS)');

        // Adicionar foreign keys (se as tabelas referenciadas existirem)
        $this->db->query('
            ALTER TABLE PEDIDOS
            ADD CONSTRAINT fk_pedidos_pessoas
            FOREIGN KEY (PES_ID) REFERENCES pessoas(PES_ID)
            ON DELETE NO ACTION ON UPDATE NO ACTION
        ');

        $this->db->query('
            ALTER TABLE PEDIDOS
            ADD CONSTRAINT fk_pedidos_usuarios
            FOREIGN KEY (USU_ID) REFERENCES usuarios(idUsuarios)
            ON DELETE NO ACTION ON UPDATE NO ACTION
        ');

        $this->db->query('
            ALTER TABLE PEDIDOS
            ADD CONSTRAINT fk_pedidos_lancamentos
            FOREIGN KEY (LAN_ID) REFERENCES lancamentos(idLancamentos)
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

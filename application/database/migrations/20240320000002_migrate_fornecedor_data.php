<?php

class Migration_migrate_fornecedor_data extends CI_Migration
{
    public function up()
    {
        // Obter ID do tipo Fornecedor
        $fornecedor_tipo = $this->db->get_where('tipos_pessoa', ['nome' => 'Fornecedor'])->row();
        $cliente_tipo = $this->db->get_where('tipos_pessoa', ['nome' => 'Cliente'])->row();

        if ($fornecedor_tipo && $cliente_tipo) {
            // Migrar fornecedores
            $this->db->query("
                INSERT INTO pessoa_tipos (pessoa_id, tipo_id)
                SELECT idClientes, {$fornecedor_tipo->id}
                FROM clientes
                WHERE fornecedor = 1
            ");

            // Migrar clientes
            $this->db->query("
                INSERT INTO pessoa_tipos (pessoa_id, tipo_id)
                SELECT idClientes, {$cliente_tipo->id}
                FROM clientes
                WHERE fornecedor = 0
            ");
        }
    }

    public function down()
    {
        // Não é possível reverter a migração de dados de forma segura
        // pois não temos como saber quais registros foram inseridos pela migração
    }
} 
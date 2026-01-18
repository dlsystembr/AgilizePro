<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Check_permissions_table extends CI_Migration
{
    public function up()
    {
        // Verifica se a tabela existe
        if (!$this->db->table_exists('permissoes')) {
            // Cria a tabela se não existir
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'situacao' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1
                ],
                'nome' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80
                ],
                'permissao' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80
                ]
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('permissoes');
        }

        // Insere a permissão de NCMs
        $this->db->where('permissao', 'vNcm');
        $exists = $this->db->get('permissoes')->num_rows();

        if ($exists == 0) {
            $data = array(
                'situacao' => 1,
                'nome' => 'Visualizar NCMs',
                'permissao' => 'vNcm'
            );
            $this->db->insert('permissoes', $data);
        }
    }

    public function down()
    {
        $this->db->where('permissao', 'vNcm');
        $this->db->delete('permissoes');
    }
} 
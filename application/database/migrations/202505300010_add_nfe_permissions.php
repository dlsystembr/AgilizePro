<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_nfe_permissions extends CI_Migration
{
    public function up()
    {
        $data = [
            [
                'nome' => 'vNfe',
                'descricao' => 'Visualizar Emissor de Notas',
                'situacao' => 1
            ],
            [
                'nome' => 'eNfe',
                'descricao' => 'Editar Emissor de Notas',
                'situacao' => 1
            ]
        ];

        $this->db->insert_batch('permissoes', $data);
    }

    public function down()
    {
        $this->db->where_in('nome', ['vNfe', 'eNfe']);
        $this->db->delete('permissoes');
    }
} 
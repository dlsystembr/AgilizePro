<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_ncm_permission_fix extends CI_Migration
{
    public function up()
    {
        // Verifica se a permissão já existe
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
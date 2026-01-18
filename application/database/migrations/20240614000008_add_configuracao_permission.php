<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_configuracao_permission extends CI_Migration
{
    public function up()
    {
        // Verifica se a permissão já existe
        $this->db->where('permissao', 'vConfiguracao');
        $exists = $this->db->get('permissoes')->num_rows();

        if ($exists == 0) {
            $data = array(
                'situacao' => 1,
                'nome' => 'Visualizar Configurações',
                'permissao' => 'vConfiguracao'
            );
            $this->db->insert('permissoes', $data);
        }
    }

    public function down()
    {
        $this->db->where('permissao', 'vConfiguracao');
        $this->db->delete('permissoes');
    }
} 
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_config_ncm_permissions_to_table extends CI_Migration
{
    public function up()
    {
        // Verifica se a permissão vConfiguracao já existe
        $this->db->where('permissao', 'vConfiguracao');
        $exists_config = $this->db->get('permissoes')->num_rows();

        if ($exists_config == 0) {
            $data = array(
                'situacao' => 1,
                'nome' => 'Visualizar Configurações',
                'permissao' => 'vConfiguracao'
            );
            $this->db->insert('permissoes', $data);
        }

        // Verifica se a permissão vNcm já existe
        $this->db->where('permissao', 'vNcm');
        $exists_ncm = $this->db->get('permissoes')->num_rows();

        if ($exists_ncm == 0) {
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
        $this->db->where('permissao', 'vConfiguracao');
        $this->db->delete('permissoes');

        $this->db->where('permissao', 'vNcm');
        $this->db->delete('permissoes');
    }
} 
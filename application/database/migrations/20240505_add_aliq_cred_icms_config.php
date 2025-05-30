<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_aliq_cred_icms_config extends CI_Migration
{
    public function up()
    {
        // Add the new configuration
        $data = [
            'config' => 'aliq_cred_icms',
            'valor' => '0,00'
        ];
        
        $this->db->insert('configuracoes', $data);
    }

    public function down()
    {
        $this->db->where('config', 'aliq_cred_icms');
        $this->db->delete('configuracoes');
    }
} 
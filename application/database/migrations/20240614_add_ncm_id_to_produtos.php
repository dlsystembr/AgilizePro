<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_ncm_id_to_produtos extends CI_Migration {

    public function up() {
        // Verifica se a coluna já existe
        if (!$this->db->field_exists('ncm_id', 'produtos')) {
            $fields = array(
                'ncm_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE,
                    'after' => 'NCMs'
                )
            );
            $this->dbforge->add_column('produtos', $fields);
        }
    }

    public function down() {
        // Verifica se a coluna existe antes de tentar removê-la
        if ($this->db->field_exists('ncm_id', 'produtos')) {
            $this->dbforge->drop_column('produtos', 'ncm_id');
        }
    }
} 
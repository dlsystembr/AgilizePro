<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_valor_total_to_nfe_emitidas extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('nfe_emitidas', [
            'valor_total' => [
                'type' => 'DECIMAL(10,2)',
                'null' => TRUE,
                'default' => 0.00
            ]
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('nfe_emitidas', 'valor_total');
    }
} 
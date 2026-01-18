<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_classificacao_fiscal_permissions extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('permissoes', [
            'aClassificacaoFiscal' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'eClassificacaoFiscal' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'dClassificacaoFiscal' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'vClassificacaoFiscal' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('permissoes', 'aClassificacaoFiscal');
        $this->dbforge->drop_column('permissoes', 'eClassificacaoFiscal');
        $this->dbforge->drop_column('permissoes', 'dClassificacaoFiscal');
        $this->dbforge->drop_column('permissoes', 'vClassificacaoFiscal');
    }
} 
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cria a tabela grupos_empresariais (Grupo Empresarial).
 * Campos: id, nome, situação (ativo/inativo), data_cadastro, data_atualizacao.
 */
class Migration_Create_grupos_empresariais_table extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists('grupos_empresariais')) {
            $this->dbforge->add_field(array(
                'gre_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'gre_nome' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => FALSE,
                ),
                'gre_situacao' => array(
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 1,
                    'comment' => '1=ativo, 0=inativo',
                ),
                'gre_data_cadastro' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ),
                'gre_data_atualizacao' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ),
            ));
            $this->dbforge->add_key('gre_id', TRUE);
            $this->dbforge->create_table('grupos_empresariais', TRUE);
        } elseif ($this->db->table_exists('grupos_empresariais') && !$this->db->field_exists('gre_situacao', 'grupos_empresariais')) {
            $this->dbforge->add_column('grupos_empresariais', array(
                'gre_situacao' => array(
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 1,
                ),
            ));
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('grupos_empresariais', TRUE);
    }
}

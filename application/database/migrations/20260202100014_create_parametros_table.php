<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cria a tabela parametros (parâmetros do sistema por empresa).
 * Valor em prm_valor (TEXT); prm_tipo_dado define conversão na aplicação.
 * Prefixo de colunas: prm_ (3 letras). FK emp_id -> empresas.
 */
class Migration_Create_parametros_table extends CI_Migration {

    public function up()
    {
        if ($this->db->table_exists('parametros')) {
            return;
        }

        $this->dbforge->add_field([
            'prm_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
                'null' => FALSE,
            ],
            'emp_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
                'null' => FALSE,
                'comment' => 'FK empresas (tipo igual a empresas.emp_id)',
            ],
            'prm_nome' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => FALSE,
                'comment' => 'Código único do parâmetro (ex: app_name, per_page)',
            ],
            'prm_caption' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => TRUE,
                'comment' => 'Rótulo para a tela de configuração',
            ],
            'prm_tipo_dado' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => FALSE,
                'default' => 'string',
                'comment' => 'string|integer|float|boolean|datetime|text|json',
            ],
            'prm_descricao' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Descrição/ajuda',
            ],
            'prm_valor' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Valor em texto; conversão conforme prm_tipo_dado na aplicação',
            ],
            'prm_dado_formatado' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Valor formatado para exibição (opcional)',
            ],
            'prm_visivel' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 1,
                'comment' => '1=exibir na tela de parâmetros',
            ],
            'prm_grupo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE,
                'comment' => 'Agrupador: geral, os, fiscal, notificacoes, nfe, etc.',
            ],
            'prm_ordem' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'default' => 0,
                'comment' => 'Ordem de exibição no grupo',
            ],
            'prm_data_atualizacao' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'comment' => 'Data de alteração',
            ],
        ]);
        $this->dbforge->add_key('prm_id', TRUE);
        $this->dbforge->add_key('emp_id', FALSE);
        $this->dbforge->add_key('prm_grupo', FALSE);
        $this->dbforge->create_table('parametros', TRUE);

        $this->db->query('ALTER TABLE `parametros` ADD UNIQUE KEY `uk_parametros_empresa_nome` (`emp_id`, `prm_nome`)');

        if ($this->db->table_exists('empresas')) {
            try {
                $this->db->query('ALTER TABLE `parametros` ADD CONSTRAINT `fk_parametros_empresa` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            } catch (Exception $e) {
                log_message('error', 'Migration parametros: ' . $e->getMessage());
            }
        }

        $this->db->query("ALTER TABLE `parametros` ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Parâmetros do sistema por empresa (substitui configuracoes)'");
    }

    public function down()
    {
        if (!$this->db->table_exists('parametros')) {
            return;
        }
        try {
            $this->db->query('ALTER TABLE `parametros` DROP FOREIGN KEY `fk_parametros_empresa`');
        } catch (Exception $e) { /* ignora */ }
        $this->dbforge->drop_table('parametros', TRUE);
    }
}

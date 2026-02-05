<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Remove a tabela permissoes e a coluna permissoes_id da tabela usuarios.
 * O sistema passou a usar grupo_usuario + grupo_usuario_empresa + grupo_usuario_permissoes.
 */
class Migration_Drop_permissoes_table extends CI_Migration {

    public function up()
    {
        if ($this->db->table_exists('usuarios')) {
            // Remover FK que aponta para permissoes (nomes comuns)
            $fks = ['fk_usuarios_permissoes1', 'fk_usuarios_permissoes', 'permissoes_id'];
            foreach ($fks as $fk) {
                try {
                    $this->db->query("ALTER TABLE `usuarios` DROP FOREIGN KEY `{$fk}`");
                } catch (Exception $e) {
                    // ignora se não existir
                }
            }
            if ($this->db->field_exists('permissoes_id', 'usuarios')) {
                $this->dbforge->drop_column('usuarios', 'permissoes_id');
            }
        }

        if ($this->db->table_exists('permissoes')) {
            $this->dbforge->drop_table('permissoes', TRUE);
        }
    }

    public function down()
    {
        // Recriar tabela permissoes e coluna não implementado (estrutura antiga variável).
        log_message('error', 'Migration_Drop_permissoes_table: down() não implementado.');
    }
}

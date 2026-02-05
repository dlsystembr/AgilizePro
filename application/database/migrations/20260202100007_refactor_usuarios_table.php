<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Refatora a tabela usuarios para o padrão (prefixo usu_, gre_id, pes_id).
 * Remove campos que passam a ficar em Pessoas (rg, cpf, endereço, telefone, etc.).
 * Mantém: id -> usu_id, nome -> usu_nome, email -> usu_email, senha -> usu_senha,
 * situação -> usu_situacao, datas, url_imagem, permissoes_id, data_expiracao.
 * Adiciona: gre_id, pes_id. Substitui ten_id por gre_id.
 *
 * ATENÇÃO: Após rodar esta migration, é necessário atualizar o código da aplicação
 * para usar usu_id, usu_nome, usu_email, usu_senha, usu_situacao, usu_data_cadastro,
 * usu_data_atualizacao, usu_url_imagem, usu_data_expiracao, gre_id, pes_id.
 * E nas tabelas filhas a coluna passa a ser usu_id (FK para usuarios.usu_id).
 */
class Migration_Refactor_usuarios_table extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists('usuarios')) {
            return;
        }

        // 1) Adicionar novas colunas (prefixo usu_ e gre_id, pes_id)
        $new_cols = [
            'usu_nome'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'usu_email'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'usu_senha'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'usu_situacao'          => ['type' => 'TINYINT', 'constraint' => 1, 'null' => TRUE],
            'usu_data_cadastro'     => ['type' => 'DATETIME', 'null' => TRUE],
            'usu_data_atualizacao'  => ['type' => 'DATETIME', 'null' => TRUE],
            'usu_url_imagem'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'usu_data_expiracao'    => ['type' => 'DATE', 'null' => TRUE],
            'gre_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'pes_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
        ];
        foreach ($new_cols as $col => $def) {
            if (!$this->db->field_exists($col, 'usuarios')) {
                $this->dbforge->add_column('usuarios', [$col => $def]);
            }
        }

        // 2) Copiar dados das colunas antigas para as novas (suporta dataCadastro ou datacadastro)
        $cadastro_col = $this->db->field_exists('datacadastro', 'usuarios') ? 'datacadastro' : 'dataCadastro';
        $this->db->query("UPDATE usuarios SET
            usu_nome = COALESCE(nome, ''),
            usu_email = COALESCE(email, ''),
            usu_senha = COALESCE(senha, ''),
            usu_situacao = COALESCE(situacao, 1),
            usu_data_cadastro = COALESCE(`{$cadastro_col}`, NOW())
        ");
        if ($this->db->field_exists('url_image_user', 'usuarios')) {
            $this->db->query("UPDATE usuarios SET usu_url_imagem = url_image_user");
        }
        if ($this->db->field_exists('dataExpiracao', 'usuarios')) {
            $this->db->query("UPDATE usuarios SET usu_data_expiracao = dataExpiracao");
        }
        if ($this->db->field_exists('ten_id', 'usuarios')) {
            $this->db->query("UPDATE usuarios SET gre_id = ten_id");
        }

        // 3) Remover FKs que apontam para usuarios.idUsuarios
        $drops = [
            ['garantias', 'fk_garantias_usuarios1'],
            ['os', 'fk_os_usuarios1'],
            ['vendas', 'fk_vendas_usuarios1'],
            ['lancamentos', 'fk_lancamentos_usuarios1'],
        ];
        if ($this->db->table_exists('pedidos') || $this->db->table_exists('PEDIDOS')) {
            $drops[] = [$this->db->table_exists('PEDIDOS') ? 'PEDIDOS' : 'pedidos', 'fk_pedidos_usuarios'];
        }
        foreach ($drops as $d) {
            if ($this->db->table_exists($d[0])) {
                try {
                    $this->db->query("ALTER TABLE `{$d[0]}` DROP FOREIGN KEY `{$d[1]}`");
                } catch (Exception $e) { /* ignora */ }
            }
        }
        if ($this->db->table_exists('ordem_servico')) {
            try {
                $this->db->query("ALTER TABLE `ordem_servico` DROP FOREIGN KEY `fk_ordem_servico_usuarios`");
            } catch (Exception $e) { /* ignora */ }
        }

        // 4) Renomear idUsuarios -> usu_id em usuarios
        if ($this->db->field_exists('idUsuarios', 'usuarios') && !$this->db->field_exists('usu_id', 'usuarios')) {
            $this->db->query("ALTER TABLE `usuarios` CHANGE `idUsuarios` `usu_id` INT(11) NOT NULL AUTO_INCREMENT");
        }

        // 5) Nas tabelas filhas: renomear coluna de usuário para usu_id (regra FK = nome da coluna pai)
        $renames = [
            ['garantias', 'usuarios_id', 'usu_id'],
            ['vendas', 'usuarios_id', 'usu_id'],
            ['lancamentos', 'usuarios_id', 'usu_id'],
        ];
        if ($this->db->table_exists('os') && $this->db->field_exists('usuarios_id', 'os')) {
            $renames[] = ['os', 'usuarios_id', 'usu_id'];
        }
        if ($this->db->table_exists('ordem_servico') && $this->db->field_exists('orv_usuarios_id', 'ordem_servico')) {
            $renames[] = ['ordem_servico', 'orv_usuarios_id', 'usu_id'];
        }
        foreach ($renames as $r) {
            if ($this->db->table_exists($r[0]) && $this->db->field_exists($r[1], $r[0]) && !$this->db->field_exists($r[2], $r[0])) {
                $this->db->query("ALTER TABLE `{$r[0]}` CHANGE `{$r[1]}` `{$r[2]}` INT(11) NULL");
            }
        }
        if ($this->db->table_exists('PEDIDOS') && $this->db->field_exists('usu_id', 'PEDIDOS')) {
            // PEDIDOS já tem usu_id; só garantir FK
        }

        // 6) Recriar FKs (usu_id -> usuarios.usu_id)
        $fk_defs = [
            ['garantias', 'usu_id', 'fk_garantias_usuarios1'],
            ['vendas', 'usu_id', 'fk_vendas_usuarios1'],
            ['lancamentos', 'usu_id', 'fk_lancamentos_usuarios1'],
        ];
        if ($this->db->table_exists('os') && $this->db->field_exists('usu_id', 'os')) {
            $fk_defs[] = ['os', 'usu_id', 'fk_os_usuarios1'];
        }
        if ($this->db->table_exists('ordem_servico') && $this->db->field_exists('usu_id', 'ordem_servico')) {
            $fk_defs[] = ['ordem_servico', 'usu_id', 'fk_ordem_servico_usuarios'];
        }
        foreach ($fk_defs as $fk) {
            if (!$this->db->table_exists($fk[0])) continue;
            try {
                $this->db->query("ALTER TABLE `{$fk[0]}` ADD CONSTRAINT `{$fk[2]}` FOREIGN KEY (`{$fk[1]}`) REFERENCES `usuarios` (`usu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION");
            } catch (Exception $e) { /* ignora se já existir */ }
        }

        // 7) FK gre_id -> grupos_empresariais
        if ($this->db->table_exists('grupos_empresariais') && $this->db->field_exists('gre_id', 'usuarios')) {
            try {
                $this->db->query("ALTER TABLE `usuarios` ADD CONSTRAINT `fk_usuarios_grupo` FOREIGN KEY (`gre_id`) REFERENCES `grupos_empresariais` (`gre_id`) ON DELETE SET NULL ON UPDATE NO ACTION");
            } catch (Exception $e) { /* ignora */ }
        }

        // 8) FK pes_id -> pessoas
        if ($this->db->table_exists('pessoas') && $this->db->field_exists('pes_id', 'usuarios')) {
            try {
                $this->db->query("ALTER TABLE `usuarios` ADD CONSTRAINT `fk_usuarios_pessoa` FOREIGN KEY (`pes_id`) REFERENCES `pessoas` (`pes_id`) ON DELETE SET NULL ON UPDATE NO ACTION");
            } catch (Exception $e) { /* ignora */ }
        }

        // 9) Remover colunas antigas de usuarios — permissoes_id é MANTIDO (processo atual; remover depois)
        $drop_cols = ['nome', 'email', 'senha', 'situacao', 'rg', 'cpf', 'rua', 'numero', 'bairro', 'cidade', 'estado', 'telefone', 'celular', 'cep', 'url_image_user', 'dataExpiracao', 'ten_id'];
        if ($this->db->field_exists('dataCadastro', 'usuarios')) $drop_cols[] = 'dataCadastro';
        if ($this->db->field_exists('datacadastro', 'usuarios')) $drop_cols[] = 'datacadastro';
        foreach ($drop_cols as $c) {
            if ($this->db->field_exists($c, 'usuarios')) {
                $this->dbforge->drop_column('usuarios', $c);
            }
        }

        // 10) Tornar novas colunas NOT NULL onde necessário (após preencher)
        $this->db->query("UPDATE usuarios SET usu_nome = 'Sem nome' WHERE usu_nome IS NULL OR usu_nome = ''");
        $this->db->query("UPDATE usuarios SET usu_email = 'sem@email' WHERE usu_email IS NULL OR usu_email = ''");
        $this->db->query("UPDATE usuarios SET usu_senha = '' WHERE usu_senha IS NULL");
        $this->db->query("ALTER TABLE `usuarios` MODIFY `usu_nome` VARCHAR(255) NOT NULL");
        $this->db->query("ALTER TABLE `usuarios` MODIFY `usu_email` VARCHAR(100) NOT NULL");
        $this->db->query("ALTER TABLE `usuarios` MODIFY `usu_senha` VARCHAR(255) NOT NULL");
        $this->db->query("ALTER TABLE `usuarios` MODIFY `usu_situacao` TINYINT(1) NOT NULL DEFAULT 1");

        // Índice único no email (login)
        try {
            $this->db->query("ALTER TABLE `usuarios` ADD UNIQUE KEY `uk_usuarios_email` (`usu_email`)");
        } catch (Exception $e) { /* ignora se já existir */ }
    }

    public function down()
    {
        // Reverter exige recriar colunas antigas e copiar de volta; não implementado por ser destrutivo.
        log_message('error', 'Migration_Refactor_usuarios_table: down() não implementado.');
    }
}

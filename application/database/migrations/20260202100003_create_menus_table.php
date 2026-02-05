<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cria a tabela menus: lista de todos os menus do sistema (sidebar).
 * Permite configurar por empresa quais menus estão disponíveis.
 */
class Migration_Create_menus_table extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists('menus')) {
            $this->dbforge->add_field(array(
                'men_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'men_identificador' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => FALSE,
                    'comment' => 'Ex: pessoas, produtos, os',
                ),
                'men_nome' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '120',
                    'null' => FALSE,
                ),
                'men_url' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => TRUE,
                ),
                'men_icone' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => TRUE,
                    'comment' => 'Ex: bx-group, bx-basket',
                ),
                'men_ordem' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE,
                    'default' => 0,
                ),
                'men_permissao' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => TRUE,
                    'comment' => 'Chave da permissão que controla o menu, ex: vPessoa',
                ),
                'men_situacao' => array(
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 1,
                    'comment' => '1=ativo, 0=inativo',
                ),
                'men_data_cadastro' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ),
                'men_data_atualizacao' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ),
            ));
            $this->dbforge->add_key('men_id', TRUE);
            $this->dbforge->add_key('men_identificador');
            $this->dbforge->create_table('menus', TRUE);

            $agora = date('Y-m-d H:i:s');
            $itens = array(
                array('men_identificador' => 'pessoas', 'men_nome' => 'Pessoas', 'men_url' => 'pessoas', 'men_icone' => 'bx-group', 'men_ordem' => 10, 'men_permissao' => 'vPessoa', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'tipos_clientes', 'men_nome' => 'Tipos de Clientes', 'men_url' => 'tipos_clientes', 'men_icone' => 'bx-user-pin', 'men_ordem' => 15, 'men_permissao' => 'vTipoCliente', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'contratos', 'men_nome' => 'Contratos', 'men_url' => 'contratos', 'men_icone' => 'bx-file-blank', 'men_ordem' => 20, 'men_permissao' => 'vContrato', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'produtos', 'men_nome' => 'Produtos / Serviços', 'men_url' => 'produtos', 'men_icone' => 'bx-basket', 'men_ordem' => 30, 'men_permissao' => 'vProduto', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'vendas', 'men_nome' => 'Vendas', 'men_url' => 'vendas', 'men_icone' => 'bx-cart-alt', 'men_ordem' => 40, 'men_permissao' => 'vVenda', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'os', 'men_nome' => 'Ordens de Serviço', 'men_url' => 'os', 'men_icone' => 'bx-file', 'men_ordem' => 50, 'men_permissao' => 'vOs', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'faturamento_entrada', 'men_nome' => 'Faturamento Entrada', 'men_url' => 'faturamentoEntrada', 'men_icone' => 'bx-receipt', 'men_ordem' => 55, 'men_permissao' => 'vFaturamentoEntrada', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'nfe', 'men_nome' => 'Emissor de Notas (NFE)', 'men_url' => 'nfe', 'men_icone' => 'bx-file-blank', 'men_ordem' => 60, 'men_permissao' => 'vNfe', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'nfecom', 'men_nome' => 'NFCOM', 'men_url' => 'nfecom', 'men_icone' => 'bx-notepad', 'men_ordem' => 65, 'men_permissao' => 'vNfecom', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'lancamentos', 'men_nome' => 'Lançamentos', 'men_url' => 'financeiro/lancamentos', 'men_icone' => 'bx-bar-chart-alt-2', 'men_ordem' => 70, 'men_permissao' => 'vLancamento', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'garantias', 'men_nome' => 'Garantias', 'men_url' => 'garantias', 'men_icone' => 'bx-certification', 'men_ordem' => 75, 'men_permissao' => 'vGarantia', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'usuarios', 'men_nome' => 'Usuários', 'men_url' => 'usuarios', 'men_icone' => 'bx-user', 'men_ordem' => 80, 'men_permissao' => 'vUsuario', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'permissoes', 'men_nome' => 'Permissões', 'men_url' => 'permissoes', 'men_icone' => 'bx-shield', 'men_ordem' => 85, 'men_permissao' => 'vPermissao', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'configuracoes', 'men_nome' => 'Configurações', 'men_url' => 'mapos/configuracoes', 'men_icone' => 'bx-cog', 'men_ordem' => 90, 'men_permissao' => 'vConfiguracao', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'auditoria', 'men_nome' => 'Auditoria', 'men_url' => 'auditoria', 'men_icone' => 'bx-history', 'men_ordem' => 95, 'men_permissao' => 'vAuditoria', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'arquivos', 'men_nome' => 'Arquivos', 'men_url' => 'arquivos', 'men_icone' => 'bx-folder', 'men_ordem' => 100, 'men_permissao' => 'vArquivo', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'backup', 'men_nome' => 'Backup', 'men_url' => 'mapos/backup', 'men_icone' => 'bx-data', 'men_ordem' => 105, 'men_permissao' => 'vBackup', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
                array('men_identificador' => 'emitente', 'men_nome' => 'Emitente', 'men_url' => 'mapos/emitente', 'men_icone' => 'bx-building', 'men_ordem' => 110, 'men_permissao' => 'vEmitente', 'men_situacao' => 1, 'men_data_cadastro' => $agora, 'men_data_atualizacao' => $agora),
            );
            $this->db->insert_batch('menus', $itens);
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('menus', TRUE);
    }
}

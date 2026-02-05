<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Insere parâmetros iniciais para cada empresa existente.
 * Valores padrão compatíveis com configuracoes atuais.
 */
class Migration_Seed_parametros_iniciais extends CI_Migration {

    private $defaults = [
        ['prm_nome' => 'app_name', 'prm_tipo_dado' => 'string', 'prm_caption' => 'Nome do sistema', 'prm_grupo' => 'geral', 'prm_ordem' => 10, 'prm_valor' => 'MapOS'],
        ['prm_nome' => 'app_theme', 'prm_tipo_dado' => 'string', 'prm_caption' => 'Tema', 'prm_grupo' => 'geral', 'prm_ordem' => 20, 'prm_valor' => 'default'],
        ['prm_nome' => 'per_page', 'prm_tipo_dado' => 'integer', 'prm_caption' => 'Itens por página', 'prm_grupo' => 'geral', 'prm_ordem' => 30, 'prm_valor' => '10'],
        ['prm_nome' => 'control_datatable', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'Usar DataTables', 'prm_grupo' => 'geral', 'prm_ordem' => 40, 'prm_valor' => '1'],
        ['prm_nome' => 'control_estoque', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'Controle de estoque', 'prm_grupo' => 'geral', 'prm_ordem' => 50, 'prm_valor' => '1'],
        ['prm_nome' => 'control_baixa', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'Baixa automática', 'prm_grupo' => 'geral', 'prm_ordem' => 60, 'prm_valor' => '1'],
        ['prm_nome' => 'control_editos', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'Editar OS', 'prm_grupo' => 'os', 'prm_ordem' => 10, 'prm_valor' => '1'],
        ['prm_nome' => 'control_edit_vendas', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'Editar vendas', 'prm_grupo' => 'geral', 'prm_ordem' => 70, 'prm_valor' => '1'],
        ['prm_nome' => 'control_2vias', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'Impressão 2 vias OS', 'prm_grupo' => 'os', 'prm_ordem' => 20, 'prm_valor' => '0'],
        ['prm_nome' => 'os_notification', 'prm_tipo_dado' => 'string', 'prm_caption' => 'Notificação OS', 'prm_grupo' => 'notificacoes', 'prm_ordem' => 10, 'prm_valor' => 'cliente'],
        ['prm_nome' => 'email_automatico', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'E-mail automático', 'prm_grupo' => 'notificacoes', 'prm_ordem' => 20, 'prm_valor' => '1'],
        ['prm_nome' => 'notifica_whats', 'prm_tipo_dado' => 'text', 'prm_caption' => 'Mensagem WhatsApp', 'prm_grupo' => 'notificacoes', 'prm_ordem' => 30, 'prm_valor' => ''],
        ['prm_nome' => 'os_status_list', 'prm_tipo_dado' => 'json', 'prm_caption' => 'Lista status OS', 'prm_grupo' => 'os', 'prm_ordem' => 30, 'prm_valor' => '["Aberto","Faturado","Negociação","Em Andamento","Orçamento","Finalizado","Cancelado","Aguardando Peças"]'],
        ['prm_nome' => 'pix_key', 'prm_tipo_dado' => 'string', 'prm_caption' => 'Chave PIX', 'prm_grupo' => 'geral', 'prm_ordem' => 80, 'prm_valor' => ''],
        ['prm_nome' => 'regime_tributario', 'prm_tipo_dado' => 'string', 'prm_caption' => 'Regime tributário', 'prm_grupo' => 'fiscal', 'prm_ordem' => 10, 'prm_valor' => ''],
        ['prm_nome' => 'mensagem_simples_nacional', 'prm_tipo_dado' => 'text', 'prm_caption' => 'Mensagem Simples Nacional', 'prm_grupo' => 'fiscal', 'prm_ordem' => 20, 'prm_valor' => ''],
        ['prm_nome' => 'aliq_cred_icms', 'prm_tipo_dado' => 'string', 'prm_caption' => 'Alíq. crédito ICMS', 'prm_grupo' => 'fiscal', 'prm_ordem' => 30, 'prm_valor' => ''],
        ['prm_nome' => 'tributacao_produto', 'prm_tipo_dado' => 'boolean', 'prm_caption' => 'Tributação por produto', 'prm_grupo' => 'fiscal', 'prm_ordem' => 40, 'prm_valor' => '0'],
    ];

    public function up()
    {
        if (!$this->db->table_exists('parametros') || !$this->db->table_exists('empresas')) {
            return;
        }
        $empresas = $this->db->select('emp_id')->from('empresas')->get()->result();
        if (empty($empresas)) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        foreach ($empresas as $emp) {
            $emp_id = (int) $emp->emp_id;
            foreach ($this->defaults as $row) {
                $existe = $this->db->get_where('parametros', [
                    'emp_id' => $emp_id,
                    'prm_nome' => $row['prm_nome'],
                ])->row();
                if (!$existe) {
                    $this->db->insert('parametros', [
                        'emp_id' => $emp_id,
                        'prm_nome' => $row['prm_nome'],
                        'prm_caption' => $row['prm_caption'],
                        'prm_tipo_dado' => $row['prm_tipo_dado'],
                        'prm_descricao' => null,
                        'prm_valor' => $row['prm_valor'],
                        'prm_visivel' => 1,
                        'prm_grupo' => $row['prm_grupo'],
                        'prm_ordem' => $row['prm_ordem'],
                        'prm_data_atualizacao' => $now,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        // Não remove dados para evitar perda; down da tabela já remove tudo
    }
}

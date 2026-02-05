<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Insere menus adicionais: Relatórios, Tributação e itens do dropdown Configurações do topo.
 * Executa apenas inserts para identificadores que ainda não existem.
 */
class Migration_Insert_new_menus_relatorios_tributacao_config extends CI_Migration {

    public function up()
    {
        if (!$this->db->table_exists('menus')) {
            return;
        }
        $agora = date('Y-m-d H:i:s');
        $novos = [
            ['relatorio_clientes', 'Relatório Clientes', 'relatorios/clientes', 'bx-pie-chart-alt-2', 120, 'rCliente'],
            ['relatorio_produtos', 'Relatório Produtos', 'relatorios/produtos', 'bx-pie-chart-alt-2', 121, 'rProduto'],
            ['relatorio_servicos', 'Relatório Serviços', 'relatorios/servicos', 'bx-pie-chart-alt-2', 122, 'rServico'],
            ['relatorio_os', 'Relatório OS', 'relatorios/os', 'bx-pie-chart-alt-2', 123, 'rOs'],
            ['relatorio_vendas', 'Relatório Vendas', 'relatorios/vendas', 'bx-pie-chart-alt-2', 124, 'rVenda'],
            ['relatorio_contratos', 'Relatório Contratos', 'relatorios/contratos', 'bx-pie-chart-alt-2', 125, 'rContrato'],
            ['relatorio_financeiro', 'Relatório Financeiro', 'relatorios/financeiro', 'bx-pie-chart-alt-2', 126, 'rFinanceiro'],
            ['relatorio_sku', 'Relatório SKU', 'relatorios/sku', 'bx-pie-chart-alt-2', 127, 'rVenda'],
            ['relatorio_receitas_mei', 'Relatório Receitas MEI', 'relatorios/receitasBrutasMei', 'bx-pie-chart-alt-2', 128, 'rFinanceiro'],
            ['relatorio_nfe_emitidas', 'Relatório NFe emitidas', 'relatorios/nfe_emitidas', 'bx-pie-chart-alt-2', 129, 'rNfe'],
            ['simulador_tributacao', 'Simulador de Tributação', 'simuladortributacao', 'bx-calculator', 130, 'vCliente'],
            ['tributacao_produto', 'Tributação Produto', 'tributacaoproduto', 'bx-calculator', 131, 'vTributacaoProduto'],
            ['classificacao_fiscal', 'Classificação Fiscal', 'classificacaofiscal', 'bx-calculator', 132, 'vClassificacaoFiscal'],
            ['operacao_comercial', 'Operação Comercial', 'operacaocomercial', 'bx-calculator', 133, 'vOperacaoComercial'],
            ['aliquotas', 'Alíquotas', 'aliquotas', 'bx-calculator', 134, 'vAliquota'],
            ['ncms', 'NCMs', 'ncms', 'bx-calculator', 135, 'vNcm'],
            ['sistema', 'Sistema', 'mapos/configurar', 'bx-cog', 140, 'vConfiguracao'],
            ['empresas', 'Empresas', 'empresas', 'bx-building', 141, 'vEmpresa'],
            ['certificados', 'Certificados', 'certificados', 'bx-certification', 142, 'vCertificado'],
            ['configuracoes_fiscais', 'Configurações Fiscais', 'configuracoesfiscais', 'bx-cog', 143, 'vConfigFiscal'],
            ['emails', 'Emails', 'mapos/emails', 'bx-envelope', 144, null],
        ];
        foreach ($novos as $item) {
            $existe = $this->db->limit(1)->get_where('menus', ['men_identificador' => $item[0]])->row();
            if (!$existe) {
                $this->db->insert('menus', [
                    'men_identificador' => $item[0],
                    'men_nome' => $item[1],
                    'men_url' => $item[2],
                    'men_icone' => $item[3],
                    'men_ordem' => $item[4],
                    'men_permissao' => $item[5],
                    'men_situacao' => 1,
                    'men_data_cadastro' => $agora,
                    'men_data_atualizacao' => $agora,
                ]);
            }
        }
    }

    public function down()
    {
        if (!$this->db->table_exists('menus')) {
            return;
        }
        $identificadores = [
            'relatorio_clientes', 'relatorio_produtos', 'relatorio_servicos', 'relatorio_os', 'relatorio_vendas',
            'relatorio_contratos', 'relatorio_financeiro', 'relatorio_sku', 'relatorio_receitas_mei', 'relatorio_nfe_emitidas',
            'simulador_tributacao', 'tributacao_produto', 'classificacao_fiscal', 'operacao_comercial', 'aliquotas', 'ncms',
            'sistema', 'empresas', 'certificados', 'configuracoes_fiscais', 'emails',
        ];
        $this->db->where_in('men_identificador', $identificadores);
        $this->db->delete('menus');
    }
}

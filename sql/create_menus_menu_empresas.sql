-- Tabela menus: lista de todos os menus do sistema (sidebar).
-- Permite configurar por empresa quais menus estão disponíveis.
-- men_situacao: 1 = ativo, 0 = inativo

CREATE TABLE IF NOT EXISTS `menus` (
  `men_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `men_identificador` varchar(64) NOT NULL COMMENT 'Ex: pessoas, produtos, os',
  `men_nome` varchar(120) NOT NULL,
  `men_url` varchar(255) DEFAULT NULL,
  `men_icone` varchar(64) DEFAULT NULL COMMENT 'Ex: bx-group, bx-basket',
  `men_ordem` int(11) NOT NULL DEFAULT 0,
  `men_permissao` varchar(64) DEFAULT NULL COMMENT 'Chave da permissão, ex: vPessoa',
  `men_situacao` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo',
  `men_data_cadastro` datetime DEFAULT NULL,
  `men_data_atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`men_id`),
  KEY `men_identificador` (`men_identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir menus do sistema (execute apenas se a tabela estiver vazia)
INSERT INTO `menus` (`men_identificador`, `men_nome`, `men_url`, `men_icone`, `men_ordem`, `men_permissao`, `men_situacao`, `men_data_cadastro`, `men_data_atualizacao`) VALUES
('pessoas', 'Pessoas', 'pessoas', 'bx-group', 10, 'vPessoa', 1, NOW(), NOW()),
('tipos_clientes', 'Tipos de Clientes', 'tipos_clientes', 'bx-user-pin', 15, 'vTipoCliente', 1, NOW(), NOW()),
('contratos', 'Contratos', 'contratos', 'bx-file-blank', 20, 'vContrato', 1, NOW(), NOW()),
('produtos', 'Produtos / Serviços', 'produtos', 'bx-basket', 30, 'vProduto', 1, NOW(), NOW()),
('vendas', 'Vendas', 'vendas', 'bx-cart-alt', 40, 'vVenda', 1, NOW(), NOW()),
('os', 'Ordens de Serviço', 'os', 'bx-file', 50, 'vOs', 1, NOW(), NOW()),
('faturamento_entrada', 'Faturamento Entrada', 'faturamentoEntrada', 'bx-receipt', 55, 'vFaturamentoEntrada', 1, NOW(), NOW()),
('nfe', 'Emissor de Notas (NFE)', 'nfe', 'bx-file-blank', 60, 'vNfe', 1, NOW(), NOW()),
('nfecom', 'NFCOM', 'nfecom', 'bx-notepad', 65, 'vNfecom', 1, NOW(), NOW()),
('lancamentos', 'Lançamentos', 'financeiro/lancamentos', 'bx-bar-chart-alt-2', 70, 'vLancamento', 1, NOW(), NOW()),
('garantias', 'Garantias', 'garantias', 'bx-certification', 75, 'vGarantia', 1, NOW(), NOW()),
('grupo_usuario', 'Grupos de Usuário', 'gruposUsuario', 'bx-group', 82, 'vUsuario', 1, NOW(), NOW()),
('configuracoes', 'Configurações', 'mapos/configuracoes', 'bx-cog', 90, 'vConfiguracao', 1, NOW(), NOW()),
('auditoria', 'Auditoria', 'auditoria', 'bx-history', 95, 'vAuditoria', 1, NOW(), NOW()),
('arquivos', 'Arquivos', 'arquivos', 'bx-folder', 100, 'vArquivo', 1, NOW(), NOW()),
('backup', 'Backup', 'mapos/backup', 'bx-data', 105, 'vBackup', 1, NOW(), NOW()),
('emitente', 'Emitente', 'mapos/emitente', 'bx-building', 110, 'vEmitente', 1, NOW(), NOW()),
('relatorio_clientes', 'Relatório Clientes', 'relatorios/clientes', 'bx-pie-chart-alt-2', 120, 'rCliente', 1, NOW(), NOW()),
('relatorio_produtos', 'Relatório Produtos', 'relatorios/produtos', 'bx-pie-chart-alt-2', 121, 'rProduto', 1, NOW(), NOW()),
('relatorio_servicos', 'Relatório Serviços', 'relatorios/servicos', 'bx-pie-chart-alt-2', 122, 'rServico', 1, NOW(), NOW()),
('relatorio_os', 'Relatório OS', 'relatorios/os', 'bx-pie-chart-alt-2', 123, 'rOs', 1, NOW(), NOW()),
('relatorio_vendas', 'Relatório Vendas', 'relatorios/vendas', 'bx-pie-chart-alt-2', 124, 'rVenda', 1, NOW(), NOW()),
('relatorio_contratos', 'Relatório Contratos', 'relatorios/contratos', 'bx-pie-chart-alt-2', 125, 'rContrato', 1, NOW(), NOW()),
('relatorio_financeiro', 'Relatório Financeiro', 'relatorios/financeiro', 'bx-pie-chart-alt-2', 126, 'rFinanceiro', 1, NOW(), NOW()),
('relatorio_sku', 'Relatório SKU', 'relatorios/sku', 'bx-pie-chart-alt-2', 127, 'rVenda', 1, NOW(), NOW()),
('relatorio_receitas_mei', 'Relatório Receitas MEI', 'relatorios/receitasBrutasMei', 'bx-pie-chart-alt-2', 128, 'rFinanceiro', 1, NOW(), NOW()),
('relatorio_nfe_emitidas', 'Relatório NFe emitidas', 'relatorios/nfe_emitidas', 'bx-pie-chart-alt-2', 129, 'rNfe', 1, NOW(), NOW()),
-- Tributação (topo)
('simulador_tributacao', 'Simulador de Tributação', 'simuladortributacao', 'bx-calculator', 130, 'vCliente', 1, NOW(), NOW()),
('tributacao_produto', 'Tributação Produto', 'tributacaoproduto', 'bx-calculator', 131, 'vTributacaoProduto', 1, NOW(), NOW()),
('classificacao_fiscal', 'Classificação Fiscal', 'classificacaofiscal', 'bx-calculator', 132, 'vClassificacaoFiscal', 1, NOW(), NOW()),
('operacao_comercial', 'Operação Comercial', 'operacaocomercial', 'bx-calculator', 133, 'vOperacaoComercial', 1, NOW(), NOW()),
('aliquotas', 'Alíquotas', 'aliquotas', 'bx-calculator', 134, 'vAliquota', 1, NOW(), NOW()),
('ncms', 'NCMs', 'ncms', 'bx-calculator', 135, 'vNcm', 1, NOW(), NOW()),
('sistema', 'Sistema', 'mapos/configurar', 'bx-cog', 140, 'vConfiguracao', 1, NOW(), NOW()),
('empresas', 'Empresas', 'empresas', 'bx-building', 141, 'vEmpresa', 1, NOW(), NOW()),
('certificados', 'Certificados', 'certificados', 'bx-certification', 142, 'vCertificado', 1, NOW(), NOW()),
('configuracoes_fiscais', 'Configurações Fiscais', 'configuracoesfiscais', 'bx-cog', 143, 'vConfigFiscal', 1, NOW(), NOW()),
('emails', 'Emails', 'mapos/emails', 'bx-envelope', 144, null, 1, NOW(), NOW());

-- Tabela menu_empresa (ligação N:N - SINGULAR; PK mep_id; FKs com nome da coluna pai: emp_id, men_id)
-- mep_id: PK da tabela de ligação (prefixo MEP)
-- emp_id: mesmo tipo de empresas.emp_id (geralmente int(11) sem UNSIGNED no MapOS)
-- men_id: mesmo tipo de menus.men_id (int unsigned)
CREATE TABLE IF NOT EXISTS `menu_empresa` (
  `mep_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `men_id` int(11) unsigned NOT NULL,
  `mep_data_cadastro` datetime DEFAULT NULL,
  PRIMARY KEY (`mep_id`),
  UNIQUE KEY `uk_menu_empresa_emp_men` (`emp_id`, `men_id`),
  KEY `fk_menu_empresa_men` (`men_id`),
  CONSTRAINT `fk_menu_empresa_emp` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_menu_empresa_men` FOREIGN KEY (`men_id`) REFERENCES `menus` (`men_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

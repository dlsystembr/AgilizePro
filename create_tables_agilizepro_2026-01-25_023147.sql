-- Scripts CREATE TABLE para AgilizePro
-- Gerado em: 2026-01-25 02:31:47

CREATE TABLE `itens_pedidos` (
  `ITP_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ITP_SUBTOTAL` decimal(10,2) DEFAULT 0.00,
  `ITP_QUANTIDADE` int(11) DEFAULT NULL,
  `ITP_PRECO` decimal(10,2) DEFAULT 0.00,
  `PDS_ID` int(11) unsigned NOT NULL COMMENT 'FK para PEDIDOS (mantém nome original)',
  `PRO_ID` int(11) NOT NULL COMMENT 'FK para produtos (mantém nome original)',
  `ten_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ITP_ID`),
  KEY `idx_itens_pedidos_pds_id` (`PDS_ID`),
  KEY `idx_itens_pedidos_pro_id` (`PRO_ID`),
  KEY `idx_itens_pedidos_ten_id` (`ten_id`),
  CONSTRAINT `fk_itens_pedidos_pedidos` FOREIGN KEY (`PDS_ID`) REFERENCES `pedidos` (`PDS_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_itens_pedidos_produtos` FOREIGN KEY (`PRO_ID`) REFERENCES `produtos` (`PRO_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_itens_pedidos_ten_id` FOREIGN KEY (`ten_id`) REFERENCES `tenants` (`ten_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `produtos_movimentados` (
  `PDM_ID` int(11) NOT NULL AUTO_INCREMENT,
  `PDM_QTDE` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Quantidade movimentada',
  `PDM_TIPO` varchar(10) NOT NULL COMMENT 'Tipo de movimentação: ENTRADA ou SAIDA',
  `ITF_ID` int(11) NOT NULL COMMENT 'ID do item faturado (FK para itens_faturados)',
  `PDM_DATA` datetime NOT NULL COMMENT 'Data da movimentação',
  PRIMARY KEY (`PDM_ID`),
  KEY `idx_produtos_movimentados_itf` (`ITF_ID`),
  CONSTRAINT `fk_produtos_movimentados_itens_faturados` FOREIGN KEY (`ITF_ID`) REFERENCES `itens_faturados` (`ITF_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tenant_permissoes_menu` (
  `TPM_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TPM_TEN_ID` int(11) unsigned NOT NULL,
  `TPM_MENU_CODIGO` varchar(50) NOT NULL,
  `TPM_PERMISSAO` varchar(50) NOT NULL COMMENT 'Código da permissão do menu (ex: vCliente, aCliente, etc)',
  `TPM_ATIVO` tinyint(1) NOT NULL DEFAULT 1,
  `TPM_DATA_CADASTRO` datetime NOT NULL,
  PRIMARY KEY (`TPM_ID`),
  UNIQUE KEY `uk_tenant_menu_permissao` (`TPM_TEN_ID`,`TPM_MENU_CODIGO`,`TPM_PERMISSAO`),
  CONSTRAINT `fk_tenant_permissoes_menu_tenant` FOREIGN KEY (`TPM_TEN_ID`) REFERENCES `tenants` (`ten_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1874 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
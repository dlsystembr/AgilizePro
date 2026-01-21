-- =====================================================
-- Script SQL para criar tabelas PEDIDOS e ITENS_PEDIDOS
-- Seguindo convenções de nomenclatura do sistema
-- ATUALIZADO: Usa PES_ID ao invés de CLN_ID
-- =====================================================

-- -----------------------------------------------------
-- Tabela PEDIDOS
-- Prefixo: PDS_
-- Tipo: PLURAL (tabela principal)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PEDIDOS` (
  `PDS_ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `PDS_DATA` DATE NULL DEFAULT NULL,
  `PDS_VALOR_TOTAL` DECIMAL(10, 2) NULL DEFAULT 0,
  `PDS_DESCONTO` DECIMAL(10, 2) NULL DEFAULT 0,
  `PDS_VALOR_DESCONTO` DECIMAL(10, 2) NULL DEFAULT 0,
  `PDS_TIPO_DESCONTO` VARCHAR(8) NULL DEFAULT NULL,
  `PDS_FATURADO` TINYINT(1) NULL DEFAULT 0,
  `PDS_OBSERVACOES` TEXT NULL,
  `PDS_OBSERVACOES_CLIENTE` TEXT NULL,
  `PDS_STATUS` VARCHAR(45) NULL DEFAULT NULL,
  `PDS_GARANTIA` INT(11) NULL DEFAULT NULL,
  `PDS_TIPO` ENUM('COMPRA', 'VENDA') NOT NULL DEFAULT 'VENDA',
  `PDS_OPERACAO_COMERCIAL` INT(11) NULL DEFAULT NULL,
  `PES_ID` INT(11) NOT NULL COMMENT 'FK para pessoas (cliente)',
  `USU_ID` INT(11) NULL DEFAULT NULL COMMENT 'FK para usuarios',
  `LAN_ID` INT(11) NULL DEFAULT NULL COMMENT 'FK para lancamentos',
  PRIMARY KEY (`PDS_ID`),
  INDEX `idx_pedidos_pes_id` (`PES_ID` ASC),
  INDEX `idx_pedidos_usu_id` (`USU_ID` ASC),
  INDEX `idx_pedidos_lan_id` (`LAN_ID` ASC),
  INDEX `idx_pedidos_tipo` (`PDS_TIPO` ASC),
  INDEX `idx_pedidos_status` (`PDS_STATUS` ASC),
  CONSTRAINT `fk_pedidos_pessoas`
    FOREIGN KEY (`PES_ID`)
    REFERENCES `pessoas` (`PES_ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pedidos_usuarios`
    FOREIGN KEY (`USU_ID`)
    REFERENCES `usuarios` (`idUsuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pedidos_lancamentos`
    FOREIGN KEY (`LAN_ID`)
    REFERENCES `lancamentos` (`idLancamentos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Tabela ITENS_PEDIDOS
-- Prefixo: ITP_
-- Tipo: PLURAL (tabela de detalhe)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ITENS_PEDIDOS` (
  `ITP_ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ITP_SUBTOTAL` DECIMAL(10, 2) NULL DEFAULT 0,
  `ITP_QUANTIDADE` INT(11) NULL DEFAULT NULL,
  `ITP_PRECO` DECIMAL(10, 2) NULL DEFAULT 0,
  `PDS_ID` INT(11) UNSIGNED NOT NULL COMMENT 'FK para PEDIDOS',
  `PRO_ID` INT(11) NOT NULL COMMENT 'FK para produtos',
  PRIMARY KEY (`ITP_ID`),
  INDEX `idx_itens_pedidos_pds_id` (`PDS_ID` ASC),
  INDEX `idx_itens_pedidos_pro_id` (`PRO_ID` ASC),
  CONSTRAINT `fk_itens_pedidos_pedidos`
    FOREIGN KEY (`PDS_ID`)
    REFERENCES `PEDIDOS` (`PDS_ID`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_itens_pedidos_produtos`
    FOREIGN KEY (`PRO_ID`)
    REFERENCES `produtos` (`idProdutos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

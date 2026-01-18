CREATE TABLE IF NOT EXISTS `faturamento_entrada` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `operacao_comercial_id` INT(11) NOT NULL,
    `chave_acesso` VARCHAR(44) NOT NULL,
    `numero_nfe` VARCHAR(20) NOT NULL,
    `data_entrada` DATE NOT NULL,
    `data_emissao` DATE NOT NULL,
    `fornecedor_id` INT(11) NOT NULL,
    `despesas` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `frete` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `total_base_icms` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `total_icms` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `total_base_icms_st` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `total_icms_st` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `valor_ipi` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `total_nota` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `data_criacao` DATETIME NOT NULL,
    `data_atualizacao` DATETIME DEFAULT NULL,
    `usuario_id` INT(11) NOT NULL,
    `observacoes` TEXT,
    PRIMARY KEY (`id`),
    KEY `fk_faturamento_entrada_operacao_comercial` (`operacao_comercial_id`),
    KEY `fk_faturamento_entrada_fornecedor` (`fornecedor_id`),
    KEY `fk_faturamento_entrada_usuario` (`usuario_id`),
    CONSTRAINT `fk_faturamento_entrada_operacao_comercial` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_faturamento_entrada_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_faturamento_entrada_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`idUsuarios`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `faturamento_entrada_itens` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `faturamento_entrada_id` INT(11) NOT NULL,
    `produto_id` INT(11) NOT NULL,
    `quantidade` DECIMAL(10,2) NOT NULL,
    `valor_unitario` DECIMAL(10,2) NOT NULL,
    `desconto` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `base_calculo_icms` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `aliquota_icms` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `valor_icms` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `base_calculo_icms_st` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `aliquota_icms_st` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `valor_icms_st` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `valor_ipi` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `total_item` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `cst` VARCHAR(2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_faturamento_entrada_itens_faturamento_entrada` (`faturamento_entrada_id`),
    KEY `fk_faturamento_entrada_itens_produto` (`produto_id`),
    CONSTRAINT `fk_faturamento_entrada_itens_faturamento_entrada` FOREIGN KEY (`faturamento_entrada_id`) REFERENCES `faturamento_entrada`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_faturamento_entrada_itens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`idProdutos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

-- Add valor_ipi column if it doesn't exist
ALTER TABLE `faturamento_entrada_itens` 
ADD COLUMN IF NOT EXISTS `valor_ipi` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `valor_icms_st`;

ALTER TABLE `faturamento_entrada` 
ADD COLUMN `operacao_comercial_id` INT(11) NOT NULL AFTER `id`,
ADD CONSTRAINT `fk_faturamento_entrada_operacao_comercial` 
FOREIGN KEY (`operacao_comercial_id`) 
REFERENCES `operacao_comercial`(`id`) 
ON DELETE NO ACTION 
ON UPDATE NO ACTION; 
CREATE TABLE IF NOT EXISTS `tributacao_produto` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome_configuracao` VARCHAR(100) NOT NULL,
    `cst_ipi_saida` VARCHAR(10) NOT NULL,
    `aliq_ipi_saida` DECIMAL(5,2) NOT NULL,
    `cst_pis_saida` VARCHAR(10) NOT NULL,
    `aliq_pis_saida` DECIMAL(5,2) NOT NULL,
    `cst_cofins_saida` VARCHAR(10) NOT NULL,
    `aliq_cofins_saida` DECIMAL(5,2) NOT NULL,
    `regime_fiscal_tributario` ENUM('ICMS Normal (Tributado)', 'Substituição Tributária') NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
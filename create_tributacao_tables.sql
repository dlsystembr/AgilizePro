-- Tabela de operações comerciais
CREATE TABLE IF NOT EXISTS `operacao_comercial` (
    `OPC_ID` INT(11) NOT NULL AUTO_INCREMENT,
    `OPC_SIGLA` VARCHAR(10) NOT NULL,
    `OPC_NOME` VARCHAR(100) NOT NULL,
    `OPC_NATUREZA_OPERACAO` ENUM('Compra', 'Venda', 'Transferencia', 'Outras') NOT NULL,
    `OPC_TIPO_MOVIMENTO` ENUM('Entrada', 'Saida') NOT NULL,
    `OPC_AFETA_CUSTO` TINYINT(1) NOT NULL DEFAULT 0,
    `OPC_FATO_FISCAL` TINYINT(1) NOT NULL DEFAULT 0,
    `OPC_GERA_FINANCEIRO` TINYINT(1) NOT NULL DEFAULT 0,
    `OPC_EMITE_CUPOM` TINYINT(1) NOT NULL DEFAULT 0,
    `OPC_SITUACAO` TINYINT(1) NOT NULL DEFAULT 1,
    `OPC_FINALIDADE_NFE` TINYINT(1) NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`OPC_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Tabela de classificação fiscal
CREATE TABLE IF NOT EXISTS `classificacao_fiscal` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `operacao_comercial_id` INT(11) NOT NULL,
    `natureza_contribuinte` VARCHAR(50) NOT NULL,
    `destinacao` VARCHAR(50) NOT NULL,
    `objetivo_comercial` VARCHAR(50) NOT NULL,
    `cst` VARCHAR(10) NOT NULL,
    `cfop` VARCHAR(10) NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_classificacao_fiscal_operacao` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`OPC_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Tabela de alíquotas
CREATE TABLE IF NOT EXISTS `aliquotas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `uf_origem` CHAR(2) NOT NULL,
    `uf_destino` CHAR(2) NOT NULL,
    `aliquota_origem` DECIMAL(10,2) NOT NULL,
    `aliquota_destino` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_uf_origem_destino` (`uf_origem`, `uf_destino`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Tabela de tributação de produtos
CREATE TABLE IF NOT EXISTS `tributacao_produto` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `produto_id` INT(11) NOT NULL,
    `ncm` VARCHAR(10) NOT NULL,
    `cest` VARCHAR(10) NULL,
    `cfop_padrao` VARCHAR(10) NOT NULL,
    `cst_padrao` VARCHAR(10) NOT NULL,
    `aliquota_icms` DECIMAL(10,2) NOT NULL,
    `aliquota_pis` DECIMAL(10,2) NOT NULL,
    `aliquota_cofins` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_tributacao_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`idProdutos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Inserir operações comerciais padrão
INSERT INTO `operacao_comercial` (`OPC_SIGLA`, `OPC_NOME`, `OPC_NATUREZA_OPERACAO`, `OPC_TIPO_MOVIMENTO`, `OPC_AFETA_CUSTO`, `OPC_FATO_FISCAL`, `OPC_GERA_FINANCEIRO`, `OPC_EMITE_CUPOM`, `OPC_SITUACAO`, `OPC_FINALIDADE_NFE`) VALUES
('VENDA', 'Venda de Mercadorias', 'Venda', 'Saida', 1, 1, 1, 1, 1, 1),
('COMPRA', 'Compra de Mercadorias', 'Compra', 'Entrada', 1, 1, 1, 0, 1, 1),
('DEV', 'Devolução de Mercadorias', 'Venda', 'Entrada', 1, 1, 1, 0, 1, 4),
('BONIF', 'Bonificação', 'Venda', 'Saida', 0, 1, 0, 0, 1, 1),
('TRANSF', 'Transferência', 'Transferencia', 'Saida', 1, 1, 0, 0, 1, 1)
ON DUPLICATE KEY UPDATE `OPC_NOME` = VALUES(`OPC_NOME`);

-- Inserir alíquotas padrão para ICMS interestadual
INSERT INTO `aliquotas` (`uf_origem`, `uf_destino`, `aliquota_origem`, `aliquota_destino`, `created_at`, `updated_at`) VALUES
('SP', 'SP', 18.00, 18.00, NOW(), NOW()),
('SP', 'RJ', 12.00, 7.00, NOW(), NOW()),
('SP', 'MG', 12.00, 7.00, NOW(), NOW()),
('SP', 'PR', 12.00, 7.00, NOW(), NOW()),
('SP', 'SC', 12.00, 7.00, NOW(), NOW()),
('SP', 'RS', 12.00, 7.00, NOW(), NOW()),
('SP', 'BA', 12.00, 7.00, NOW(), NOW()),
('SP', 'PE', 12.00, 7.00, NOW(), NOW()),
('SP', 'CE', 12.00, 7.00, NOW(), NOW()),
('SP', 'DF', 12.00, 7.00, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW(); 
-- Tabela de operações comerciais
CREATE TABLE IF NOT EXISTS `operacao_comercial` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`)
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
    CONSTRAINT `fk_classificacao_fiscal_operacao` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
INSERT INTO `operacao_comercial` (`nome`, `descricao`, `created_at`, `updated_at`) VALUES
('Venda', 'Venda de mercadorias', NOW(), NOW()),
('Devolução', 'Devolução de mercadorias', NOW(), NOW()),
('Remessa', 'Remessa para conserto', NOW(), NOW()),
('Bonificação', 'Bonificação de mercadorias', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

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
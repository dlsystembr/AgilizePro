-- Tabela de operações comerciais
CREATE TABLE IF NOT EXISTS `operacao_comercial` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Adicionar coluna operacao_comercial_id na tabela vendas se não existir
ALTER TABLE `vendas` 
ADD COLUMN IF NOT EXISTS `operacao_comercial_id` INT(11) NULL AFTER `clientes_id`,
ADD COLUMN IF NOT EXISTS `emitida_nfe` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
ADD CONSTRAINT `fk_vendas_operacao_comercial` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Inserir operações comerciais padrão
INSERT INTO `operacao_comercial` (`nome`, `descricao`, `created_at`, `updated_at`) VALUES
('Venda', 'Venda de mercadorias', NOW(), NOW()),
('Devolução', 'Devolução de mercadorias', NOW(), NOW()),
('Remessa', 'Remessa para conserto', NOW(), NOW()),
('Bonificação', 'Bonificação de mercadorias', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Tabela de configurações NFe
CREATE TABLE IF NOT EXISTS `configuracoes_nfe` (
    `idConfiguracao` INT(11) NOT NULL AUTO_INCREMENT,
    `tipo_documento` VARCHAR(10) NOT NULL DEFAULT 'NFe',
    `ambiente` TINYINT(1) NOT NULL DEFAULT 2,
    `versao_nfe` VARCHAR(10) NOT NULL DEFAULT '4.00',
    `tipo_impressao_danfe` TINYINT(1) NOT NULL DEFAULT 1,
    `orientacao_danfe` CHAR(1) NOT NULL DEFAULT 'P',
    `sequencia_nota` INT(11) NOT NULL DEFAULT 1,
    `sequencia_nfce` INT(11) NOT NULL DEFAULT 1,
    `csc` VARCHAR(100) NULL,
    `csc_id` VARCHAR(100) NULL,
    `preview_nfe` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`idConfiguracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Tabela de configurações NFCe
CREATE TABLE IF NOT EXISTS `configuracoes_nfce` (
    `idConfiguracao` INT(11) NOT NULL AUTO_INCREMENT,
    `tipo_documento` VARCHAR(10) NOT NULL DEFAULT 'NFCe',
    `ambiente` TINYINT(1) NOT NULL DEFAULT 2,
    `versao_nfce` VARCHAR(10) NOT NULL DEFAULT '4.00',
    `tipo_impressao_danfe` TINYINT(1) NOT NULL DEFAULT 1,
    `orientacao_danfe` CHAR(1) NOT NULL DEFAULT 'P',
    `sequencia_nota` INT(11) NOT NULL DEFAULT 1,
    `csc` VARCHAR(100) NULL,
    `csc_id` VARCHAR(100) NULL,
    `preview_nfce` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`idConfiguracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Tabela de certificados digitais
CREATE TABLE IF NOT EXISTS `nfe_certificates` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `certificado_digital` LONGBLOB NOT NULL,
    `senha_certificado` VARCHAR(255) NOT NULL,
    `data_validade` DATE NOT NULL,
    `nome_certificado` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Tabela de NFes emitidas
DROP TABLE IF EXISTS `nfe_emitidas`;
CREATE TABLE `nfe_emitidas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `venda_id` INT(11) NULL,
    `cliente_id` INT(11) NULL,
    `numero_nfe` VARCHAR(50) NOT NULL,
    `chave_nfe` VARCHAR(50) NOT NULL,
    `status` VARCHAR(20) NOT NULL,
    `xml` LONGTEXT NOT NULL,
    `protocolo` VARCHAR(50) NOT NULL,
    `motivo` TEXT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `venda_id` (`venda_id`),
    KEY `cliente_id` (`cliente_id`),
    CONSTRAINT `fk_nfe_emitidas_venda` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`idVendas`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_nfe_emitidas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idClientes`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Inserir configurações padrão para NFe
INSERT INTO `configuracoes_nfe` (
    `tipo_documento`, 
    `ambiente`, 
    `versao_nfe`, 
    `tipo_impressao_danfe`, 
    `orientacao_danfe`, 
    `sequencia_nota`, 
    `sequencia_nfce`, 
    `preview_nfe`,
    `created_at`, 
    `updated_at`
) VALUES (
    'NFe',
    2,
    '4.00',
    1,
    'P',
    1,
    1,
    0,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Inserir configurações padrão para NFCe
INSERT INTO `configuracoes_nfce` (
    `tipo_documento`, 
    `ambiente`, 
    `versao_nfce`, 
    `tipo_impressao_danfe`, 
    `orientacao_danfe`, 
    `sequencia_nota`, 
    `preview_nfce`,
    `created_at`, 
    `updated_at`
) VALUES (
    'NFCe',
    2,
    '4.00',
    1,
    'P',
    1,
    0,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE `updated_at` = NOW(); 
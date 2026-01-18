-- Drop table if exists
DROP TABLE IF EXISTS `configuracoes_nfe`;

-- Create table with correct structure
CREATE TABLE IF NOT EXISTS `configuracoes_nfe` (
    `idConfiguracao` INT(11) NOT NULL AUTO_INCREMENT,
    `tipo_documento` VARCHAR(10) NOT NULL DEFAULT 'NFe',
    `ambiente` TINYINT(1) NOT NULL DEFAULT 2 COMMENT '1 = Produção, 2 = Homologação',
    `versao_nfe` VARCHAR(10) NOT NULL DEFAULT '4.00',
    `tipo_impressao_danfe` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Normal, 2 = DANFE Simplificado',
    `orientacao_danfe` CHAR(1) NOT NULL DEFAULT 'P' COMMENT 'P = Retrato, L = Paisagem',
    `sequencia_nota` INT(11) NOT NULL DEFAULT 1,
    `sequencia_nfce` INT(11) NOT NULL DEFAULT 1,
    `csc` VARCHAR(100) NULL DEFAULT NULL,
    `csc_id` VARCHAR(100) NULL DEFAULT NULL,
    `imprimir_logo_nfe` TINYINT(1) NOT NULL DEFAULT 1,
    `preview_nfe` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`idConfiguracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default configuration if not exists
INSERT INTO `configuracoes_nfe` (
    `tipo_documento`, 
    `ambiente`, 
    `versao_nfe`, 
    `tipo_impressao_danfe`, 
    `orientacao_danfe`, 
    `sequencia_nota`, 
    `sequencia_nfce`, 
    `imprimir_logo_nfe`,
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
    1,
    0,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE `updated_at` = NOW(); 
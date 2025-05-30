-- Drop table if exists
DROP TABLE IF EXISTS `configuracoes_nfce`;

-- Create table with correct structure
CREATE TABLE IF NOT EXISTS `configuracoes_nfce` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tipo_documento` VARCHAR(10) NOT NULL DEFAULT 'NFCe',
    `ambiente` TINYINT(1) NOT NULL DEFAULT 2 COMMENT '1 = Produção, 2 = Homologação',
    `versao_nfce` VARCHAR(10) NOT NULL DEFAULT '4.00',
    `tipo_impressao_danfe` TINYINT(1) NOT NULL DEFAULT 4 COMMENT '4 = NFCe',
    `sequencia_nfce` INT(11) NOT NULL DEFAULT 1,
    `csc` VARCHAR(100) NULL DEFAULT NULL,
    `csc_id` VARCHAR(100) NULL DEFAULT NULL,
    `preview_nfce` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default configuration if not exists
INSERT INTO `configuracoes_nfce` (
    `tipo_documento`,
    `ambiente`, 
    `versao_nfce`, 
    `tipo_impressao_danfe`, 
    `sequencia_nfce`, 
    `preview_nfce`,
    `created_at`, 
    `updated_at`
) VALUES (
    'NFCe',
    2,
    '4.00',
    4,
    1,
    0,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE `updated_at` = NOW(); 
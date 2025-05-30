-- Drop table if exists
DROP TABLE IF EXISTS `nfe_monitoradas`;

-- Create table nfe_monitoradas
CREATE TABLE `nfe_monitoradas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `chave_acesso` VARCHAR(44) NOT NULL,
    `numero` VARCHAR(20) NOT NULL,
    `serie` VARCHAR(10) NOT NULL,
    `fornecedor` VARCHAR(255) NOT NULL,
    `data_emissao` DATE NOT NULL,
    `valor` DECIMAL(15,2) NOT NULL,
    `xml` LONGTEXT NOT NULL,
    `processada` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_chave_acesso` (`chave_acesso`),
    KEY `idx_data_emissao` (`data_emissao`),
    KEY `idx_processada` (`processada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
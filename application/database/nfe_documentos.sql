-- Drop table if exists
DROP TABLE IF EXISTS `nfe_documentos`;

-- Create table with correct structure
CREATE TABLE IF NOT EXISTS `nfe_documentos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nfe_id` INT(11) NOT NULL,
    `tipo` VARCHAR(50) NOT NULL COMMENT 'Tipo do documento (cancelamento, carta_correcao, etc)',
    `justificativa` TEXT NULL,
    `protocolo` VARCHAR(50) NULL,
    `data_evento` DATETIME NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `xml` LONGTEXT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `nfe_id` (`nfe_id`),
    CONSTRAINT `fk_nfe_documentos_nfe` FOREIGN KEY (`nfe_id`) REFERENCES `nfe_emitidas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 
-- Drop table if exists to ensure clean creation
DROP TABLE IF EXISTS `classificacao_fiscal`;

-- Create table with correct structure
CREATE TABLE IF NOT EXISTS `classificacao_fiscal` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `operacao_comercial_id` INT(11) NOT NULL,
    `cst` VARCHAR(2) NULL,
    `csosn` VARCHAR(3) NULL,
    `natureza_contribuinte` ENUM('inscrito', 'nao_inscrito') NOT NULL DEFAULT 'nao_inscrito',
    `cfop` VARCHAR(4) NOT NULL,
    `destinacao` VARCHAR(50) NOT NULL,
    `objetivo_comercial` ENUM('consumo', 'revenda') NOT NULL DEFAULT 'consumo',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_classificacao_fiscal_operacao_comercial` 
        FOREIGN KEY (`operacao_comercial_id`) 
        REFERENCES `operacao_comercial` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for better performance
ALTER TABLE `classificacao_fiscal`
    ADD INDEX `idx_operacao_comercial` (`operacao_comercial_id`),
    ADD INDEX `idx_natureza_contribuinte` (`natureza_contribuinte`),
    ADD INDEX `idx_objetivo_comercial` (`objetivo_comercial`); 
-- Drop table if exists
DROP TABLE IF EXISTS `nfe_emitidas`;

-- Create table with correct structure
CREATE TABLE IF NOT EXISTS `nfe_emitidas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `venda_id` INT(11) NULL,
    `entrada_id` INT(11) NULL,
    `cliente_id` INT(11) NULL,
    `modelo` INT(11) DEFAULT NULL,
    `numero_nfe` VARCHAR(50) NOT NULL,
    `chave_nfe` VARCHAR(44) NOT NULL,
    `status` VARCHAR(20) NOT NULL,
    `xml` LONGTEXT NOT NULL,
    `xml_protocolo` TEXT NULL,
    `protocolo` VARCHAR(50) NULL,
    `motivo` TEXT NULL,
    `chave_retorno_evento` TEXT NULL,
    `valor_total` DECIMAL(10,2) DEFAULT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `venda_id` (`venda_id`),
    KEY `entrada_id` (`entrada_id`),
    KEY `cliente_id` (`cliente_id`),
    CONSTRAINT `fk_nfe_emitidas_venda` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`idVendas`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_nfe_emitidas_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `faturamento_entrada` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_nfe_emitidas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idClientes`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 
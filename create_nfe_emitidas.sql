DROP TABLE IF EXISTS `nfe_emitidas`;

CREATE TABLE `nfe_emitidas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `venda_id` INT(11) UNSIGNED NOT NULL,
    `cliente_id` INT(11) UNSIGNED NULL,
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
    CONSTRAINT `fk_nfe_emitidas_venda` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`idVendas`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_nfe_emitidas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`idClientes`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
-- Tabela de vendas
CREATE TABLE IF NOT EXISTS `vendas` (
    `idVendas` INT(11) NOT NULL AUTO_INCREMENT,
    `clientes_id` INT(11) NOT NULL,
    `operacao_comercial_id` INT(11) NOT NULL,
    `usuarios_id` INT(11) NOT NULL,
    `dataVenda` DATETIME NOT NULL,
    `faturado` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('Aberta', 'Faturada', 'Cancelada') NOT NULL DEFAULT 'Aberta',
    `emitida_nfe` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`idVendas`),
    CONSTRAINT `fk_vendas_clientes` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_vendas_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_vendas_operacao_comercial` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`OPC_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- View de vendas
CREATE OR REPLACE VIEW `view_vendas` AS
SELECT 
    v.*,
    c.nomeCliente,
    c.documento,
    u.nome as nome_usuario,
    oc.OPC_NOME as nome_operacao,
    (SELECT SUM(subtotal) FROM itens_de_vendas WHERE vendas_id = v.idVendas) as valor_total
FROM vendas v
LEFT JOIN clientes c ON c.idClientes = v.clientes_id
LEFT JOIN usuarios u ON u.idUsuarios = v.usuarios_id
LEFT JOIN operacao_comercial oc ON oc.OPC_ID = v.operacao_comercial_id; 
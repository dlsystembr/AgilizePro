-- Tabela de faturamento
CREATE TABLE IF NOT EXISTS `faturamento` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `venda_id` INT(11) NOT NULL,
    `operacao_comercial_id` INT(11) NOT NULL,
    `numero_nf` VARCHAR(20) NOT NULL,
    `serie_nf` VARCHAR(10) NOT NULL,
    `data_emissao` DATETIME NOT NULL,
    `valor_total` DECIMAL(10,2) NOT NULL,
    `status` ENUM('Pendente', 'Emitida', 'Cancelada') NOT NULL DEFAULT 'Pendente',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_faturamento_venda` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`idVendas`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_faturamento_operacao_comercial` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`OPC_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- View de faturamento
CREATE OR REPLACE VIEW `view_faturamento` AS
SELECT 
    f.*,
    v.dataVenda,
    c.nomeCliente,
    c.documento,
    oc.OPC_NOME as nome_operacao,
    oc.OPC_SIGLA as sigla_operacao
FROM faturamento f
LEFT JOIN vendas v ON v.idVendas = f.venda_id
LEFT JOIN clientes c ON c.idClientes = v.clientes_id
LEFT JOIN operacao_comercial oc ON oc.OPC_ID = f.operacao_comercial_id; 
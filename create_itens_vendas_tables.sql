-- Tabela de itens de vendas
CREATE TABLE IF NOT EXISTS `itens_de_vendas` (
    `idItens` INT(11) NOT NULL AUTO_INCREMENT,
    `vendas_id` INT(11) NOT NULL,
    `produtos_id` INT(11) NOT NULL,
    `quantidade` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`idItens`),
    CONSTRAINT `fk_itens_vendas_venda` FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_itens_vendas_produto` FOREIGN KEY (`produtos_id`) REFERENCES `produtos` (`idProdutos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- View de itens de vendas
CREATE OR REPLACE VIEW `view_itens_vendas` AS
SELECT 
    iv.*,
    p.descricao as nome_produto,
    p.unidade as unidade_produto,
    v.dataVenda,
    c.nomeCliente,
    c.documento,
    oc.OPC_NOME as nome_operacao,
    oc.OPC_SIGLA as sigla_operacao
FROM itens_de_vendas iv
LEFT JOIN produtos p ON p.idProdutos = iv.produtos_id
LEFT JOIN vendas v ON v.idVendas = iv.vendas_id
LEFT JOIN clientes c ON c.idClientes = v.clientes_id
LEFT JOIN operacao_comercial oc ON oc.OPC_ID = v.operacao_comercial_id; 
-- Tabela de itens de faturamento
CREATE TABLE IF NOT EXISTS `itens_faturamento` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `faturamento_id` INT(11) NOT NULL,
    `produto_id` INT(11) NOT NULL,
    `quantidade` DECIMAL(10,2) NOT NULL,
    `valor_unitario` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_itens_faturamento_faturamento` FOREIGN KEY (`faturamento_id`) REFERENCES `faturamento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_itens_faturamento_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`idProdutos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- View de itens de faturamento
CREATE OR REPLACE VIEW `view_itens_faturamento` AS
SELECT 
    if.*,
    p.descricao as nome_produto,
    p.unidade as unidade_produto,
    f.numero_nf,
    f.serie_nf,
    f.data_emissao,
    oc.OPC_NOME as nome_operacao,
    oc.OPC_SIGLA as sigla_operacao
FROM itens_faturamento if
LEFT JOIN produtos p ON p.idProdutos = if.produto_id
LEFT JOIN faturamento f ON f.id = if.faturamento_id
LEFT JOIN operacao_comercial oc ON oc.OPC_ID = f.operacao_comercial_id; 
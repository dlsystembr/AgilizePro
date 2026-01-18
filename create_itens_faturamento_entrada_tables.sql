-- Tabela de itens de faturamento de entrada
CREATE TABLE IF NOT EXISTS `itens_faturamento_entrada` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `faturamento_entrada_id` INT(11) NOT NULL,
    `produto_id` INT(11) NOT NULL,
    `quantidade` DECIMAL(10,2) NOT NULL,
    `valor_unitario` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_itens_faturamento_entrada_faturamento` FOREIGN KEY (`faturamento_entrada_id`) REFERENCES `faturamento_entrada` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_itens_faturamento_entrada_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`idProdutos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- View de itens de faturamento de entrada
CREATE OR REPLACE VIEW `view_itens_faturamento_entrada` AS
SELECT 
    ife.*,
    p.descricao as nome_produto,
    p.unidade as unidade_produto,
    fe.numero_nf,
    fe.serie_nf,
    fe.data_emissao,
    fe.data_entrada,
    oc.OPC_NOME as nome_operacao,
    oc.OPC_SIGLA as sigla_operacao
FROM itens_faturamento_entrada ife
LEFT JOIN produtos p ON p.idProdutos = ife.produto_id
LEFT JOIN faturamento_entrada fe ON fe.id = ife.faturamento_entrada_id
LEFT JOIN operacao_comercial oc ON oc.OPC_ID = fe.operacao_comercial_id; 
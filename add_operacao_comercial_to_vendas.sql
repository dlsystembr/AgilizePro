-- Adicionar coluna operacao_comercial_id na tabela vendas
ALTER TABLE `vendas` 
ADD COLUMN IF NOT EXISTS `operacao_comercial_id` INT(11) NULL AFTER `clientes_id`,
ADD COLUMN IF NOT EXISTS `emitida_nfe` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
ADD CONSTRAINT `fk_vendas_operacao_comercial` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`OPC_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Atualizar a view de vendas para incluir a operação comercial
CREATE OR REPLACE VIEW `view_vendas` AS
SELECT 
    v.*,
    c.nomeCliente,
    c.documento,
    u.nome as nome_usuario,
    oc.OPC_NOME as nome_operacao,
    (SELECT SUM(quantidade * preco) FROM itens_de_vendas WHERE vendas_id = v.idVendas) as valor_total
FROM 
    vendas v
    LEFT JOIN clientes c ON c.idClientes = v.clientes_id
    LEFT JOIN usuarios u ON u.idUsuarios = v.usuarios_id
    LEFT JOIN operacao_comercial oc ON oc.OPC_ID = v.operacao_comercial_id;

-- Inserir operações comerciais padrão se não existirem
INSERT INTO `operacao_comercial` (`OPC_SIGLA`, `OPC_NOME`, `OPC_NATUREZA_OPERACAO`, `OPC_TIPO_MOVIMENTO`, `OPC_AFETA_CUSTO`, `OPC_FATO_FISCAL`, `OPC_GERA_FINANCEIRO`, `OPC_EMITE_CUPOM`, `OPC_SITUACAO`, `OPC_FINALIDADE_NFE`) VALUES
('VENDA', 'Venda de Mercadorias', 'Venda', 'Saida', 1, 1, 1, 1, 1, 1),
('COMPRA', 'Compra de Mercadorias', 'Compra', 'Entrada', 1, 1, 1, 0, 1, 1),
('DEV', 'Devolução de Mercadorias', 'Venda', 'Entrada', 1, 1, 1, 0, 1, 4),
('BONIF', 'Bonificação', 'Venda', 'Saida', 0, 1, 0, 0, 1, 1),
('TRANSF', 'Transferência', 'Transferencia', 'Saida', 1, 1, 0, 0, 1, 1)
ON DUPLICATE KEY UPDATE `OPC_NOME` = VALUES(`OPC_NOME`);

-- Atualizar vendas existentes para usar a operação comercial padrão (Venda)
UPDATE `vendas` 
SET `operacao_comercial_id` = (SELECT OPC_ID FROM `operacao_comercial` WHERE OPC_SIGLA = 'VENDA' LIMIT 1)
WHERE `operacao_comercial_id` IS NULL; 
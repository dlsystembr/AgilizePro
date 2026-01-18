-- Remover a coluna OPC_EMITE_CUPOM
ALTER TABLE `operacao_comercial` DROP COLUMN `OPC_EMITE_CUPOM`;

-- Adicionar a coluna OPC_MOVIMENTA_ESTOQUE
ALTER TABLE `operacao_comercial` 
ADD COLUMN `OPC_MOVIMENTA_ESTOQUE` TINYINT(1) NOT NULL DEFAULT 0 AFTER `OPC_GERA_FINANCEIRO`;

-- Atualizar os registros existentes
UPDATE `operacao_comercial` 
SET `OPC_MOVIMENTA_ESTOQUE` = 1 
WHERE `OPC_SIGLA` IN ('VENDA', 'COMPRA', 'DEV', 'TRANSF'); 
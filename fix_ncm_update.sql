-- Primeiro, vamos garantir que o campo NCMs existe
ALTER TABLE `produtos` 
ADD COLUMN IF NOT EXISTS `NCMs` VARCHAR(8) NULL COMMENT 'Código NCM do produto' AFTER `unidade`;

-- Atualizar o NCMs dos produtos com base na tributação
UPDATE produtos p
INNER JOIN tributacao_produto tp ON tp.produto_id = p.idProdutos
SET p.NCMs = tp.ncm
WHERE p.NCMs IS NULL OR p.NCMs = '';

-- Atualizar a tributação com base no NCMs dos produtos
UPDATE tributacao_produto tp
INNER JOIN produtos p ON p.idProdutos = tp.produto_id
SET tp.ncm = p.NCMs
WHERE p.NCMs IS NOT NULL AND p.NCMs != '';

-- Criar trigger para manter NCMs e ncm sincronizados
DELIMITER //
DROP TRIGGER IF EXISTS `trg_produtos_ncm_update`//
CREATE TRIGGER `trg_produtos_ncm_update` 
AFTER UPDATE ON `produtos`
FOR EACH ROW
BEGIN
    IF NEW.NCMs != OLD.NCMs THEN
        UPDATE tributacao_produto 
        SET ncm = NEW.NCMs 
        WHERE produto_id = NEW.idProdutos;
    END IF;
END//

DROP TRIGGER IF EXISTS `trg_tributacao_produto_ncm_update`//
CREATE TRIGGER `trg_tributacao_produto_ncm_update` 
AFTER UPDATE ON `tributacao_produto`
FOR EACH ROW
BEGIN
    IF NEW.ncm != OLD.ncm THEN
        UPDATE produtos 
        SET NCMs = NEW.ncm 
        WHERE idProdutos = NEW.produto_id;
    END IF;
END//
DELIMITER ; 
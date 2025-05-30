-- Adicionar campos de tributação na tabela produtos
ALTER TABLE `produtos` 
ADD COLUMN IF NOT EXISTS `NCMs` VARCHAR(8) NULL COMMENT 'Código NCM do produto' AFTER `unidade`,
ADD COLUMN IF NOT EXISTS `origem_produto` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0-Nacional, 1-Estrangeira Importação Direta, 2-Estrangeira Adquirida no Mercado Interno' AFTER `estoque`,
ADD COLUMN IF NOT EXISTS `tributacao_produto_id` INT(11) NULL AFTER `origem_produto`,
ADD CONSTRAINT `fk_produtos_tributacao` FOREIGN KEY (`tributacao_produto_id`) REFERENCES `tributacao_produto` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Atualizar a view de produtos para incluir a tributação
CREATE OR REPLACE VIEW `view_produtos` AS
SELECT 
    p.*,
    tp.ncm,
    tp.cest,
    tp.cfop_padrao,
    tp.cst_padrao,
    tp.aliquota_icms,
    tp.aliquota_pis,
    tp.aliquota_cofins,
    CASE p.origem_produto
        WHEN 0 THEN 'Nacional'
        WHEN 1 THEN 'Estrangeira Importação Direta'
        WHEN 2 THEN 'Estrangeira Adquirida no Mercado Interno'
    END as origem_produto_descricao
FROM 
    produtos p
    LEFT JOIN tributacao_produto tp ON tp.produto_id = p.idProdutos;

-- Inserir tributação padrão para produtos existentes
INSERT INTO `tributacao_produto` (
    `produto_id`,
    `ncm`,
    `cest`,
    `cfop_padrao`,
    `cst_padrao`,
    `aliquota_icms`,
    `aliquota_pis`,
    `aliquota_cofins`,
    `created_at`,
    `updated_at`
)
SELECT 
    p.idProdutos,
    COALESCE(p.NCMs, '00000000'), -- Usa o NCM do produto se existir, senão usa o padrão
    NULL, -- CEST padrão
    '5102', -- CFOP padrão para venda
    '102', -- CST padrão para tributação normal
    18.00, -- Alíquota ICMS padrão
    0.65, -- Alíquota PIS padrão
    3.00, -- Alíquota COFINS padrão
    NOW(),
    NOW()
FROM 
    produtos p
WHERE 
    NOT EXISTS (
        SELECT 1 
        FROM tributacao_produto tp 
        WHERE tp.produto_id = p.idProdutos
    );

-- Atualizar produtos existentes para vincular à tributação padrão
UPDATE produtos p
SET tributacao_produto_id = (
    SELECT id 
    FROM tributacao_produto tp 
    WHERE tp.produto_id = p.idProdutos 
    LIMIT 1
)
WHERE tributacao_produto_id IS NULL;

-- Atualizar NCMs dos produtos com base na tributação
UPDATE produtos p
JOIN tributacao_produto tp ON tp.produto_id = p.idProdutos
SET p.NCMs = tp.ncm
WHERE p.NCMs IS NULL OR p.NCMs = ''; 
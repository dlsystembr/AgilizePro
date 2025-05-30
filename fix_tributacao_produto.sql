-- Verificar e adicionar colunas se não existirem
ALTER TABLE `produtos` 
ADD COLUMN IF NOT EXISTS `NCMs` VARCHAR(8) NULL COMMENT 'Código NCM do produto' AFTER `unidade`,
ADD COLUMN IF NOT EXISTS `tributacao_produto_id` INT(11) NULL AFTER `NCMs`;

-- Remover foreign key se existir
ALTER TABLE `produtos` 
DROP FOREIGN KEY IF EXISTS `fk_produtos_tributacao`;

-- Adicionar foreign key
ALTER TABLE `produtos`
ADD CONSTRAINT `fk_produtos_tributacao` 
FOREIGN KEY (`tributacao_produto_id`) 
REFERENCES `tributacao_produto` (`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Atualizar produtos existentes com NCMs vazio para NULL
UPDATE `produtos` 
SET `NCMs` = NULL 
WHERE `NCMs` = '';

-- Atualizar produtos existentes com tributacao_produto_id vazio para NULL
UPDATE `produtos` 
SET `tributacao_produto_id` = NULL 
WHERE `tributacao_produto_id` = 0 OR `tributacao_produto_id` = '';

-- Recriar a tabela tributacao_produto com a estrutura correta
DROP TABLE IF EXISTS `tributacao_produto`;
CREATE TABLE `tributacao_produto` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome_configuracao` VARCHAR(100) NOT NULL,
    `cst_ipi_saida` VARCHAR(10) NOT NULL,
    `aliq_ipi_saida` DECIMAL(5,2) NOT NULL,
    `cst_pis_saida` VARCHAR(10) NOT NULL,
    `aliq_pis_saida` DECIMAL(5,2) NOT NULL,
    `cst_cofins_saida` VARCHAR(10) NOT NULL,
    `aliq_cofins_saida` DECIMAL(5,2) NOT NULL,
    `regime_fiscal_tributario` ENUM('ICMS Normal (Tributado)', 'Substituição Tributária') NOT NULL,
    `aliq_red_icms` DECIMAL(5,2) NULL DEFAULT 0.00,
    `aliq_iva` DECIMAL(5,2) NULL DEFAULT 0.00,
    `aliq_rd_icms_st` DECIMAL(5,2) NULL DEFAULT 0.00,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Inserir tributação padrão
INSERT INTO `tributacao_produto` (
    `nome_configuracao`,
    `cst_ipi_saida`,
    `aliq_ipi_saida`,
    `cst_pis_saida`,
    `aliq_pis_saida`,
    `cst_cofins_saida`,
    `aliq_cofins_saida`,
    `regime_fiscal_tributario`,
    `aliq_red_icms`,
    `aliq_iva`,
    `aliq_rd_icms_st`,
    `created_at`,
    `updated_at`
) VALUES (
    'Configuração Padrão',
    '999', -- CST IPI padrão
    0.00, -- Alíquota IPI padrão
    '01', -- CST PIS padrão
    0.65, -- Alíquota PIS padrão
    '01', -- CST COFINS padrão
    3.00, -- Alíquota COFINS padrão
    'ICMS Normal (Tributado)', -- Regime fiscal padrão
    0.00, -- Alíquota redução ICMS padrão
    0.00, -- Alíquota IVA padrão
    0.00, -- Alíquota redução ICMS ST padrão
    NOW(),
    NOW()
);

-- Criar trigger para manter NCMs sincronizado
DELIMITER //
DROP TRIGGER IF EXISTS `trg_produtos_ncm_update`//
CREATE TRIGGER `trg_produtos_ncm_update` 
AFTER UPDATE ON `produtos`
FOR EACH ROW
BEGIN
    IF NEW.NCMs != OLD.NCMs THEN
        UPDATE tributacao_produto 
        SET nome_configuracao = CONCAT('NCM: ', NEW.NCMs)
        WHERE produto_id = NEW.idProdutos;
    END IF;
END//
DELIMITER ; 
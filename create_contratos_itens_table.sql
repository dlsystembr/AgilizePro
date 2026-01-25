-- Criar tabela contratos_itens para armazenar serviços vinculados aos contratos
-- Primeiro, criar a tabela sem foreign keys
CREATE TABLE IF NOT EXISTS `contratos_itens` (
    `CTI_ID` INT(11) NOT NULL AUTO_INCREMENT,
    `CTR_ID` INT(11) NOT NULL COMMENT 'ID do Contrato (FK)',
    `PRO_ID` INT(11) NOT NULL COMMENT 'ID do Produto/Serviço (FK)',
    `CTI_PRECO` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Preço do Serviço',
    `CTI_QUANTIDADE` DECIMAL(15,4) NOT NULL DEFAULT 1.0000 COMMENT 'Quantidade Padrão',
    `CTI_ATIVO` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1-Ativo, 0-Inativo',
    `CTI_OBSERVACAO` TEXT NULL COMMENT 'Observações sobre o item',
    `CTI_DATA_CADASTRO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `CTI_DATA_ATUALIZACAO` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `ten_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID do Tenant',
    PRIMARY KEY (`CTI_ID`),
    INDEX `idx_contratos_itens_contrato` (`CTR_ID`),
    INDEX `idx_contratos_itens_produto` (`PRO_ID`),
    INDEX `idx_contratos_itens_tenant` (`ten_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Itens (Serviços) dos Contratos';

-- Adicionar foreign keys separadamente
ALTER TABLE `contratos_itens`
    ADD CONSTRAINT `fk_contratos_itens_contrato` 
    FOREIGN KEY (`CTR_ID`) REFERENCES `contratos`(`CTR_ID`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `contratos_itens`
    ADD CONSTRAINT `fk_contratos_itens_produto` 
    FOREIGN KEY (`PRO_ID`) REFERENCES `produtos`(`PRO_ID`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `contratos_itens`
    ADD CONSTRAINT `fk_contratos_itens_tenant` 
    FOREIGN KEY (`ten_id`) REFERENCES `tenants`(`ten_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

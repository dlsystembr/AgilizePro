-- Add natureza_contribuinte column to clientes table
ALTER TABLE `clientes` 
ADD COLUMN `natureza_contribuinte` ENUM('inscrito', 'nao_inscrito', 'nao_informado') NOT NULL DEFAULT 'nao_informado' AFTER `documento`;
 
-- Add objetivo_comercial column to clientes table
ALTER TABLE `clientes` 
ADD COLUMN `objetivo_comercial` ENUM('consumo', 'revenda') DEFAULT NULL AFTER `fornecedor`; 
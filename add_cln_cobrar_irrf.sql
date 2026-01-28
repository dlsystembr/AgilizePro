-- Adiciona campo para indicar se o cliente deve ter IRRF cobrado na NFCom
-- 1 = Cobrar IRRF
-- 0 = Não cobrar IRRF

-- MySQL 5.x: use apenas ADD COLUMN (se a coluna já existir, ignore o erro)
ALTER TABLE `clientes` 
ADD COLUMN `cln_cobrar_irrf` TINYINT(1) NOT NULL DEFAULT 0 
COMMENT '1=Cobrar IRRF na NFCom, 0=Não cobrar';

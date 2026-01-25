-- Adicionar campos faltantes na tabela nfecom_capa conforme especificação NFCom
-- Campos relacionados a ICMS e FCP que não existiam anteriormente
-- 
-- IMPORTANTE: Execute este script apenas se os campos ainda não existirem.
-- Se algum campo já existir, o comando irá falhar. Nesse caso, comente a linha correspondente.

-- Base de cálculo do ICMS
ALTER TABLE `nfecom_capa` 
ADD COLUMN `NFC_V_BC_ICMS` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base de Cálculo ICMS' 
AFTER `NFC_V_PROD`;

-- Valor do ICMS
ALTER TABLE `nfecom_capa` 
ADD COLUMN `NFC_V_ICMS` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor Total do ICMS' 
AFTER `NFC_V_BC_ICMS`;

-- Valor do ICMS desonerado
ALTER TABLE `nfecom_capa` 
ADD COLUMN `NFC_V_ICMS_DESON` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor Total do ICMS Desonerado' 
AFTER `NFC_V_ICMS`;

-- Valor do FCP (Fundo de Combate à Pobreza)
ALTER TABLE `nfecom_capa` 
ADD COLUMN `NFC_V_FCP` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor Total do FCP' 
AFTER `NFC_V_ICMS_DESON`;

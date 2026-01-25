-- Adicionar campos faltantes na tabela nfecom_itens conforme especificação NFCom
-- Campos relacionados a ICMS, ICMS ST, FCP e CSOSN
-- 
-- IMPORTANTE: Execute este script apenas se os campos ainda não existirem.
-- Se algum campo já existir, o comando irá falhar. Nesse caso, comente a linha correspondente.

-- ============================================
-- CAMPOS DE ICMS BÁSICO
-- ============================================

-- Base de cálculo do ICMS
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_BC_ICMS` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base de Cálculo do ICMS' 
AFTER `NFI_CST_ICMS`;

-- Alíquota do ICMS
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_P_ICMS` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Alíquota do ICMS (%)' 
AFTER `NFI_V_BC_ICMS`;

-- Valor do ICMS
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_ICMS` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do ICMS' 
AFTER `NFI_P_ICMS`;

-- Valor do ICMS Desonerado
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_ICMS_DESON` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do ICMS Desonerado' 
AFTER `NFI_V_ICMS`;

-- Motivo da Desoneração do ICMS
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_MOT_DES_ICMS` VARCHAR(2) NULL COMMENT 'Motivo da Desoneração do ICMS' 
AFTER `NFI_V_ICMS_DESON`;

-- ============================================
-- CAMPOS DE ICMS ST (SUBSTITUIÇÃO TRIBUTÁRIA)
-- ============================================

-- Base de cálculo do ICMS ST
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_BC_ICMS_ST` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base de Cálculo do ICMS ST' 
AFTER `NFI_MOT_DES_ICMS`;

-- Alíquota do ICMS ST
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_P_ICMS_ST` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Alíquota do ICMS ST (%)' 
AFTER `NFI_V_BC_ICMS_ST`;

-- Valor do ICMS ST
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_ICMS_ST` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do ICMS ST' 
AFTER `NFI_P_ICMS_ST`;

-- Base de cálculo do ST Retido
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_BC_ST_RET` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base de Cálculo do ST Retido' 
AFTER `NFI_V_ICMS_ST`;

-- Valor do ICMS ST Retido
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_ICMS_ST_RET` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do ICMS ST Retido' 
AFTER `NFI_V_BC_ST_RET`;

-- Alíquota do ST
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_P_ST` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Alíquota do ST (%)' 
AFTER `NFI_V_ICMS_ST_RET`;

-- Valor do ICMS Próprio do Substituto
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_ICMS_SUBST` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do ICMS Próprio do Substituto' 
AFTER `NFI_P_ST`;

-- ============================================
-- CAMPOS DE FCP (FUNDO DE COMBATE À POBREZA)
-- ============================================

-- Base de cálculo do FCP
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_BC_FCP` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base de Cálculo do FCP' 
AFTER `NFI_V_ICMS_SUBST`;

-- Alíquota do FCP
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_P_FCP` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Alíquota do FCP (%)' 
AFTER `NFI_V_BC_FCP`;

-- Valor do FCP
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_FCP` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do FCP' 
AFTER `NFI_P_FCP`;

-- Valor do FCP ST
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_FCP_ST` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do FCP ST' 
AFTER `NFI_V_FCP`;

-- Valor do FCP ST Retido
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_V_FCP_ST_RET` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor do FCP ST Retido' 
AFTER `NFI_V_FCP_ST`;

-- ============================================
-- CAMPOS DE CSOSN (SIMPLES NACIONAL)
-- ============================================

-- Código de Situação da Operação - Simples Nacional
ALTER TABLE `nfecom_itens` 
ADD COLUMN `NFI_CSOSN` VARCHAR(3) NULL COMMENT 'Código de Situação da Operação - Simples Nacional' 
AFTER `NFI_CST_ICMS`;

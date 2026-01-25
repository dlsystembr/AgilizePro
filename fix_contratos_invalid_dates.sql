-- Script para corrigir datas inválidas na tabela contratos
-- Execute este script ANTES de executar os scripts de sincronização
-- 
-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!
-- 
-- Uso:
-- mysql -u root -p agilizepro < fix_contratos_invalid_dates.sql
-- ou execute no phpMyAdmin

-- Desabilitar modo strict temporariamente
SET SESSION sql_mode = '';

-- ============================================
-- CORRIGIR CTR_DATA_FIM NA TABELA contratos
-- ============================================
-- Verificar se a coluna permite NULL
-- Se permitir NULL, converter 0000-00-00 para NULL
UPDATE `contratos` 
SET `CTR_DATA_FIM` = NULL 
WHERE (`CTR_DATA_FIM` = '0000-00-00' OR `CTR_DATA_FIM` = '0000-00-00 00:00:00')
AND `CTR_DATA_FIM` IS NOT NULL;

-- Se a coluna NÃO permite NULL e você quer usar uma data padrão, descomente:
-- UPDATE `contratos` 
-- SET `CTR_DATA_FIM` = '1900-01-01' 
-- WHERE (`CTR_DATA_FIM` = '0000-00-00' OR `CTR_DATA_FIM` = '0000-00-00 00:00:00')
-- AND `CTR_DATA_FIM` IS NOT NULL;

-- Verificar outras colunas de data na tabela contratos
UPDATE `contratos` 
SET `CTR_DATA_INICIO` = NULL 
WHERE (`CTR_DATA_INICIO` = '0000-00-00' OR `CTR_DATA_INICIO` = '0000-00-00 00:00:00')
AND `CTR_DATA_INICIO` IS NOT NULL;

UPDATE `contratos` 
SET `CTR_DATA_CADASTRO` = NOW() 
WHERE (`CTR_DATA_CADASTRO` = '0000-00-00' OR `CTR_DATA_CADASTRO` = '0000-00-00 00:00:00')
AND `CTR_DATA_CADASTRO` IS NOT NULL;

UPDATE `contratos` 
SET `CTR_DATA_ALTERACAO` = NULL 
WHERE (`CTR_DATA_ALTERACAO` = '0000-00-00' OR `CTR_DATA_ALTERACAO` = '0000-00-00 00:00:00')
AND `CTR_DATA_ALTERACAO` IS NOT NULL;

-- ============================================
-- VERIFICAR SE FOI CORRIGIDO
-- ============================================
-- Execute esta query para verificar:
-- SELECT COUNT(*) as total_invalidos
-- FROM `contratos`
-- WHERE `CTR_DATA_FIM` = '0000-00-00' 
-- OR `CTR_DATA_FIM` = '0000-00-00 00:00:00';

-- ============================================
-- REABILITAR MODO STRICT (RECOMENDADO)
-- ============================================
SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

-- Script para corrigir valores truncados na coluna uf_origem
-- Execute este script ANTES de executar os scripts de sincronização
-- 
-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!
-- 
-- Uso:
-- mysql -u root -p agilizepro < fix_uf_origem.sql
-- ou execute no phpMyAdmin

-- ============================================
-- 1. VERIFICAR ESTRUTURA DA COLUNA
-- ============================================
-- Execute esta query para verificar:
-- SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH
-- FROM information_schema.COLUMNS
-- WHERE TABLE_SCHEMA = 'agilizepro'
-- AND TABLE_NAME = 'aliquotas'
-- AND COLUMN_NAME = 'uf_origem';

-- ============================================
-- 2. CORRIGIR TAMANHO DA COLUNA (se necessário)
-- ============================================
-- Se a coluna não for CHAR(2) ou VARCHAR(2), execute:
ALTER TABLE `aliquotas` MODIFY COLUMN `uf_origem` CHAR(2) NOT NULL;

-- ============================================
-- 3. LIMPAR E CORRIGIR VALORES
-- ============================================
-- Remover espaços, converter para maiúsculas e garantir 2 caracteres
UPDATE `aliquotas` 
SET `uf_origem` = UPPER(TRIM(SUBSTRING(TRIM(`uf_origem`), 1, 2))) 
WHERE LENGTH(TRIM(`uf_origem`)) > 2 
   OR TRIM(`uf_origem`) != `uf_origem`
   OR `uf_origem` LIKE '% %';

-- Corrigir valores vazios (ajuste o valor padrão conforme necessário)
-- UPDATE `aliquotas` 
-- SET `uf_origem` = 'SP' 
-- WHERE `uf_origem` IS NULL 
--    OR `uf_origem` = '' 
--    OR TRIM(`uf_origem`) = '';

-- ============================================
-- 4. VERIFICAR SE FOI CORRIGIDO
-- ============================================
-- Execute esta query para verificar:
-- SELECT id, uf_origem, LENGTH(uf_origem) as tamanho
-- FROM aliquotas
-- WHERE LENGTH(uf_origem) != 2
--    OR uf_origem LIKE '% %'
--    OR TRIM(uf_origem) != uf_origem;

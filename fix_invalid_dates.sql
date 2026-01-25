-- Script para corrigir datas inválidas (0000-00-00) no banco de dados
-- Execute este script ANTES de executar os scripts de sincronização
-- 
-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!
-- 
-- Uso:
-- mysql -u root -p agilizepro < fix_invalid_dates.sql
-- ou execute no phpMyAdmin

-- Desabilitar modo strict temporariamente
SET SESSION sql_mode = '';

-- ============================================
-- CORRIGIR DATAS INVÁLIDAS NA TABELA contratos
-- ============================================
-- Se CTR_DATA_FIM permite NULL, converter para NULL
-- Se não permite NULL, usar data padrão
UPDATE `contratos` SET `CTR_DATA_FIM` = NULL 
WHERE `CTR_DATA_FIM` = '0000-00-00' OR `CTR_DATA_FIM` = '0000-00-00 00:00:00';

-- Se a coluna não permite NULL, descomente e ajuste:
-- UPDATE `contratos` SET `CTR_DATA_FIM` = '1900-01-01' 
-- WHERE `CTR_DATA_FIM` = '0000-00-00' OR `CTR_DATA_FIM` = '0000-00-00 00:00:00';

-- ============================================
-- CORRIGIR OUTRAS TABELAS COM DATAS INVÁLIDAS
-- ============================================
-- Execute esta query para encontrar todas as colunas com datas inválidas:
-- SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE
-- FROM information_schema.COLUMNS
-- WHERE TABLE_SCHEMA = 'agilizepro'
-- AND DATA_TYPE IN ('date', 'datetime', 'timestamp')
-- ORDER BY TABLE_NAME, ORDINAL_POSITION;

-- Para cada coluna encontrada, execute:
-- UPDATE `nome_tabela` SET `nome_coluna` = NULL 
-- WHERE `nome_coluna` = '0000-00-00' OR `nome_coluna` = '0000-00-00 00:00:00';

-- ============================================
-- REABILITAR MODO STRICT (RECOMENDADO)
-- ============================================
SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

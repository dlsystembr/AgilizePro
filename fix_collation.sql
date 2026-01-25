-- Script para padronizar collation para utf8mb4_unicode_ci
-- Recomendado para português brasileiro
-- 
-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!
-- 
-- Uso:
-- mysql -u root -p mapos < fix_collation.sql
-- ou execute no phpMyAdmin

-- ============================================
-- 1. ALTERAR COLLATION DO BANCO
-- ============================================
ALTER DATABASE `mapos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============================================
-- 2. ALTERAR COLLATION DE TODAS AS TABELAS
-- ============================================
-- Execute esta query primeiro para gerar os comandos ALTER TABLE:
-- SELECT CONCAT('ALTER TABLE `', TABLE_NAME, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;')
-- FROM information_schema.TABLES
-- WHERE TABLE_SCHEMA = 'mapos'
-- AND TABLE_COLLATION != 'utf8mb4_unicode_ci';

-- Ou execute manualmente para cada tabela:
-- ALTER TABLE `nome_tabela` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============================================
-- 3. VERIFICAR COLLATION APÓS EXECUÇÃO
-- ============================================
-- SELECT TABLE_NAME, TABLE_COLLATION
-- FROM information_schema.TABLES
-- WHERE TABLE_SCHEMA = 'mapos'
-- ORDER BY TABLE_NAME;

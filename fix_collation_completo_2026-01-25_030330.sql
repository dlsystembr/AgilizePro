-- Script para padronizar collation para utf8mb4_unicode_ci
-- Gerado em: 2026-01-25 03:03:30
-- Banco: mapos

-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!

-- ============================================
-- 1. ALTERAR COLLATION DO BANCO
-- ============================================
ALTER DATABASE `mapos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============================================
-- 2. ALTERAR COLLATION DAS TABELAS
-- ============================================
-- Todas as tabelas já estão com a collation correta!

-- ============================================
-- 3. VERIFICAR COLLATION APÓS EXECUÇÃO
-- ============================================
-- Execute estas queries para verificar:

-- Ver collation do banco:
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
FROM information_schema.SCHEMATA
WHERE SCHEMA_NAME = 'mapos';

-- Ver collation de todas as tabelas:
SELECT TABLE_NAME, TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'mapos'
ORDER BY TABLE_NAME;
-- Script SQL para comparar bancos MapOS e AgilizePro
-- Execute este script no MySQL para ver as diferenças

-- ============================================
-- 1. TABELAS QUE EXISTEM NO MAPOS MAS NÃO NO AGILIZEPRO
-- ============================================
SELECT 
    'Tabelas apenas no MapOS' AS tipo,
    TABLE_NAME AS tabela,
    '' AS coluna,
    '' AS diferenca
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'mapos' 
AND TABLE_NAME NOT IN (
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = 'agilizepro'
)
ORDER BY TABLE_NAME;

-- ============================================
-- 2. TABELAS QUE EXISTEM NO AGILIZEPRO MAS NÃO NO MAPOS
-- ============================================
SELECT 
    'Tabelas apenas no AgilizePro' AS tipo,
    TABLE_NAME AS tabela,
    '' AS coluna,
    '' AS diferenca
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'agilizepro' 
AND TABLE_NAME NOT IN (
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = 'mapos'
)
ORDER BY TABLE_NAME;

-- ============================================
-- 3. COLUNAS QUE EXISTEM NO MAPOS MAS NÃO NO AGILIZEPRO
-- ============================================
SELECT 
    'Coluna faltando no AgilizePro' AS tipo,
    m.TABLE_NAME AS tabela,
    m.COLUMN_NAME AS coluna,
    CONCAT('Tipo: ', m.DATA_TYPE, 
           ', Null: ', m.IS_NULLABLE,
           ', Default: ', IFNULL(m.COLUMN_DEFAULT, 'NULL'),
           ', Extra: ', m.EXTRA) AS diferenca
FROM information_schema.COLUMNS m
LEFT JOIN information_schema.COLUMNS a 
    ON m.TABLE_NAME = a.TABLE_NAME 
    AND m.COLUMN_NAME = a.COLUMN_NAME
    AND a.TABLE_SCHEMA = 'agilizepro'
WHERE m.TABLE_SCHEMA = 'mapos'
AND a.COLUMN_NAME IS NULL
AND m.TABLE_NAME IN (
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = 'agilizepro'
)
ORDER BY m.TABLE_NAME, m.ORDINAL_POSITION;

-- ============================================
-- 4. COLUNAS COM DIFERENÇAS DE TIPO/ESTRUTURA
-- ============================================
SELECT 
    'Diferença de estrutura' AS tipo,
    m.TABLE_NAME AS tabela,
    m.COLUMN_NAME AS coluna,
    CONCAT(
        'MapOS: ', m.DATA_TYPE, 
        ' (', m.IS_NULLABLE, ', ', IFNULL(m.COLUMN_DEFAULT, 'NULL'), ')',
        ' | AgilizePro: ', a.DATA_TYPE,
        ' (', a.IS_NULLABLE, ', ', IFNULL(a.COLUMN_DEFAULT, 'NULL'), ')'
    ) AS diferenca
FROM information_schema.COLUMNS m
INNER JOIN information_schema.COLUMNS a 
    ON m.TABLE_NAME = a.TABLE_NAME 
    AND m.COLUMN_NAME = a.COLUMN_NAME
    AND a.TABLE_SCHEMA = 'agilizepro'
WHERE m.TABLE_SCHEMA = 'mapos'
AND (
    m.DATA_TYPE != a.DATA_TYPE 
    OR m.IS_NULLABLE != a.IS_NULLABLE
    OR m.COLUMN_DEFAULT != a.COLUMN_DEFAULT
    OR m.EXTRA != a.EXTRA
)
ORDER BY m.TABLE_NAME, m.ORDINAL_POSITION;

-- ============================================
-- 5. GERAR SCRIPT ALTER TABLE PARA COLUNAS FALTANTES
-- ============================================
-- Execute esta query e copie os resultados para criar os ALTER TABLE
SELECT 
    CONCAT(
        'ALTER TABLE `', m.TABLE_NAME, '` ',
        'ADD COLUMN `', m.COLUMN_NAME, '` ',
        m.COLUMN_TYPE,
        IF(m.IS_NULLABLE = 'NO', ' NOT NULL', ' NULL'),
        IF(m.COLUMN_DEFAULT IS NOT NULL, 
           CONCAT(' DEFAULT ''', m.COLUMN_DEFAULT, ''''), 
           ''),
        IF(m.EXTRA != '', CONCAT(' ', m.EXTRA), ''),
        ';'
    ) AS script_sql
FROM information_schema.COLUMNS m
LEFT JOIN information_schema.COLUMNS a 
    ON m.TABLE_NAME = a.TABLE_NAME 
    AND m.COLUMN_NAME = a.COLUMN_NAME
    AND a.TABLE_SCHEMA = 'agilizepro'
WHERE m.TABLE_SCHEMA = 'mapos'
AND a.COLUMN_NAME IS NULL
AND m.TABLE_NAME IN (
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = 'agilizepro'
)
ORDER BY m.TABLE_NAME, m.ORDINAL_POSITION;

-- ============================================
-- 6. GERAR SCRIPT ALTER TABLE PARA MODIFICAR COLUNAS
-- ============================================
SELECT 
    CONCAT(
        'ALTER TABLE `', m.TABLE_NAME, '` ',
        'MODIFY COLUMN `', m.COLUMN_NAME, '` ',
        m.COLUMN_TYPE,
        IF(m.IS_NULLABLE = 'NO', ' NOT NULL', ' NULL'),
        IF(m.COLUMN_DEFAULT IS NOT NULL, 
           CONCAT(' DEFAULT ''', m.COLUMN_DEFAULT, ''''), 
           ''),
        IF(m.EXTRA != '', CONCAT(' ', m.EXTRA), ''),
        ';'
    ) AS script_sql
FROM information_schema.COLUMNS m
INNER JOIN information_schema.COLUMNS a 
    ON m.TABLE_NAME = a.TABLE_NAME 
    AND m.COLUMN_NAME = a.COLUMN_NAME
    AND a.TABLE_SCHEMA = 'agilizepro'
WHERE m.TABLE_SCHEMA = 'mapos'
AND (
    m.DATA_TYPE != a.DATA_TYPE 
    OR m.IS_NULLABLE != a.IS_NULLABLE
    OR m.COLUMN_DEFAULT != a.COLUMN_DEFAULT
    OR m.EXTRA != a.EXTRA
)
ORDER BY m.TABLE_NAME, m.ORDINAL_POSITION;

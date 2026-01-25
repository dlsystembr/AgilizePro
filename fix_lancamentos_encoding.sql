-- Script para corrigir encoding corrompido na tabela lancamentos
-- Data: 2026-01-25
-- 
-- Este script corrige os caracteres acentuados e especiais que foram corrompidos durante a conversão de collation
-- 
-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

-- Corrigir "N┬║" para "Nº" na coluna descricao
UPDATE `lancamentos` 
SET `descricao` = REPLACE(`descricao`, 'N┬║', 'Nº')
WHERE `descricao` LIKE '%N┬║%';

-- Corrigir "1┬¬" para "1ª" na coluna descricao
UPDATE `lancamentos` 
SET `descricao` = REPLACE(`descricao`, '1┬¬', '1ª')
WHERE `descricao` LIKE '%1┬¬%';

-- Corrigir "Promiss├│ria" para "Promissória" na coluna forma_pgto
UPDATE `lancamentos` 
SET `forma_pgto` = REPLACE(`forma_pgto`, 'Promiss├│ria', 'Promissória')
WHERE `forma_pgto` LIKE '%Promiss├│ria%';

-- Corrigir "Cart├úo de Cr├®dito" para "Cartão de Crédito" na coluna forma_pgto
UPDATE `lancamentos` 
SET `forma_pgto` = REPLACE(`forma_pgto`, 'Cart├úo de Cr├®dito', 'Cartão de Crédito')
WHERE `forma_pgto` LIKE '%Cart├úo de Cr├®dito%';

-- Verificar se todas as correções foram aplicadas
SELECT 
    `idlancamentos`,
    `descricao`,
    `forma_pgto`,
    `cliente_fornecedor`
FROM `lancamentos`
WHERE `descricao` LIKE '%N┬║%' 
   OR `descricao` LIKE '%1┬¬%'
   OR `forma_pgto` LIKE '%Promiss├│ria%'
   OR `forma_pgto` LIKE '%Cart├úo%'
ORDER BY `idlancamentos`;

-- Se a query acima retornar vazia, todas as correções foram aplicadas com sucesso!

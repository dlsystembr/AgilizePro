-- Script para corrigir encoding corrompido na tabela municipios
-- Data: 2026-01-25
-- 
-- Este script corrige os caracteres acentuados e especiais que foram corrompidos durante a conversão de collation
-- 
-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

-- Correções mais comuns de encoding corrompido encontrados nos dados
-- Padrão: caractere_corrompido -> caractere_correto

-- Corrigir "├í" para "á"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├í', 'á')
WHERE `mun_nome` LIKE '%├í%';

-- Corrigir "├ú" para "ã"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├ú', 'ã')
WHERE `mun_nome` LIKE '%├ú%';

-- Corrigir "├│" para "ó"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├│', 'ó')
WHERE `mun_nome` LIKE '%├│%';

-- Corrigir "├®" para "é"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├®', 'é')
WHERE `mun_nome` LIKE '%├®%';

-- Corrigir "├¡" para "í"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├¡', 'í')
WHERE `mun_nome` LIKE '%├¡%';

-- Corrigir "├║" para "ú"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├║', 'ú')
WHERE `mun_nome` LIKE '%├║%';

-- Corrigir "├┤" para "ô"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├┤', 'ô')
WHERE `mun_nome` LIKE '%├┤%';

-- Corrigir "├º" para "ç"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├º', 'ç')
WHERE `mun_nome` LIKE '%├º%';

-- Correções adicionais encontradas nos dados
-- Corrigir "├ü" para "Á"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├ü', 'Á')
WHERE `mun_nome` LIKE '%├ü%';

-- Corrigir "├Á" para "Ã"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├Á', 'Ã')
WHERE `mun_nome` LIKE '%├Á%';

-- Corrigir "├ì" para "Í"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├ì', 'Í')
WHERE `mun_nome` LIKE '%├ì%';

-- Corrigir "├ô" para "Ô"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├ô', 'Ô')
WHERE `mun_nome` LIKE '%├ô%';

-- Corrigir "├ë" para "Ê"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├ë', 'Ê')
WHERE `mun_nome` LIKE '%├ë%';

-- Corrigir "├é" para "É"
UPDATE `municipios` 
SET `mun_nome` = REPLACE(`mun_nome`, '├é', 'É')
WHERE `mun_nome` LIKE '%├é%';

-- Atualizar data de alteração para os registros modificados
UPDATE `municipios` 
SET `mun_data_atualizacao` = NOW() 
WHERE `mun_nome` LIKE '%├%';

-- Verificar se ainda há problemas de encoding
SELECT 
    `mun_id`,
    `mun_nome`,
    `est_id`,
    `mun_ibge`
FROM `municipios`
WHERE `mun_nome` LIKE '%├%'
ORDER BY `mun_id`
LIMIT 50;

-- Se a query acima retornar vazia, todas as correções foram aplicadas com sucesso!

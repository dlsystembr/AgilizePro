-- Script para corrigir encoding corrompido na tabela protocolos
-- Data: 2026-01-25
-- 
-- Este script corrige os caracteres acentuados e especiais que foram corrompidos durante a conversão de collation
-- 
-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

-- Correções de encoding corrompido encontrados nos dados
-- Padrão: caractere_corrompido -> caractere_correto

-- Corrigir sequências de caracteres corrompidos (devem vir primeiro)
-- Corrigir "├º├ú" para "ção"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├º├ú', 'ção')
WHERE `prt_motivo` LIKE '%├º├ú%';

-- Corrigir "├ç├â" para "ÇÃO" (outra variação)
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ç├â', 'ÇÃO')
WHERE `prt_motivo` LIKE '%├ç├â%';

-- Corrigir "├írios" para "ários"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├írios', 'ários')
WHERE `prt_motivo` LIKE '%├írios%';

-- Corrigir "├ónia" para "ânia"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ónia', 'ânia')
WHERE `prt_motivo` LIKE '%├ónia%';

-- Corrigir "├ídio" para "ádio"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ídio', 'ádio')
WHERE `prt_motivo` LIKE '%├ídio%';

-- Corrigir "├¬ncia" para "ência"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├¬ncia', 'ência')
WHERE `prt_motivo` LIKE '%├¬ncia%';

-- Corrigir "├í" para "á"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├í', 'á')
WHERE `prt_motivo` LIKE '%├í%';

-- Corrigir "├ú" para "ã"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ú', 'ã')
WHERE `prt_motivo` LIKE '%├ú%';

-- Corrigir "├│" para "ó"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├│', 'ó')
WHERE `prt_motivo` LIKE '%├│%';

-- Corrigir "├®" para "é"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├®', 'é')
WHERE `prt_motivo` LIKE '%├®%';

-- Corrigir "├¡" para "í"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├¡', 'í')
WHERE `prt_motivo` LIKE '%├¡%';

-- Corrigir "├║" para "ú"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├║', 'ú')
WHERE `prt_motivo` LIKE '%├║%';

-- Corrigir "├┤" para "ô"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├┤', 'ô')
WHERE `prt_motivo` LIKE '%├┤%';

-- Corrigir "├º" para "ç"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├º', 'ç')
WHERE `prt_motivo` LIKE '%├º%';

-- Corrigir "├ç" para "ç"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ç', 'ç')
WHERE `prt_motivo` LIKE '%├ç%';

-- Corrigir "├ü" para "Á"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ü', 'Á')
WHERE `prt_motivo` LIKE '%├ü%';

-- Corrigir "├Á" para "Ã"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├Á', 'Ã')
WHERE `prt_motivo` LIKE '%├Á%';

-- Corrigir "├ì" para "Í"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ì', 'Í')
WHERE `prt_motivo` LIKE '%├ì%';

-- Corrigir "├ô" para "Ô"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ô', 'Ô')
WHERE `prt_motivo` LIKE '%├ô%';

-- Corrigir "├ë" para "Ê"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├ë', 'Ê')
WHERE `prt_motivo` LIKE '%├ë%';

-- Corrigir "├é" para "É"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├é', 'É')
WHERE `prt_motivo` LIKE '%├é%';

-- Corrigir "├â" para "Â"
UPDATE `protocolos` 
SET `prt_motivo` = REPLACE(`prt_motivo`, '├â', 'Â')
WHERE `prt_motivo` LIKE '%├â%';

-- Verificar se ainda há problemas de encoding
SELECT 
    `prt_id`,
    `nfc_id`,
    `prt_tipo`,
    `prt_motivo`
FROM `protocolos`
WHERE `prt_motivo` LIKE '%├%'
ORDER BY `prt_id`
LIMIT 50;

-- Se a query acima retornar vazia, todas as correções foram aplicadas com sucesso!

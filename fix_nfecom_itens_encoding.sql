-- Script para corrigir encoding corrompido na tabela nfecom_itens
-- Data: 2026-01-25
-- 
-- Este script corrige os caracteres acentuados e especiais que foram corrompidos durante a conversão de collation
-- 
-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

-- Correções de encoding corrompido encontrados nos dados
-- Padrão: caractere_corrompido -> caractere_correto

-- Corrigir sequências de caracteres corrompidos (devem vir primeiro)
-- Corrigir "├º├ú" para "ção"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├º├ú', 'ÇÃO')
WHERE `nfi_x_prod` LIKE '%├º├ú%';

-- Corrigir "├ç├â" para "ÇÃO" (outra variação)
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ç├â', 'ÇÃO')
WHERE `nfi_x_prod` LIKE '%├ç├â%';

-- Corrigir "├írios" para "ários"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├írios', 'ÁRIOS')
WHERE `nfi_x_prod` LIKE '%├írios%';

-- Corrigir "├ónia" para "ânia"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ónia', 'ÂNIA')
WHERE `nfi_x_prod` LIKE '%├ónia%';

-- Corrigir "├ídio" para "ádio"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ídio', 'ÁDIO')
WHERE `nfi_x_prod` LIKE '%├ídio%';

-- Corrigir "├¬ncia" para "ência"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├¬ncia', 'ÊNCIA')
WHERE `nfi_x_prod` LIKE '%├¬ncia%';

-- Corrigir "├ü" para "Á"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ü', 'Á')
WHERE `nfi_x_prod` LIKE '%├ü%';

-- Corrigir "├Á" para "Ã"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├Á', 'Ã')
WHERE `nfi_x_prod` LIKE '%├Á%';

-- Corrigir "├ì" para "Í"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ì', 'Í')
WHERE `nfi_x_prod` LIKE '%├ì%';

-- Corrigir "├ô" para "Ô"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ô', 'Ô')
WHERE `nfi_x_prod` LIKE '%├ô%';

-- Corrigir "├ë" para "Ê"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ë', 'Ê')
WHERE `nfi_x_prod` LIKE '%├ë%';

-- Corrigir "├é" para "É"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├é', 'É')
WHERE `nfi_x_prod` LIKE '%├é%';

-- Corrigir "├â" para "Â"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├â', 'Â')
WHERE `nfi_x_prod` LIKE '%├â%';

-- Corrigir "├í" para "Á"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├í', 'Á')
WHERE `nfi_x_prod` LIKE '%├í%';

-- Corrigir "├ú" para "Ã"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ú', 'Ã')
WHERE `nfi_x_prod` LIKE '%├ú%';

-- Corrigir "├│" para "Ó"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├│', 'Ó')
WHERE `nfi_x_prod` LIKE '%├│%';

-- Corrigir "├®" para "É"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├®', 'É')
WHERE `nfi_x_prod` LIKE '%├®%';

-- Corrigir "├¡" para "Í"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├¡', 'Í')
WHERE `nfi_x_prod` LIKE '%├¡%';

-- Corrigir "├║" para "Ú"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├║', 'Ú')
WHERE `nfi_x_prod` LIKE '%├║%';

-- Corrigir "├┤" para "Ô"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├┤', 'Ô')
WHERE `nfi_x_prod` LIKE '%├┤%';

-- Corrigir "├º" para "Ç"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├º', 'Ç')
WHERE `nfi_x_prod` LIKE '%├º%';

-- Corrigir "├ç" para "Ç"
UPDATE `nfecom_itens` 
SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ç', 'Ç')
WHERE `nfi_x_prod` LIKE '%├ç%';

-- Atualizar data de alteração para os registros modificados
UPDATE `nfecom_itens` 
SET `nfi_data_atualizacao` = NOW() 
WHERE `nfi_x_prod` LIKE '%├%';

-- Verificar se ainda há problemas de encoding
SELECT 
    `nfi_id`,
    `nfc_id`,
    `nfi_x_prod`
FROM `nfecom_itens`
WHERE `nfi_x_prod` LIKE '%├%'
ORDER BY `nfi_id`
LIMIT 50;

-- Se a query acima retornar vazia, todas as correções foram aplicadas com sucesso!

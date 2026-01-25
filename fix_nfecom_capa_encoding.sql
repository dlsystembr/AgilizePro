-- Script para corrigir encoding corrompido na tabela nfecom_capa
-- Data: 2026-01-25
-- 
-- Este script corrige os caracteres acentuados e especiais que foram corrompidos durante a conversão de collation
-- 
-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

-- Correções de encoding corrompido encontrados nos dados
-- Padrão: caractere_corrompido -> caractere_correto

-- Corrigir "├í" para "á"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├í', 'á'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├í', 'á'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├í', 'á'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├í', 'á')
WHERE `nfc_x_motivo` LIKE '%├í%' 
   OR `nfc_inf_cpl` LIKE '%├í%'
   OR `nfc_x_mun_emit` LIKE '%├í%'
   OR `nfc_x_mun_dest` LIKE '%├í%';

-- Corrigir "├ú" para "ã"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ú', 'ã'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ú', 'ã'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ú', 'ã'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ú', 'ã')
WHERE `nfc_x_motivo` LIKE '%├ú%' 
   OR `nfc_inf_cpl` LIKE '%├ú%'
   OR `nfc_x_mun_emit` LIKE '%├ú%'
   OR `nfc_x_mun_dest` LIKE '%├ú%';

-- Corrigir "├│" para "ó"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├│', 'ó'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├│', 'ó'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├│', 'ó'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├│', 'ó')
WHERE `nfc_x_motivo` LIKE '%├│%' 
   OR `nfc_inf_cpl` LIKE '%├│%'
   OR `nfc_x_mun_emit` LIKE '%├│%'
   OR `nfc_x_mun_dest` LIKE '%├│%';

-- Corrigir "├®" para "é"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├®', 'é'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├®', 'é'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├®', 'é'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├®', 'é')
WHERE `nfc_x_motivo` LIKE '%├®%' 
   OR `nfc_inf_cpl` LIKE '%├®%'
   OR `nfc_x_mun_emit` LIKE '%├®%'
   OR `nfc_x_mun_dest` LIKE '%├®%';

-- Corrigir "├¡" para "í"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├¡', 'í'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├¡', 'í'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├¡', 'í'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├¡', 'í')
WHERE `nfc_x_motivo` LIKE '%├¡%' 
   OR `nfc_inf_cpl` LIKE '%├¡%'
   OR `nfc_x_mun_emit` LIKE '%├¡%'
   OR `nfc_x_mun_dest` LIKE '%├¡%';

-- Corrigir "├║" para "ú"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├║', 'ú'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├║', 'ú'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├║', 'ú'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├║', 'ú')
WHERE `nfc_x_motivo` LIKE '%├║%' 
   OR `nfc_inf_cpl` LIKE '%├║%'
   OR `nfc_x_mun_emit` LIKE '%├║%'
   OR `nfc_x_mun_dest` LIKE '%├║%';

-- Corrigir "├┤" para "ô"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├┤', 'ô'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├┤', 'ô'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├┤', 'ô'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├┤', 'ô')
WHERE `nfc_x_motivo` LIKE '%├┤%' 
   OR `nfc_inf_cpl` LIKE '%├┤%'
   OR `nfc_x_mun_emit` LIKE '%├┤%'
   OR `nfc_x_mun_dest` LIKE '%├┤%';

-- Corrigir "├º" para "ç"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├º', 'ç'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├º', 'ç'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├º', 'ç'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├º', 'ç')
WHERE `nfc_x_motivo` LIKE '%├º%' 
   OR `nfc_inf_cpl` LIKE '%├º%'
   OR `nfc_x_mun_emit` LIKE '%├º%'
   OR `nfc_x_mun_dest` LIKE '%├º%';

-- Corrigir "├ü" para "Á"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ü', 'Á'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ü', 'Á'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ü', 'Á'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ü', 'Á')
WHERE `nfc_x_motivo` LIKE '%├ü%' 
   OR `nfc_inf_cpl` LIKE '%├ü%'
   OR `nfc_x_mun_emit` LIKE '%├ü%'
   OR `nfc_x_mun_dest` LIKE '%├ü%';

-- Corrigir "├Á" para "Ã"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├Á', 'Ã'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├Á', 'Ã'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├Á', 'Ã'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├Á', 'Ã')
WHERE `nfc_x_motivo` LIKE '%├Á%' 
   OR `nfc_inf_cpl` LIKE '%├Á%'
   OR `nfc_x_mun_emit` LIKE '%├Á%'
   OR `nfc_x_mun_dest` LIKE '%├Á%';

-- Corrigir "├ì" para "Í"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ì', 'Í'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ì', 'Í'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ì', 'Í'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ì', 'Í')
WHERE `nfc_x_motivo` LIKE '%├ì%' 
   OR `nfc_inf_cpl` LIKE '%├ì%'
   OR `nfc_x_mun_emit` LIKE '%├ì%'
   OR `nfc_x_mun_dest` LIKE '%├ì%';

-- Corrigir "├ô" para "Ô"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ô', 'Ô'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ô', 'Ô'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ô', 'Ô'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ô', 'Ô')
WHERE `nfc_x_motivo` LIKE '%├ô%' 
   OR `nfc_inf_cpl` LIKE '%├ô%'
   OR `nfc_x_mun_emit` LIKE '%├ô%'
   OR `nfc_x_mun_dest` LIKE '%├ô%';

-- Corrigir "├ë" para "Ê"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ë', 'Ê'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ë', 'Ê'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ë', 'Ê'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ë', 'Ê')
WHERE `nfc_x_motivo` LIKE '%├ë%' 
   OR `nfc_inf_cpl` LIKE '%├ë%'
   OR `nfc_x_mun_emit` LIKE '%├ë%'
   OR `nfc_x_mun_dest` LIKE '%├ë%';

-- Corrigir "├é" para "É"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├é', 'É'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├é', 'É'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├é', 'É'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├é', 'É')
WHERE `nfc_x_motivo` LIKE '%├é%' 
   OR `nfc_inf_cpl` LIKE '%├é%'
   OR `nfc_x_mun_emit` LIKE '%├é%'
   OR `nfc_x_mun_dest` LIKE '%├é%';

-- Corrigir "├â" para "Â"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├â', 'Â'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├â', 'Â'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├â', 'Â'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├â', 'Â')
WHERE `nfc_x_motivo` LIKE '%├â%' 
   OR `nfc_inf_cpl` LIKE '%├â%'
   OR `nfc_x_mun_emit` LIKE '%├â%'
   OR `nfc_x_mun_dest` LIKE '%├â%';

-- Corrigir "┬º" para "§"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '┬º', '§'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '┬º', '§')
WHERE `nfc_x_motivo` LIKE '%┬º%' 
   OR `nfc_inf_cpl` LIKE '%┬º%';

-- Corrigir "┬║" para "º"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '┬║', 'º'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '┬║', 'º')
WHERE `nfc_x_motivo` LIKE '%┬║%' 
   OR `nfc_inf_cpl` LIKE '%┬║%';

-- Corrigir "┬¬" para "ª"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '┬¬', 'ª'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '┬¬', 'ª')
WHERE `nfc_x_motivo` LIKE '%┬¬%' 
   OR `nfc_inf_cpl` LIKE '%┬¬%';

-- Corrigir sequências de caracteres corrompidos comuns
-- Corrigir "├º├ú" para "ção"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├º├ú', 'ção'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├º├ú', 'ção')
WHERE `nfc_x_motivo` LIKE '%├º├ú%' 
   OR `nfc_inf_cpl` LIKE '%├º├ú%';

-- Corrigir "├írios" para "ários"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├írios', 'ários'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├írios', 'ários')
WHERE `nfc_x_motivo` LIKE '%├írios%' 
   OR `nfc_inf_cpl` LIKE '%├írios%';

-- Corrigir "├ónia" para "ânia"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ónia', 'ânia'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ónia', 'ânia'),
    `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ónia', 'ânia'),
    `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ónia', 'ânia')
WHERE `nfc_x_motivo` LIKE '%├ónia%' 
   OR `nfc_inf_cpl` LIKE '%├ónia%'
   OR `nfc_x_mun_emit` LIKE '%├ónia%'
   OR `nfc_x_mun_dest` LIKE '%├ónia%';

-- Corrigir "├ídio" para "ádio"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ídio', 'ádio'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ídio', 'ádio')
WHERE `nfc_x_motivo` LIKE '%├ídio%' 
   OR `nfc_inf_cpl` LIKE '%├ídio%';

-- Corrigir "├¬ncia" para "ência"
UPDATE `nfecom_capa` 
SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├¬ncia', 'ência'),
    `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├¬ncia', 'ência')
WHERE `nfc_x_motivo` LIKE '%├¬ncia%' 
   OR `nfc_inf_cpl` LIKE '%├¬ncia%';

-- Atualizar data de alteração para os registros modificados
UPDATE `nfecom_capa` 
SET `nfc_data_atualizacao` = NOW() 
WHERE `nfc_x_motivo` LIKE '%├%' 
   OR `nfc_inf_cpl` LIKE '%├%'
   OR `nfc_x_mun_emit` LIKE '%├%'
   OR `nfc_x_mun_dest` LIKE '%├%'
   OR `nfc_x_motivo` LIKE '%┬%'
   OR `nfc_inf_cpl` LIKE '%┬%';

-- Verificar se ainda há problemas de encoding
SELECT 
    `nfc_id`,
    `nfc_x_motivo`,
    `nfc_x_mun_emit`,
    `nfc_x_mun_dest`
FROM `nfecom_capa`
WHERE `nfc_x_motivo` LIKE '%├%' 
   OR `nfc_inf_cpl` LIKE '%├%'
   OR `nfc_x_mun_emit` LIKE '%├%'
   OR `nfc_x_mun_dest` LIKE '%├%'
   OR `nfc_x_motivo` LIKE '%┬%'
   OR `nfc_inf_cpl` LIKE '%┬%'
ORDER BY `nfc_id`
LIMIT 50;

-- Se a query acima retornar vazia, todas as correções foram aplicadas com sucesso!

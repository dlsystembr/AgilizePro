-- Script para corrigir nomes de estados corrompidos devido a problemas de encoding/collation
-- Data: 2026-01-25
-- 
-- Este script corrige os caracteres acentuados que foram corrompidos durante a conversão de collation
-- 
-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

-- Corrigir nomes dos estados com encoding corrompido
UPDATE `estados` SET `est_nome` = 'Goiás' WHERE `est_id` = 9 AND `est_uf` = 'GO';
UPDATE `estados` SET `est_nome` = 'Maranhão' WHERE `est_id` = 10 AND `est_uf` = 'MA';
UPDATE `estados` SET `est_nome` = 'Pará' WHERE `est_id` = 14 AND `est_uf` = 'PA';
UPDATE `estados` SET `est_nome` = 'Paraíba' WHERE `est_id` = 15 AND `est_uf` = 'PB';
UPDATE `estados` SET `est_nome` = 'Paraná' WHERE `est_id` = 16 AND `est_uf` = 'PR';
UPDATE `estados` SET `est_nome` = 'Piauí' WHERE `est_id` = 18 AND `est_uf` = 'PI';
UPDATE `estados` SET `est_nome` = 'Rondônia' WHERE `est_id` = 22 AND `est_uf` = 'RO';
UPDATE `estados` SET `est_nome` = 'São Paulo' WHERE `est_id` = 25 AND `est_uf` = 'SP';

-- Atualizar data de alteração para os registros modificados
UPDATE `estados` SET `est_data_alteracao` = NOW() 
WHERE `est_id` IN (9, 10, 14, 15, 16, 18, 22, 25);

-- Verificar se todas as correções foram aplicadas
SELECT 
    `est_id`,
    `est_nome`,
    `est_uf`,
    `est_codigo_uf`,
    `est_data_alteracao`
FROM `estados`
WHERE `est_id` IN (9, 10, 14, 15, 16, 18, 22, 25)
ORDER BY `est_id`;

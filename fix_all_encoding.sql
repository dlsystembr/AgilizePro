-- Script UNIFICADO para corrigir encoding corrompido em TODAS as tabelas
-- Data: 2026-01-25
-- 
-- Este script corrige os caracteres acentuados e especiais que foram corrompidos durante a conversão de collation
-- em todas as tabelas: estados, lancamentos, municipios, nfecom_capa, nfecom_itens, protocolos
-- 
-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

-- ============================================================================
-- TABELA: estados
-- ============================================================================
UPDATE `estados` SET `est_nome` = 'Goiás' WHERE `est_id` = 9 AND `est_uf` = 'GO';
UPDATE `estados` SET `est_nome` = 'Maranhão' WHERE `est_id` = 10 AND `est_uf` = 'MA';
UPDATE `estados` SET `est_nome` = 'Pará' WHERE `est_id` = 14 AND `est_uf` = 'PA';
UPDATE `estados` SET `est_nome` = 'Paraíba' WHERE `est_id` = 15 AND `est_uf` = 'PB';
UPDATE `estados` SET `est_nome` = 'Paraná' WHERE `est_id` = 16 AND `est_uf` = 'PR';
UPDATE `estados` SET `est_nome` = 'Piauí' WHERE `est_id` = 18 AND `est_uf` = 'PI';
UPDATE `estados` SET `est_nome` = 'Rondônia' WHERE `est_id` = 22 AND `est_uf` = 'RO';
UPDATE `estados` SET `est_nome` = 'São Paulo' WHERE `est_id` = 25 AND `est_uf` = 'SP';
UPDATE `estados` SET `est_data_alteracao` = NOW() WHERE `est_id` IN (9, 10, 14, 15, 16, 18, 22, 25);

-- ============================================================================
-- TABELA: lancamentos
-- ============================================================================
UPDATE `lancamentos` SET `descricao` = REPLACE(`descricao`, 'N┬║', 'Nº') WHERE `descricao` LIKE '%N┬║%';
UPDATE `lancamentos` SET `descricao` = REPLACE(`descricao`, '1┬¬', '1ª') WHERE `descricao` LIKE '%1┬¬%';
UPDATE `lancamentos` SET `forma_pgto` = REPLACE(`forma_pgto`, 'Promiss├│ria', 'Promissória') WHERE `forma_pgto` LIKE '%Promiss├│ria%';
UPDATE `lancamentos` SET `forma_pgto` = REPLACE(`forma_pgto`, 'Cart├úo de Cr├®dito', 'Cartão de Crédito') WHERE `forma_pgto` LIKE '%Cart├úo de Cr├®dito%';

-- ============================================================================
-- TABELA: municipios
-- ============================================================================
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├í', 'á') WHERE `mun_nome` LIKE '%├í%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├ú', 'ã') WHERE `mun_nome` LIKE '%├ú%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├│', 'ó') WHERE `mun_nome` LIKE '%├│%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├®', 'é') WHERE `mun_nome` LIKE '%├®%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├¡', 'í') WHERE `mun_nome` LIKE '%├¡%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├║', 'ú') WHERE `mun_nome` LIKE '%├║%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├┤', 'ô') WHERE `mun_nome` LIKE '%├┤%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├º', 'ç') WHERE `mun_nome` LIKE '%├º%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├ü', 'Á') WHERE `mun_nome` LIKE '%├ü%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├Á', 'Ã') WHERE `mun_nome` LIKE '%├Á%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├ì', 'Í') WHERE `mun_nome` LIKE '%├ì%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├ô', 'Ô') WHERE `mun_nome` LIKE '%├ô%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├ë', 'Ê') WHERE `mun_nome` LIKE '%├ë%';
UPDATE `municipios` SET `mun_nome` = REPLACE(`mun_nome`, '├é', 'É') WHERE `mun_nome` LIKE '%├é%';
UPDATE `municipios` SET `mun_data_atualizacao` = NOW() WHERE `mun_nome` LIKE '%├%';

-- ============================================================================
-- TABELA: nfecom_capa
-- ============================================================================
-- Sequências primeiro
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├º├ú', 'ção'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├º├ú', 'ção'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├º├ú', 'ção'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├º├ú', 'ção') WHERE `nfc_x_motivo` LIKE '%├º├ú%' OR `nfc_inf_cpl` LIKE '%├º├ú%' OR `nfc_x_mun_emit` LIKE '%├º├ú%' OR `nfc_x_mun_dest` LIKE '%├º├ú%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├írios', 'ários'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├írios', 'ários'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├írios', 'ários'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├írios', 'ários') WHERE `nfc_x_motivo` LIKE '%├írios%' OR `nfc_inf_cpl` LIKE '%├írios%' OR `nfc_x_mun_emit` LIKE '%├írios%' OR `nfc_x_mun_dest` LIKE '%├írios%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ónia', 'ânia'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ónia', 'ânia'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ónia', 'ânia'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ónia', 'ânia') WHERE `nfc_x_motivo` LIKE '%├ónia%' OR `nfc_inf_cpl` LIKE '%├ónia%' OR `nfc_x_mun_emit` LIKE '%├ónia%' OR `nfc_x_mun_dest` LIKE '%├ónia%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ídio', 'ádio'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ídio', 'ádio'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ídio', 'ádio'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ídio', 'ádio') WHERE `nfc_x_motivo` LIKE '%├ídio%' OR `nfc_inf_cpl` LIKE '%├ídio%' OR `nfc_x_mun_emit` LIKE '%├ídio%' OR `nfc_x_mun_dest` LIKE '%├ídio%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├¬ncia', 'ência'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├¬ncia', 'ência') WHERE `nfc_x_motivo` LIKE '%├¬ncia%' OR `nfc_inf_cpl` LIKE '%├¬ncia%';
-- Caracteres individuais
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├í', 'á'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├í', 'á'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├í', 'á'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├í', 'á') WHERE `nfc_x_motivo` LIKE '%├í%' OR `nfc_inf_cpl` LIKE '%├í%' OR `nfc_x_mun_emit` LIKE '%├í%' OR `nfc_x_mun_dest` LIKE '%├í%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ú', 'ã'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ú', 'ã'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ú', 'ã'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ú', 'ã') WHERE `nfc_x_motivo` LIKE '%├ú%' OR `nfc_inf_cpl` LIKE '%├ú%' OR `nfc_x_mun_emit` LIKE '%├ú%' OR `nfc_x_mun_dest` LIKE '%├ú%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├│', 'ó'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├│', 'ó'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├│', 'ó'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├│', 'ó') WHERE `nfc_x_motivo` LIKE '%├│%' OR `nfc_inf_cpl` LIKE '%├│%' OR `nfc_x_mun_emit` LIKE '%├│%' OR `nfc_x_mun_dest` LIKE '%├│%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├®', 'é'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├®', 'é'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├®', 'é'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├®', 'é') WHERE `nfc_x_motivo` LIKE '%├®%' OR `nfc_inf_cpl` LIKE '%├®%' OR `nfc_x_mun_emit` LIKE '%├®%' OR `nfc_x_mun_dest` LIKE '%├®%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├¡', 'í'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├¡', 'í'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├¡', 'í'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├¡', 'í') WHERE `nfc_x_motivo` LIKE '%├¡%' OR `nfc_inf_cpl` LIKE '%├¡%' OR `nfc_x_mun_emit` LIKE '%├¡%' OR `nfc_x_mun_dest` LIKE '%├¡%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├║', 'ú'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├║', 'ú'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├║', 'ú'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├║', 'ú') WHERE `nfc_x_motivo` LIKE '%├║%' OR `nfc_inf_cpl` LIKE '%├║%' OR `nfc_x_mun_emit` LIKE '%├║%' OR `nfc_x_mun_dest` LIKE '%├║%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├┤', 'ô'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├┤', 'ô'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├┤', 'ô'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├┤', 'ô') WHERE `nfc_x_motivo` LIKE '%├┤%' OR `nfc_inf_cpl` LIKE '%├┤%' OR `nfc_x_mun_emit` LIKE '%├┤%' OR `nfc_x_mun_dest` LIKE '%├┤%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├º', 'ç'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├º', 'ç'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├º', 'ç'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├º', 'ç') WHERE `nfc_x_motivo` LIKE '%├º%' OR `nfc_inf_cpl` LIKE '%├º%' OR `nfc_x_mun_emit` LIKE '%├º%' OR `nfc_x_mun_dest` LIKE '%├º%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ü', 'Á'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ü', 'Á'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ü', 'Á'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ü', 'Á') WHERE `nfc_x_motivo` LIKE '%├ü%' OR `nfc_inf_cpl` LIKE '%├ü%' OR `nfc_x_mun_emit` LIKE '%├ü%' OR `nfc_x_mun_dest` LIKE '%├ü%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├Á', 'Ã'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├Á', 'Ã'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├Á', 'Ã'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├Á', 'Ã') WHERE `nfc_x_motivo` LIKE '%├Á%' OR `nfc_inf_cpl` LIKE '%├Á%' OR `nfc_x_mun_emit` LIKE '%├Á%' OR `nfc_x_mun_dest` LIKE '%├Á%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ì', 'Í'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ì', 'Í'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ì', 'Í'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ì', 'Í') WHERE `nfc_x_motivo` LIKE '%├ì%' OR `nfc_inf_cpl` LIKE '%├ì%' OR `nfc_x_mun_emit` LIKE '%├ì%' OR `nfc_x_mun_dest` LIKE '%├ì%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ô', 'Ô'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ô', 'Ô'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ô', 'Ô'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ô', 'Ô') WHERE `nfc_x_motivo` LIKE '%├ô%' OR `nfc_inf_cpl` LIKE '%├ô%' OR `nfc_x_mun_emit` LIKE '%├ô%' OR `nfc_x_mun_dest` LIKE '%├ô%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├ë', 'Ê'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├ë', 'Ê'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├ë', 'Ê'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├ë', 'Ê') WHERE `nfc_x_motivo` LIKE '%├ë%' OR `nfc_inf_cpl` LIKE '%├ë%' OR `nfc_x_mun_emit` LIKE '%├ë%' OR `nfc_x_mun_dest` LIKE '%├ë%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├é', 'É'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├é', 'É'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├é', 'É'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├é', 'É') WHERE `nfc_x_motivo` LIKE '%├é%' OR `nfc_inf_cpl` LIKE '%├é%' OR `nfc_x_mun_emit` LIKE '%├é%' OR `nfc_x_mun_dest` LIKE '%├é%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '├â', 'Â'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '├â', 'Â'), `nfc_x_mun_emit` = REPLACE(`nfc_x_mun_emit`, '├â', 'Â'), `nfc_x_mun_dest` = REPLACE(`nfc_x_mun_dest`, '├â', 'Â') WHERE `nfc_x_motivo` LIKE '%├â%' OR `nfc_inf_cpl` LIKE '%├â%' OR `nfc_x_mun_emit` LIKE '%├â%' OR `nfc_x_mun_dest` LIKE '%├â%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '┬º', '§'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '┬º', '§') WHERE `nfc_x_motivo` LIKE '%┬º%' OR `nfc_inf_cpl` LIKE '%┬º%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '┬║', 'º'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '┬║', 'º') WHERE `nfc_x_motivo` LIKE '%┬║%' OR `nfc_inf_cpl` LIKE '%┬║%';
UPDATE `nfecom_capa` SET `nfc_x_motivo` = REPLACE(`nfc_x_motivo`, '┬¬', 'ª'), `nfc_inf_cpl` = REPLACE(`nfc_inf_cpl`, '┬¬', 'ª') WHERE `nfc_x_motivo` LIKE '%┬¬%' OR `nfc_inf_cpl` LIKE '%┬¬%';
UPDATE `nfecom_capa` SET `nfc_data_atualizacao` = NOW() WHERE `nfc_x_motivo` LIKE '%├%' OR `nfc_inf_cpl` LIKE '%├%' OR `nfc_x_mun_emit` LIKE '%├%' OR `nfc_x_mun_dest` LIKE '%├%' OR `nfc_x_motivo` LIKE '%┬%' OR `nfc_inf_cpl` LIKE '%┬%';

-- ============================================================================
-- TABELA: nfecom_itens
-- ============================================================================
-- Sequências primeiro
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├º├ú', 'ÇÃO') WHERE `nfi_x_prod` LIKE '%├º├ú%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ç├â', 'ÇÃO') WHERE `nfi_x_prod` LIKE '%├ç├â%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├írios', 'ÁRIOS') WHERE `nfi_x_prod` LIKE '%├írios%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ónia', 'ÂNIA') WHERE `nfi_x_prod` LIKE '%├ónia%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ídio', 'ÁDIO') WHERE `nfi_x_prod` LIKE '%├ídio%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├¬ncia', 'ÊNCIA') WHERE `nfi_x_prod` LIKE '%├¬ncia%';
-- Caracteres individuais
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ü', 'Á') WHERE `nfi_x_prod` LIKE '%├ü%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├Á', 'Ã') WHERE `nfi_x_prod` LIKE '%├Á%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ì', 'Í') WHERE `nfi_x_prod` LIKE '%├ì%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ô', 'Ô') WHERE `nfi_x_prod` LIKE '%├ô%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ë', 'Ê') WHERE `nfi_x_prod` LIKE '%├ë%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├é', 'É') WHERE `nfi_x_prod` LIKE '%├é%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├â', 'Â') WHERE `nfi_x_prod` LIKE '%├â%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├í', 'Á') WHERE `nfi_x_prod` LIKE '%├í%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ú', 'Ã') WHERE `nfi_x_prod` LIKE '%├ú%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├│', 'Ó') WHERE `nfi_x_prod` LIKE '%├│%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├®', 'É') WHERE `nfi_x_prod` LIKE '%├®%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├¡', 'Í') WHERE `nfi_x_prod` LIKE '%├¡%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├║', 'Ú') WHERE `nfi_x_prod` LIKE '%├║%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├┤', 'Ô') WHERE `nfi_x_prod` LIKE '%├┤%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├º', 'Ç') WHERE `nfi_x_prod` LIKE '%├º%';
UPDATE `nfecom_itens` SET `nfi_x_prod` = REPLACE(`nfi_x_prod`, '├ç', 'Ç') WHERE `nfi_x_prod` LIKE '%├ç%';
UPDATE `nfecom_itens` SET `nfi_data_atualizacao` = NOW() WHERE `nfi_x_prod` LIKE '%├%';

-- ============================================================================
-- TABELA: protocolos
-- ============================================================================
-- Sequências primeiro
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├º├ú', 'ção') WHERE `prt_motivo` LIKE '%├º├ú%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ç├â', 'ÇÃO') WHERE `prt_motivo` LIKE '%├ç├â%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├írios', 'ários') WHERE `prt_motivo` LIKE '%├írios%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ónia', 'ânia') WHERE `prt_motivo` LIKE '%├ónia%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ídio', 'ádio') WHERE `prt_motivo` LIKE '%├ídio%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├¬ncia', 'ência') WHERE `prt_motivo` LIKE '%├¬ncia%';
-- Caracteres individuais
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├í', 'á') WHERE `prt_motivo` LIKE '%├í%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ú', 'ã') WHERE `prt_motivo` LIKE '%├ú%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├│', 'ó') WHERE `prt_motivo` LIKE '%├│%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├®', 'é') WHERE `prt_motivo` LIKE '%├®%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├¡', 'í') WHERE `prt_motivo` LIKE '%├¡%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├║', 'ú') WHERE `prt_motivo` LIKE '%├║%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├┤', 'ô') WHERE `prt_motivo` LIKE '%├┤%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├º', 'ç') WHERE `prt_motivo` LIKE '%├º%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ç', 'ç') WHERE `prt_motivo` LIKE '%├ç%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ü', 'Á') WHERE `prt_motivo` LIKE '%├ü%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├Á', 'Ã') WHERE `prt_motivo` LIKE '%├Á%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ì', 'Í') WHERE `prt_motivo` LIKE '%├ì%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ô', 'Ô') WHERE `prt_motivo` LIKE '%├ô%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├ë', 'Ê') WHERE `prt_motivo` LIKE '%├ë%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├é', 'É') WHERE `prt_motivo` LIKE '%├é%';
UPDATE `protocolos` SET `prt_motivo` = REPLACE(`prt_motivo`, '├â', 'Â') WHERE `prt_motivo` LIKE '%├â%';

-- ============================================================================
-- VERIFICAÇÃO FINAL
-- ============================================================================
-- Verificar se ainda há problemas de encoding em todas as tabelas
SELECT 'estados' as tabela, COUNT(*) as problemas FROM `estados` WHERE `est_nome` LIKE '%├%' OR `est_nome` LIKE '%┬%'
UNION ALL
SELECT 'lancamentos', COUNT(*) FROM `lancamentos` WHERE `descricao` LIKE '%├%' OR `descricao` LIKE '%┬%' OR `forma_pgto` LIKE '%├%' OR `forma_pgto` LIKE '%┬%'
UNION ALL
SELECT 'municipios', COUNT(*) FROM `municipios` WHERE `mun_nome` LIKE '%├%'
UNION ALL
SELECT 'nfecom_capa', COUNT(*) FROM `nfecom_capa` WHERE `nfc_x_motivo` LIKE '%├%' OR `nfc_inf_cpl` LIKE '%├%' OR `nfc_x_mun_emit` LIKE '%├%' OR `nfc_x_mun_dest` LIKE '%├%' OR `nfc_x_motivo` LIKE '%┬%' OR `nfc_inf_cpl` LIKE '%┬%'
UNION ALL
SELECT 'nfecom_itens', COUNT(*) FROM `nfecom_itens` WHERE `nfi_x_prod` LIKE '%├%'
UNION ALL
SELECT 'protocolos', COUNT(*) FROM `protocolos` WHERE `prt_motivo` LIKE '%├%';

-- Se todas as queries retornarem 0, todas as correções foram aplicadas com sucesso!

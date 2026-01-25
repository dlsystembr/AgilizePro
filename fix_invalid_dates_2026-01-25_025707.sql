-- Script para corrigir datas inválidas (0000-00-00)
-- Gerado em: 2026-01-25 02:57:07
-- Banco: agilizepro

-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!

-- Desabilitar modo strict temporariamente
SET SESSION sql_mode = '';

-- Tabela: lancamentos
UPDATE `lancamentos` SET `data_pagamento` = NULL WHERE `data_pagamento` = '0000-00-00' OR `data_pagamento` = '0000-00-00 00:00:00';

-- Reabilitar modo strict (recomendado)
SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
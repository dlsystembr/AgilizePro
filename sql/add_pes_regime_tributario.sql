-- Regime tributário do destinatário (MEI / Simples Nacional / Regime Normal)
-- Usado na NFCom: se Simples ou MEI, enviar como não contribuinte (indIEDest=9) mesmo tendo IE.
-- A API CNPJ.WS (publica.cnpj.ws) já retorna essa informação (simples_nacional.mei, simples_nacional.simples).

-- 1. Adicionar coluna na tabela pessoas (uma pessoa jurídica tem um regime; cliente é apenas “papel” da pessoa)
ALTER TABLE `pessoas`
ADD COLUMN `pes_regime_tributario` VARCHAR(30) NULL DEFAULT NULL
COMMENT 'MEI, Simples Nacional ou Regime Normal. Obrigatório para CNPJ, opcional para CPF.'
AFTER `pes_fisico_juridico`;

-- Valores aceitos: NULL, 'MEI', 'Simples Nacional', 'Regime Normal'

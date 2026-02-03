-- ============================================
-- Script de Alteração: Separar campo nfc_inf_cpl
-- Data: 2026-01-28
-- Descrição: Adiciona campos separados para observações, 
--            dados bancários, info tributária e mensagem legal
-- ============================================

USE mapos;

-- Adicionar novos campos na tabela nfecom_capa
ALTER TABLE nfecom_capa 
ADD COLUMN nfc_observacoes TEXT COMMENT 'Observações editáveis pelo usuário' AFTER nfc_inf_cpl,
ADD COLUMN nfc_dados_bancarios TEXT COMMENT 'Dados bancários editáveis pelo usuário' AFTER nfc_observacoes,
ADD COLUMN nfc_info_tributaria TEXT COMMENT 'Informações tributárias automáticas (IRRF, etc)' AFTER nfc_dados_bancarios,
ADD COLUMN nfc_msg_legal TEXT COMMENT 'Mensagem legal da classificação fiscal (automática)' AFTER nfc_info_tributaria;

-- Verificar se os campos foram criados
DESCRIBE nfecom_capa;

-- Opcional: Migrar dados existentes do nfc_inf_cpl para nfc_observacoes
-- (apenas se houver dados existentes que você queira preservar)
-- UPDATE nfecom_capa 
-- SET nfc_observacoes = nfc_inf_cpl 
-- WHERE nfc_inf_cpl IS NOT NULL AND nfc_inf_cpl != '';

SELECT 'Campos adicionados com sucesso!' AS status;

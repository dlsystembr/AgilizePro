-- Adicionar coluna NFC_IE_DEST (Inscrição Estadual do Destinatário) na tabela nfecom_capa

ALTER TABLE nfecom_capa 
ADD COLUMN nfc_ie_dest VARCHAR(14) NULL AFTER nfc_ind_ie_dest;

-- Verificar se foi adicionada
SHOW COLUMNS FROM nfecom_capa LIKE 'nfc_ie_dest';

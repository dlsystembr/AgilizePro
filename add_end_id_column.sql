-- Adicionar coluna end_id (ID do endereço usado) na tabela nfecom_capa
-- Isso permite saber qual endereço foi usado na NFCom para buscar a IE correta

ALTER TABLE nfecom_capa 
ADD COLUMN end_id INT(11) NULL AFTER nfc_ie_dest,
ADD KEY idx_end_id (end_id);

-- Verificar se foi adicionada
SHOW COLUMNS FROM nfecom_capa LIKE 'end_id';

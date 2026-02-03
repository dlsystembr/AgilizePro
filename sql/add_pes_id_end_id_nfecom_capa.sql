-- Adicionar colunas pes_id e end_id na tabela nfecom_capa (necessário para salvar/emitir NFCom)
-- O código já envia esses campos no INSERT; sem as colunas ocorre: Unknown column 'pes_id' in 'INSERT INTO'
-- Execute no MySQL (phpMyAdmin ou linha de comando). Se end_id já existir, ignore o erro do passo 2.

-- 1. Adicionar pes_id (pessoas.pes_id do destinatário)
ALTER TABLE nfecom_capa
ADD COLUMN pes_id INT(11) NULL AFTER cln_id;

ALTER TABLE nfecom_capa ADD KEY idx_nfecom_capa_pes_id (pes_id);

-- 2. Adicionar end_id (endereço usado na NFCom) — se der "Duplicate column", a coluna já existe; pule este bloco
ALTER TABLE nfecom_capa
ADD COLUMN end_id INT(11) NULL AFTER nfc_ie_dest;

ALTER TABLE nfecom_capa ADD KEY idx_nfecom_capa_end_id (end_id);

-- 3. Preencher pes_id nos registros antigos (a partir de clientes)
UPDATE nfecom_capa n
INNER JOIN clientes c ON c.cln_id = n.cln_id AND c.ten_id = n.ten_id
SET n.pes_id = c.pes_id
WHERE n.cln_id IS NOT NULL AND (n.pes_id IS NULL OR n.pes_id = 0);

-- Verificar: SELECT nfc_id, cln_id, pes_id, end_id, nfc_ie_dest FROM nfecom_capa LIMIT 5;

-- Incluir pes_id na capa da NFCom e migrar dados existentes (corrigir uso de código vs id da pessoa)
-- A capa passa a usar pes_id (pessoas.pes_id) em vez de cln_id para buscar destinatário.

-- 1. Adicionar coluna pes_id na nfecom_capa
ALTER TABLE nfecom_capa
ADD COLUMN pes_id INT(11) NULL AFTER cln_id,
ADD KEY idx_pes_id (pes_id);

-- 2. Migrar: preencher pes_id a partir de cln_id (para registros antigos)
UPDATE nfecom_capa n
INNER JOIN clientes c ON c.cln_id = n.cln_id AND c.ten_id = n.ten_id
SET n.pes_id = c.pes_id
WHERE n.cln_id IS NOT NULL AND (n.pes_id IS NULL OR n.pes_id = 0);

-- 3. Verificar
-- SELECT nfc_id, cln_id, pes_id, end_id, nfc_ie_dest FROM nfecom_capa LIMIT 10;

-- Atualizar NFCom 81 com IE e end_id corretos
-- Buscar IE do cliente da NFCom 81

SELECT 
    n.nfc_id,
    n.nfc_x_nome_dest,
    n.nfc_cnpj_dest,
    e.end_id,
    d.doc_numero as ie_dest
FROM nfecom_capa n
INNER JOIN pessoas p ON p.pes_cpfcnpj = n.nfc_cnpj_dest
INNER JOIN enderecos e ON e.pes_id = p.pes_id AND e.end_padrao = 1
INNER JOIN documentos d ON d.end_id = e.end_id
WHERE n.nfc_id = 81
  AND d.doc_tipo_documento = 'inscrição estadual'
LIMIT 1;

-- Atualizar NFCom 81
UPDATE nfecom_capa n
INNER JOIN pessoas p ON p.pes_cpfcnpj = n.nfc_cnpj_dest
INNER JOIN enderecos e ON e.pes_id = p.pes_id AND e.end_padrao = 1
INNER JOIN documentos d ON d.end_id = e.end_id
SET 
    n.nfc_ie_dest = d.doc_numero,
    n.end_id = e.end_id
WHERE n.nfc_id = 81
  AND d.doc_tipo_documento = 'inscrição estadual';

-- Verificar
SELECT nfc_id, nfc_x_nome_dest, nfc_ie_dest, end_id 
FROM nfecom_capa 
WHERE nfc_id = 81;

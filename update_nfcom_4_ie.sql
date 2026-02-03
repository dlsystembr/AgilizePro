-- Atualizar NFCom 4 com a IE correta do cliente

-- Buscar a IE do cliente
SELECT d.doc_numero 
FROM nfecom_capa n
INNER JOIN pessoas p ON p.pes_cpfcnpj = n.nfc_cnpj_dest
INNER JOIN enderecos e ON e.pes_id = p.pes_id AND e.end_padrao = 1
INNER JOIN documentos d ON d.end_id = e.end_id
WHERE n.nfc_id = 4 
  AND d.doc_tipo_documento = 'inscrição estadual'
LIMIT 1;

-- Atualizar a NFCom 4 com a IE encontrada
UPDATE nfecom_capa 
SET nfc_ie_dest = (
    SELECT d.doc_numero 
    FROM nfecom_capa n
    INNER JOIN pessoas p ON p.pes_cpfcnpj = n.nfc_cnpj_dest
    INNER JOIN enderecos e ON e.pes_id = p.pes_id AND e.end_padrao = 1
    INNER JOIN documentos d ON d.end_id = e.end_id
    WHERE n.nfc_id = 4 
      AND d.doc_tipo_documento = 'inscrição estadual'
    LIMIT 1
)
WHERE nfc_id = 4;

-- Verificar se foi atualizado
SELECT nfc_id, nfc_x_nome_dest, nfc_ie_dest, nfc_ind_ie_dest 
FROM nfecom_capa 
WHERE nfc_id = 4;

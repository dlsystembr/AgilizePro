-- Query corrigida: pessoa + cliente + endereço + documento (IE)
-- Use para conferir se a IE está vinculada ao endereço usado na NFCom (nfecom_capa.end_id)

-- Exemplo: buscar por pes_id e end_id (substitua os valores)
-- SELECT * FROM ... WHERE pes.pes_id = 52 AND doc.end_id = 38

SELECT
  pes.pes_id,
  pes.pes_cpfcnpj,
  pes.pes_nome,
  pes.pes_razao_social,
  pes.pes_fisico_juridico,
  cli.cln_id,
  end.end_id,
  end.end_logradouro,
  end.end_numero,
  end.end_cep,
  doc.doc_id,
  doc.doc_tipo_documento,
  doc.doc_numero AS ie_dest,
  doc.doc_natureza_contribuinte
FROM pessoas pes
LEFT JOIN clientes cli ON pes.pes_id = cli.pes_id
LEFT JOIN enderecos end ON pes.pes_id = end.pes_id
LEFT JOIN documentos doc ON end.end_id = doc.end_id
  AND doc.doc_tipo_documento IN ('Inscrição Estadual', 'inscrição estadual')
WHERE pes.pes_id = 52
  AND doc.end_id = 38;

-- Para conferir a capa da NFCom e o end_id usado:
-- SELECT nfc_id, cln_id, end_id, nfc_ie_dest, nfc_ind_ie_dest, nfc_x_nome_dest
-- FROM nfecom_capa
-- WHERE nfc_id = 12;

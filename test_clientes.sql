-- Testar busca de clientes
SELECT
    c.CLN_ID as idClientes,
    CASE
        WHEN p.PES_FISICO_JURIDICO = 'F' THEN p.PES_NOME
        ELSE COALESCE(p.PES_RAZAO_SOCIAL, p.PES_NOME)
    END as nomeCliente,
    p.PES_CPFCNPJ as cpf_cnpj
FROM clientes c
LEFT JOIN pessoas p ON p.PES_ID = c.PES_ID
ORDER BY nomeCliente
LIMIT 10;
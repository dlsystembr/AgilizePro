-- Verificar se vNfecom existe no Administrador
SELECT
    nome,
    IF(LOCATE('s:8:"vNfecom"', permissoes) > 0, 'SIM - vNfecom encontrado', 'NÃO - vNfecom não encontrado') as vNfecom_status,
    IF(LOCATE('s:7:"aNfecom"', permissoes) > 0, 'SIM - aNfecom encontrado', 'NÃO - aNfecom não encontrado') as aNfecom_status,
    IF(LOCATE('s:7:"eNfecom"', permissoes) > 0, 'SIM - eNfecom encontrado', 'NÃO - eNfecom não encontrado') as eNfecom_status,
    IF(LOCATE('s:7:"dNfecom"', permissoes) > 0, 'SIM - dNfecom encontrado', 'NÃO - dNfecom não encontrado') as dNfecom_status
FROM permissoes
WHERE nome = 'Administrador';
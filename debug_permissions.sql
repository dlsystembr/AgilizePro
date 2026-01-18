-- Verificar usuários e suas permissões
SELECT u.idUsuarios, u.nome, u.email, u.permissoes_id, p.nome as perfil_nome
FROM usuarios u
LEFT JOIN permissoes p ON u.permissoes_id = p.idPermissao;

-- Verificar permissões disponíveis
SELECT idPermissao, nome FROM permissoes;
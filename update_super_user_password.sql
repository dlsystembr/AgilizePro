-- Script para atualizar a senha do super usuário
-- Execute este script se a senha não estiver funcionando

-- Atualizar senha para 'admin123' (hash correto)
UPDATE usuarios_super 
SET USS_SENHA = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy'
WHERE USS_EMAIL = 'admin@super.com';

-- Verificar se foi atualizado
SELECT USS_ID, USS_NOME, USS_EMAIL, USS_SITUACAO, LEFT(USS_SENHA, 30) as HASH_INICIO
FROM usuarios_super 
WHERE USS_EMAIL = 'admin@super.com';


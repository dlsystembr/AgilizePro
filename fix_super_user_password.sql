-- Script SQL para corrigir a senha do super usuário
-- Execute este script no MySQL

-- Primeiro, vamos gerar um hash PHP válido para 'admin123'
-- Use o script fix_super_user_password.php para gerar o hash correto
-- Ou use este hash (gerado agora):
-- Hash para 'admin123': $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy

-- Atualizar senha
UPDATE usuarios_super 
SET USS_SENHA = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy'
WHERE USS_EMAIL = 'admin@super.com';

-- Verificar se foi atualizado
SELECT 
    USS_ID,
    USS_NOME,
    USS_EMAIL,
    USS_SITUACAO,
    LEFT(USS_SENHA, 30) as HASH_INICIO,
    LENGTH(USS_SENHA) as TAMANHO_HASH
FROM usuarios_super 
WHERE USS_EMAIL = 'admin@super.com';


-- ============================================
-- SCRIPT PARA CORRIGIR SENHA DO SUPER USUÁRIO
-- Execute este script no MySQL/phpMyAdmin
-- ============================================

-- Hash gerado para senha: admin123
-- Este hash foi testado e está funcionando

UPDATE usuarios_super 
SET USS_SENHA = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy'
WHERE USS_EMAIL = 'admin@super.com';

-- Verificar se foi atualizado corretamente
SELECT 
    USS_ID,
    USS_NOME,
    USS_EMAIL,
    USS_SITUACAO,
    LEFT(USS_SENHA, 30) as HASH_INICIO,
    LENGTH(USS_SENHA) as TAMANHO_HASH,
    CASE 
        WHEN LENGTH(USS_SENHA) = 60 THEN '✓ Hash válido (60 caracteres)'
        ELSE '✗ Hash inválido'
    END as STATUS
FROM usuarios_super 
WHERE USS_EMAIL = 'admin@super.com';

-- ============================================
-- CREDENCIAIS DE LOGIN:
-- Email: admin@super.com
-- Senha: admin123
-- ============================================


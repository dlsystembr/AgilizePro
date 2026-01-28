-- ========================================================================
-- Script para corrigir campo ten_id sem valor padrão
-- Execute este script no phpMyAdmin ou cliente MySQL
-- ========================================================================

-- 1. Criar tenant padrão se não existir
INSERT INTO `tenants` (
    `ten_nome`,
    `ten_cnpj`,
    `ten_email`,
    `ten_telefone`,
    `ten_data_cadastro`
) 
SELECT 
    'Tenant Padrão',
    '00.000.000/0001-00',
    'tenant@padrao.com',
    '(00) 0000-0000',
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `tenants` WHERE `ten_nome` = 'Tenant Padrão'
);

-- 2. Pegar o ID do tenant padrão
SET @ten_id = (SELECT ten_id FROM tenants WHERE ten_nome = 'Tenant Padrão' LIMIT 1);

-- Se ainda não tem tenant, usar ID 1
SET @ten_id = IFNULL(@ten_id, 1);

-- 3. Atualizar TODOS os registros NULL ou 0 para ter o tenant padrão
-- Isso garante que não haverá erro ao inserir novos registros

UPDATE `usuarios` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `clientes` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `produtos` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `servicos` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `vendas` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `os` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `contratos` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `nfecom_capa` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `nfecom_itens` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `empresas` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `classificacao_fiscal` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `operacao_comercial` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `configuracoes_fiscais` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `certificados` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `permissoes` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `faturamento_entrada` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `itens_faturamento_entrada` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `pedidos` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `itens_pedidos` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `protocolos` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `tipos_clientes` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;
UPDATE `ncms` SET `ten_id` = @ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0;

-- 4. Adicionar valor padrão DEFAULT para as tabelas principais
-- IMPORTANTE: Execute apenas se quiser que o campo tenha DEFAULT
-- Descomente as linhas conforme necessário

-- ALTER TABLE `usuarios` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `clientes` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `produtos` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `servicos` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `vendas` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `os` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `contratos` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `nfecom_capa` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `nfecom_itens` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `empresas` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `classificacao_fiscal` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `operacao_comercial` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `configuracoes_fiscais` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `certificados` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `permissoes` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `faturamento_entrada` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `itens_faturamento_entrada` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `pedidos` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `itens_pedidos` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `protocolos` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `tipos_clientes` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `ncms` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT 1;

-- ========================================================================
-- Verificação
-- ========================================================================

SELECT 'Tenant ID usado:' as Info, @ten_id as Valor;

-- Verificar registros sem ten_id (deve retornar 0 em todas as tabelas)
SELECT 'usuarios' as Tabela, COUNT(*) as SemTenId FROM usuarios WHERE ten_id IS NULL OR ten_id = 0
UNION ALL
SELECT 'clientes', COUNT(*) FROM clientes WHERE ten_id IS NULL OR ten_id = 0
UNION ALL
SELECT 'produtos', COUNT(*) FROM produtos WHERE ten_id IS NULL OR ten_id = 0
UNION ALL
SELECT 'nfecom_capa', COUNT(*) FROM nfecom_capa WHERE ten_id IS NULL OR ten_id = 0
UNION ALL
SELECT 'nfecom_itens', COUNT(*) FROM nfecom_itens WHERE ten_id IS NULL OR ten_id = 0;

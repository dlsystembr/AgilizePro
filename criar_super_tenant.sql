-- ========================================================================
-- Script SQL para criar usuário super e tenant padrão
-- Execute este script no phpMyAdmin ou cliente MySQL
-- ========================================================================

-- 1. Criar usuário super (se não existir)
INSERT INTO `usuarios_super` (
    `uss_nome`,
    `uss_cpf`,
    `uss_email`,
    `uss_senha`,
    `uss_telefone`,
    `uss_situacao`,
    `uss_data_cadastro`
) VALUES (
    'Administrador Super',
    '000.000.000-00',
    'admin@super.com',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', -- senha: admin123
    '(00) 0000-0000',
    1,
    CURDATE()
) ON DUPLICATE KEY UPDATE `uss_nome` = `uss_nome`;

-- 2. Criar tenant padrão (se não existir)
INSERT INTO `tenants` (
    `ten_nome`,
    `ten_cnpj`,
    `ten_email`,
    `ten_telefone`,
    `ten_data_cadastro`
) VALUES (
    'Tenant Padrão',
    '00.000.000/0001-00',
    'tenant@padrao.com',
    '(00) 0000-0000',
    NOW()
) ON DUPLICATE KEY UPDATE `ten_nome` = `ten_nome`;

-- 3. Pegar o ID do tenant (ajuste se necessário)
SET @ten_id = (SELECT ten_id FROM tenants WHERE ten_nome = 'Tenant Padrão' LIMIT 1);

-- Se não encontrou, usar ID 1
SET @ten_id = IFNULL(@ten_id, 1);

-- 4. Atualizar todos os registros sem ten_id ou com ten_id = 0
-- (Execute apenas as tabelas que existem no seu banco)

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

-- 5. Criar permissão padrão (se não existir)
INSERT INTO `permissoes` (
    `nome`,
    `data`,
    `situacao`,
    `ten_id`,
    `aCliente`, `eCliente`, `dCliente`, `vCliente`,
    `aProduto`, `eProduto`, `dProduto`, `vProduto`,
    `aServico`, `eServico`, `dServico`, `vServico`,
    `aOs`, `eOs`, `dOs`, `vOs`,
    `aVenda`, `eVenda`, `dVenda`, `vVenda`,
    `aContrato`, `eContrato`, `dContrato`, `vContrato`,
    `aNfecom`, `eNfecom`, `dNfecom`, `vNfecom`,
    `rCliente`, `rProduto`, `rServico`, `rOs`, `rVenda`, `rContrato`,
    `aUsuario`, `eUsuario`, `dUsuario`, `vUsuario`,
    `aPermissao`, `ePermissao`, `dPermissao`, `vPermissao`,
    `aConfiguracao`, `eConfiguracao`, `dConfiguracao`, `vConfiguracao`
) 
SELECT 
    'Administrador',
    CURDATE(),
    1,
    @ten_id,
    1, 1, 1, 1, -- Cliente
    1, 1, 1, 1, -- Produto
    1, 1, 1, 1, -- Serviço
    1, 1, 1, 1, -- OS
    1, 1, 1, 1, -- Venda
    1, 1, 1, 1, -- Contrato
    1, 1, 1, 1, -- NFCom
    1, 1, 1, 1, 1, 1, -- Relatórios
    1, 1, 1, 1, -- Usuário
    1, 1, 1, 1, -- Permissão
    1, 1, 1, 1  -- Configuração
WHERE NOT EXISTS (
    SELECT 1 FROM `permissoes` WHERE `ten_id` = @ten_id AND `nome` = 'Administrador'
);

-- 6. Criar usuário padrão (se não existir)
SET @permissao_id = (SELECT idPermissao FROM permissoes WHERE ten_id = @ten_id AND nome = 'Administrador' LIMIT 1);
SET @permissao_id = IFNULL(@permissao_id, 1);

INSERT INTO `usuarios` (
    `nome`,
    `email`,
    `senha`,
    `cpf`,
    `cep`,
    `telefone`,
    `situacao`,
    `dataCadastro`,
    `dataExpiracao`,
    `permissoes_id`,
    `ten_id`
) VALUES (
    'Administrador',
    'admin@tenant.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: admin
    '000.000.000-00',
    '00000-000',
    '(00) 0000-0000',
    1,
    CURDATE(),
    '3000-01-01',
    @permissao_id,
    @ten_id
) ON DUPLICATE KEY UPDATE `ten_id` = @ten_id;

-- ========================================================================
-- Verificação
-- ========================================================================

-- Verificar tenant criado
SELECT 'Tenant criado:' as Info, ten_id, ten_nome FROM tenants WHERE ten_nome = 'Tenant Padrão';

-- Verificar usuário super
SELECT 'Super usuário:' as Info, uss_id, uss_email FROM usuarios_super WHERE uss_email = 'admin@super.com';

-- Verificar usuário comum
SELECT 'Usuário comum:' as Info, idusuarios, email, ten_id FROM usuarios WHERE email = 'admin@tenant.com';

-- Contar registros atualizados por tabela
SELECT 'usuarios' as Tabela, COUNT(*) as Total, COUNT(CASE WHEN ten_id = @ten_id THEN 1 END) as ComTenId FROM usuarios
UNION ALL
SELECT 'clientes', COUNT(*), COUNT(CASE WHEN ten_id = @ten_id THEN 1 END) FROM clientes
UNION ALL
SELECT 'produtos', COUNT(*), COUNT(CASE WHEN ten_id = @ten_id THEN 1 END) FROM produtos
UNION ALL
SELECT 'nfecom_capa', COUNT(*), COUNT(CASE WHEN ten_id = @ten_id THEN 1 END) FROM nfecom_capa;

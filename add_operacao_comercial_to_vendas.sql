-- Adicionar coluna operacao_comercial_id na tabela vendas
ALTER TABLE `vendas` 
ADD COLUMN IF NOT EXISTS `operacao_comercial_id` INT(11) NULL AFTER `clientes_id`,
ADD COLUMN IF NOT EXISTS `emitida_nfe` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
ADD CONSTRAINT `fk_vendas_operacao_comercial` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Atualizar a view de vendas para incluir a operação comercial
CREATE OR REPLACE VIEW `view_vendas` AS
SELECT 
    v.*,
    c.nomeCliente,
    c.documento,
    u.nome as nome_usuario,
    oc.nome as nome_operacao,
    (SELECT SUM(quantidade * preco) FROM itens_de_vendas WHERE vendas_id = v.idVendas) as valor_total
FROM 
    vendas v
    LEFT JOIN clientes c ON c.idClientes = v.clientes_id
    LEFT JOIN usuarios u ON u.idUsuarios = v.usuarios_id
    LEFT JOIN operacao_comercial oc ON oc.id = v.operacao_comercial_id;

-- Inserir operações comerciais padrão se não existirem
INSERT INTO `operacao_comercial` (`nome`, `descricao`, `created_at`, `updated_at`) VALUES
('Venda', 'Venda de mercadorias', NOW(), NOW()),
('Devolução', 'Devolução de mercadorias', NOW(), NOW()),
('Remessa', 'Remessa para conserto', NOW(), NOW()),
('Bonificação', 'Bonificação de mercadorias', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Atualizar vendas existentes para usar a operação comercial padrão (Venda)
UPDATE `vendas` 
SET `operacao_comercial_id` = (SELECT id FROM `operacao_comercial` WHERE nome = 'Venda' LIMIT 1)
WHERE `operacao_comercial_id` IS NULL; 
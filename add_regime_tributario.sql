-- Adicionar regime tributário nas configurações
INSERT INTO `configuracoes` (`config`, `valor`) VALUES
('regime_tributario', '1')
ON DUPLICATE KEY UPDATE `valor` = '1';

-- Atualizar a tabela de operações comerciais para incluir mensagem da nota
ALTER TABLE `operacao_comercial` 
ADD COLUMN IF NOT EXISTS `mensagem_nota` TEXT NULL AFTER `descricao`; 
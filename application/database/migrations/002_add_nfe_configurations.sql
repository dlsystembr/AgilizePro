-- Criação da tabela de configurações se não existir
CREATE TABLE IF NOT EXISTS `configuracoes` (
  `idConfiguracao` int(11) NOT NULL AUTO_INCREMENT,
  `config` varchar(255) NOT NULL,
  `valor` text,
  `tipo_documento` varchar(10) DEFAULT 'NFe' COMMENT 'NFe ou NFCe',
  `ambiente` tinyint(1) DEFAULT 2 COMMENT '1 = Produção, 2 = Homologação',
  `versao_nfe` varchar(10) DEFAULT '4.00',
  `tipo_impressao_danfe` tinyint(1) DEFAULT 1 COMMENT '1 = Normal, 2 = DANFE Simplificado',
  `orientacao_danfe` char(1) DEFAULT 'P' COMMENT 'P = Retrato, L = Paisagem',
  `csc` varchar(255) DEFAULT NULL,
  `csc_id` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idConfiguracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserção das configurações padrão
INSERT INTO `configuracoes` (`config`, `valor`, `created_at`, `updated_at`) VALUES
('app_name', 'Map-OS', NOW(), NOW()),
('app_theme', 'white', NOW(), NOW()),
('control_datatable', '1', NOW(), NOW()),
('tipo_documento', 'NFe', NOW(), NOW()),
('ambiente', '2', NOW(), NOW()),
('versao_nfe', '4.00', NOW(), NOW()),
('tipo_impressao_danfe', '1', NOW(), NOW()),
('orientacao_danfe', 'P', NOW(), NOW());

-- Adiciona as colunas se não existirem
ALTER TABLE `configuracoes`
  ADD COLUMN IF NOT EXISTS `config` varchar(255) NOT NULL AFTER `idConfiguracao`,
  ADD COLUMN IF NOT EXISTS `valor` text AFTER `config`,
  ADD COLUMN IF NOT EXISTS `tipo_documento` varchar(10) DEFAULT 'NFe' COMMENT 'NFe ou NFCe' AFTER `valor`,
  ADD COLUMN IF NOT EXISTS `ambiente` tinyint(1) DEFAULT 2 COMMENT '1 = Produção, 2 = Homologação' AFTER `tipo_documento`,
  ADD COLUMN IF NOT EXISTS `versao_nfe` varchar(10) DEFAULT '4.00' AFTER `ambiente`,
  ADD COLUMN IF NOT EXISTS `tipo_impressao_danfe` tinyint(1) DEFAULT 1 COMMENT '1 = Normal, 2 = DANFE Simplificado' AFTER `versao_nfe`,
  ADD COLUMN IF NOT EXISTS `orientacao_danfe` char(1) DEFAULT 'P' COMMENT 'P = Retrato, L = Paisagem' AFTER `tipo_impressao_danfe`,
  ADD COLUMN IF NOT EXISTS `csc` varchar(255) DEFAULT NULL AFTER `orientacao_danfe`,
  ADD COLUMN IF NOT EXISTS `csc_id` varchar(255) DEFAULT NULL AFTER `csc`,
  ADD COLUMN IF NOT EXISTS `created_at` datetime DEFAULT NULL AFTER `csc_id`,
  ADD COLUMN IF NOT EXISTS `updated_at` datetime DEFAULT NULL AFTER `created_at`; 
-- Drop table if exists
DROP TABLE IF EXISTS `configuracoes_nfe`;

-- Create table
CREATE TABLE `configuracoes_nfe` (
  `idConfiguracao` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(10) NOT NULL DEFAULT 'NFe',
  `ambiente` int(11) NOT NULL DEFAULT 2,
  `versao_nfe` varchar(10) NOT NULL DEFAULT '4.00',
  `tipo_impressao_danfe` int(11) NOT NULL DEFAULT 1,
  `orientacao_danfe` varchar(1) NOT NULL DEFAULT 'P',
  `sequencia_nota` int(11) NOT NULL DEFAULT 1,
  `sequencia_nfce` int(11) NOT NULL DEFAULT 1,
  `csc` varchar(100) DEFAULT NULL,
  `csc_id` varchar(100) DEFAULT NULL,
  `imprimir_logo_nfe` TINYINT(1) NOT NULL DEFAULT 1,
  `preview_nfe` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`idConfiguracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default configuration
INSERT INTO `configuracoes_nfe` (`tipo_documento`, `ambiente`, `versao_nfe`, `tipo_impressao_danfe`, `orientacao_danfe`, `sequencia_nota`, `sequencia_nfce`, `created_at`, `updated_at`) 
VALUES ('NFe', 2, '4.00', 1, 'P', 1, 1, NOW(), NOW()); 
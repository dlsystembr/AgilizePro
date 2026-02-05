-- Tabela parametros (parâmetros do sistema por empresa).
-- Valor em prm_valor (TEXT); prm_tipo_dado define conversão na aplicação.
-- Prefixo de colunas: prm_ (3 letras). FK emp_id -> empresas.
-- IMPORTANTE: emp_id deve ter o MESMO tipo da PK de empresas (emp_id).
-- Se empresas.emp_id for int(11) signed, use INT(11) abaixo; se for UNSIGNED, use INT(11) UNSIGNED.
-- Substitui a tabela configuracoes.

CREATE TABLE IF NOT EXISTS `parametros` (
  `prm_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emp_id` INT(11) NOT NULL COMMENT 'FK empresas (tipo igual a empresas.emp_id)',
  `prm_nome` VARCHAR(80) NOT NULL COMMENT 'Código único do parâmetro (ex: app_name, per_page)',
  `prm_caption` VARCHAR(120) DEFAULT NULL COMMENT 'Rótulo para a tela de configuração',
  `prm_tipo_dado` VARCHAR(20) NOT NULL DEFAULT 'string' COMMENT 'string|integer|float|boolean|datetime|text|json',
  `prm_descricao` VARCHAR(255) DEFAULT NULL COMMENT 'Descrição/ajuda',
  `prm_valor` TEXT DEFAULT NULL COMMENT 'Valor em texto; conversão conforme prm_tipo_dado na aplicação',
  `prm_dado_formatado` VARCHAR(255) DEFAULT NULL COMMENT 'Valor formatado para exibição (opcional)',
  `prm_visivel` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=exibir na tela de parâmetros',
  `prm_grupo` VARCHAR(50) DEFAULT NULL COMMENT 'Agrupador: geral, os, fiscal, notificacoes, nfe, etc.',
  `prm_ordem` INT(11) DEFAULT 0 COMMENT 'Ordem de exibição no grupo',
  `prm_data_atualizacao` DATETIME DEFAULT NULL COMMENT 'Data de alteração',
  PRIMARY KEY (`prm_id`),
  UNIQUE KEY `uk_parametros_empresa_nome` (`emp_id`, `prm_nome`),
  KEY `emp_id` (`emp_id`),
  KEY `prm_grupo` (`prm_grupo`),
  CONSTRAINT `fk_parametros_empresa` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Parâmetros do sistema por empresa (substitui configuracoes)';

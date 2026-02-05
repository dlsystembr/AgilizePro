-- =============================================================================
-- GRUPOS DE USUÁRIO, PERMISSÕES POR GRUPO E VÍNCULO USUÁRIO–GRUPO POR EMPRESA
-- =============================================================================
-- Execute no MySQL (phpMyAdmin ou cliente). FAÇA BACKUP DO BANCO ANTES.
--
-- Regras: TUDO vinculado a emp_id (empresa). Para saber o GRE, use empresas.gre_id.
-- 1. Grupos de usuário (grupo_usuario) pertencem a uma EMPRESA (emp_id).
-- 2. Permissões do grupo (grupo_usuario_permissoes): por empresa (emp_id); uma linha
--    por (grupo, empresa, recurso) com colunas boolean: visualizar, editar, deletar, alterar, relatorio.
-- 3. Usuário–grupo por empresa (grupo_usuario_empresa): por EMPRESA o usuário
--    só pode estar em UM grupo; em outra empresa pode estar em outro grupo.
-- =============================================================================

-- 1) Tabela grupo_usuario (por EMPRESA — GRE obtido via empresas.gre_id)
CREATE TABLE IF NOT EXISTS `grupo_usuario` (
  `gpu_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emp_id` INT(11)  NOT NULL COMMENT 'Empresa (grupos são por empresa)',
  `gpu_nome` VARCHAR(100) NOT NULL,
  `gpu_descricao` VARCHAR(255) DEFAULT NULL,
  `gpu_situacao` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo',
  `gpu_data_cadastro` DATETIME DEFAULT NULL,
  `gpu_data_atualizacao` DATETIME DEFAULT NULL,
  PRIMARY KEY (`gpu_id`),
  KEY `fk_grupo_usuario_empresa` (`emp_id`),
  CONSTRAINT `fk_grupo_usuario_empresa` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Tabela grupo_usuario_permissoes (vinculada ao menu_empresa: só menus que a empresa tem)
-- Uma linha por (grupo, menu_empresa) com colunas boolean: visualizar, editar, deletar, alterar, relatorio.
CREATE TABLE IF NOT EXISTS `grupo_usuario_permissoes` (
  `gup_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gpu_id` INT(11) UNSIGNED NOT NULL COMMENT 'Grupo de Usuário',
  `mep_id` INT(11) UNSIGNED NOT NULL COMMENT 'Menu liberado para a empresa (menu_empresa); recurso = menu',
  `gup_visualizar` TINYINT(1) NOT NULL DEFAULT 0,
  `gup_editar` TINYINT(1) NOT NULL DEFAULT 0,
  `gup_deletar` TINYINT(1) NOT NULL DEFAULT 0,
  `gup_alterar` TINYINT(1) NOT NULL DEFAULT 0,
  `gup_relatorio` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`gup_id`),
  UNIQUE KEY `uk_gup_gpu_mep` (`gpu_id`, `mep_id`),
  CONSTRAINT `fk_gup_grupo_usuario` FOREIGN KEY (`gpu_id`) REFERENCES `grupo_usuario` (`gpu_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_gup_menu_empresa` FOREIGN KEY (`mep_id`) REFERENCES `menu_empresa` (`mep_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Tabela grupo_usuario_empresa (1 grupo por usuário por empresa; limitado ao GRE)
CREATE TABLE IF NOT EXISTS `grupo_usuario_empresa` (
  `uge_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usu_id` INT(11) NOT NULL COMMENT 'Usuário; mesmo tipo de usuarios.usu_id',
  `gpu_id` INT(11) UNSIGNED NOT NULL COMMENT 'Grupo de Usuário',
  `emp_id` INT(11) NOT NULL COMMENT 'Empresa; mesmo tipo de empresas.emp_id',
  `uge_data_cadastro` DATETIME DEFAULT NULL,
  `uge_data_atualizacao` DATETIME DEFAULT NULL,
  PRIMARY KEY (`uge_id`),
  UNIQUE KEY `uk_uge_usu_emp` (`usu_id`, `emp_id`),
  CONSTRAINT `fk_uge_usuarios` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_uge_grupo_usuario` FOREIGN KEY (`gpu_id`) REFERENCES `grupo_usuario` (`gpu_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_uge_empresas` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- FIM. Próximos passos:
-- - Cadastrar grupo_usuario por EMPRESA (ex: "Vendedores", "Gerentes" da emp_id X).
-- - Preencher grupo_usuario_permissoes para cada (grupo, menu_empresa) com os 5 boolean;
--   usar apenas mep_id cuja empresa seja a mesma do grupo (menus já liberados para a empresa).
-- - Vincular usuários a grupos por empresa em grupo_usuario_empresa (gpu_id da mesma emp_id).
-- - Para saber o GRE: buscar empresas.gre_id onde empresas.emp_id = emp_id.
-- - Integrar a biblioteca Permission para usar permissões do grupo quando
--   houver registro em grupo_usuario_empresa para (usu_id, emp_id atual).
-- =============================================================================

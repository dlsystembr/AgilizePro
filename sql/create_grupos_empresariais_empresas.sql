-- Tabela grupos_empresariais (Grupo Empresarial: id + nome + situação + datas)
-- Situação: 1 = ativo, 0 = inativo
-- A tabela empresas já existe; apenas criamos grupos_empresariais e adicionamos gre_id em empresas (FK: nome da coluna pai).

CREATE TABLE IF NOT EXISTS `grupos_empresariais` (
  `gre_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gre_nome` varchar(255) NOT NULL,
  `gre_situacao` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo',
  `gre_data_cadastro` datetime DEFAULT NULL,
  `gre_data_atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`gre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vincular tabela empresas existente ao grupo empresarial
-- Execute apenas se a coluna gre_id ainda não existir em empresas (regra: FK mantém nome da coluna pai)
ALTER TABLE `empresas`
  ADD COLUMN `gre_id` int(11) unsigned DEFAULT NULL COMMENT 'Grupo Empresarial' AFTER `ten_id`,
  ADD KEY `idx_empresas_gre_id` (`gre_id`);

ALTER TABLE `empresas`
  ADD CONSTRAINT `fk_empresas_grupo` FOREIGN KEY (`gre_id`) REFERENCES `grupos_empresariais` (`gre_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

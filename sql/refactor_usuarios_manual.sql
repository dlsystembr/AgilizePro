-- =============================================================================
-- SCRIPT MANUAL: Refatorar tabela usuarios (padrão usu_, gre_id, pes_id)
-- =============================================================================
-- Execute no MySQL (phpMyAdmin ou cliente). FAÇA BACKUP DO BANCO ANTES.
--
-- Ajustes antes de rodar:
-- - Se a coluna de data de cadastro for "datacadastro" (minúsculo), troque
--   dataCadastro por datacadastro no UPDATE do passo 2 e no DROP do passo 9.
-- - Se você usa tabela "ordem_servico" em vez de "os", COMENTE as linhas que
--   mencionam a tabela "os" (DROP FK, CHANGE, ADD FK) e DESCOMENTE as linhas
--   que mencionam "ordem_servico".
-- - Se não existir tabela grupos_empresariais ou pessoas, COMENTE o passo 7 ou 8.
-- - Pode executar bloco a bloco (1, depois 2, depois 3, etc.) para checar erros.
-- =============================================================================

-- 1) Adicionar novas colunas em usuarios
ALTER TABLE `usuarios`
  ADD COLUMN `usu_nome` VARCHAR(255) NULL,
  ADD COLUMN `usu_email` VARCHAR(100) NULL,
  ADD COLUMN `usu_senha` VARCHAR(255) NULL,
  ADD COLUMN `usu_situacao` TINYINT(1) NULL,
  ADD COLUMN `usu_data_cadastro` DATETIME NULL,
  ADD COLUMN `usu_data_atualizacao` DATETIME NULL,
  ADD COLUMN `usu_url_imagem` VARCHAR(255) NULL,
  ADD COLUMN `usu_data_expiracao` DATE NULL,
  ADD COLUMN `gre_id` INT(11) UNSIGNED NULL,
  ADD COLUMN `pes_id` INT(11) UNSIGNED NULL;

-- 2) Copiar dados das colunas antigas para as novas
-- Se sua coluna for "datacadastro" (minúsculo), use: usu_data_cadastro = COALESCE(datacadastro, NOW())
UPDATE `usuarios` SET
  usu_nome = COALESCE(nome, ''),
  usu_email = COALESCE(email, ''),
  usu_senha = COALESCE(senha, ''),
  usu_situacao = COALESCE(situacao, 1),
  usu_data_cadastro = COALESCE(dataCadastro, NOW());

UPDATE `usuarios` SET usu_url_imagem = url_image_user WHERE 1=1;
UPDATE `usuarios` SET usu_data_expiracao = dataExpiracao WHERE 1=1;
UPDATE `usuarios` SET gre_id = ten_id WHERE 1=1;

-- 3) Remover FKs que apontam para usuarios.idUsuarios
ALTER TABLE `garantias` DROP FOREIGN KEY `fk_garantias_usuarios1`;
ALTER TABLE `os` DROP FOREIGN KEY `fk_os_usuarios1`;
ALTER TABLE `vendas` DROP FOREIGN KEY `fk_vendas_usuarios1`;
ALTER TABLE `lancamentos` DROP FOREIGN KEY `fk_lancamentos_usuarios1`;

-- Se existir tabela PEDIDOS com FK para usuarios:
-- ALTER TABLE `PEDIDOS` DROP FOREIGN KEY `fk_pedidos_usuarios`;

-- Se você usa tabela ordem_servico em vez de os, comente o DROP da os acima e use:
-- ALTER TABLE `ordem_servico` DROP FOREIGN KEY `fk_ordem_servico_usuarios`;

-- 4) Renomear idUsuarios -> usu_id em usuarios
ALTER TABLE `usuarios` CHANGE `idUsuarios` `usu_id` INT(11) NOT NULL AUTO_INCREMENT;

-- 5) Renomear coluna de usuário nas tabelas filhas para usu_id (FK = nome da coluna pai)
ALTER TABLE `garantias` CHANGE `usuarios_id` `usu_id` INT(11) NULL;
ALTER TABLE `os` CHANGE `usuarios_id` `usu_id` INT(11) NULL;
ALTER TABLE `vendas` CHANGE `usuarios_id` `usu_id` INT(11) NULL;
ALTER TABLE `lancamentos` CHANGE `usuarios_id` `usu_id` INT(11) NULL;

-- Se você usa ordem_servico (e não os), comente as linhas da tabela "os" acima e use:
-- ALTER TABLE `ordem_servico` CHANGE `orv_usuarios_id` `usu_id` INT(11) NULL;

-- 6) Recriar FKs (usu_id -> usuarios.usu_id)
ALTER TABLE `garantias`
  ADD CONSTRAINT `fk_garantias_usuarios1` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `os`
  ADD CONSTRAINT `fk_os_usuarios1` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `vendas`
  ADD CONSTRAINT `fk_vendas_usuarios1` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `lancamentos`
  ADD CONSTRAINT `fk_lancamentos_usuarios1` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- Se existir PEDIDOS com usu_id:
-- ALTER TABLE `PEDIDOS` ADD CONSTRAINT `fk_pedidos_usuarios` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- Se você usa ordem_servico:
-- ALTER TABLE `ordem_servico` ADD CONSTRAINT `fk_ordem_servico_usuarios` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- 7) FK gre_id -> grupos_empresariais (execute só se a tabela grupos_empresariais existir)
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_grupo` FOREIGN KEY (`gre_id`) REFERENCES `grupos_empresariais` (`gre_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- 8) FK pes_id -> pessoas (execute só se a tabela pessoas existir)
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_pessoa` FOREIGN KEY (`pes_id`) REFERENCES `pessoas` (`pes_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- 9) Remover colunas antigas de usuarios
-- IMPORTANTE: permissoes_id NÃO é removido — mantido para o processo atual; remover depois.
ALTER TABLE `usuarios`
  DROP COLUMN nome,
  DROP COLUMN email,
  DROP COLUMN senha,
  DROP COLUMN situacao,
  DROP COLUMN rg,
  DROP COLUMN cpf,
  DROP COLUMN rua,
  DROP COLUMN numero,
  DROP COLUMN bairro,
  DROP COLUMN cidade,
  DROP COLUMN estado,
  DROP COLUMN telefone,
  DROP COLUMN celular,
  DROP COLUMN cep,
  DROP COLUMN url_image_user,
  DROP COLUMN dataExpiracao,
  DROP COLUMN ten_id;

-- Se a coluna de data se chama dataCadastro:
ALTER TABLE `usuarios` DROP COLUMN dataCadastro;
-- Se for datacadastro (minúsculo), use em vez da linha acima:
-- ALTER TABLE `usuarios` DROP COLUMN datacadastro;

-- 10) Ajustar NOT NULL e valor padrão
UPDATE `usuarios` SET usu_nome = 'Sem nome' WHERE usu_nome IS NULL OR usu_nome = '';
UPDATE `usuarios` SET usu_email = 'sem@email' WHERE usu_email IS NULL OR usu_email = '';
UPDATE `usuarios` SET usu_senha = '' WHERE usu_senha IS NULL;

ALTER TABLE `usuarios` MODIFY `usu_nome` VARCHAR(255) NOT NULL;
ALTER TABLE `usuarios` MODIFY `usu_email` VARCHAR(100) NOT NULL;
ALTER TABLE `usuarios` MODIFY `usu_senha` VARCHAR(255) NOT NULL;
ALTER TABLE `usuarios` MODIFY `usu_situacao` TINYINT(1) NOT NULL DEFAULT 1;

-- 11) Índice único no email (login)
ALTER TABLE `usuarios` ADD UNIQUE KEY `uk_usuarios_email` (`usu_email`);

-- =============================================================================
-- FIM. Após executar, atualize o código da aplicação para usar usu_id, usu_nome,
-- usu_email, usu_senha, usu_situacao, usu_data_cadastro, usu_url_imagem,
-- usu_data_expiracao, gre_id, pes_id. A coluna permissoes_id foi mantida (remover depois).
-- Nas tabelas filhas a coluna é usu_id.
-- =============================================================================

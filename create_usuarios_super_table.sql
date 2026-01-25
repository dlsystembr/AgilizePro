-- Script para criar a tabela usuarios_super manualmente
-- Execute este script se a migration não foi executada

CREATE TABLE IF NOT EXISTS `usuarios_super` (
  `USS_ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `USS_NOME` VARCHAR(80) NOT NULL,
  `USS_RG` VARCHAR(20) NULL,
  `USS_CPF` VARCHAR(20) NOT NULL,
  `USS_EMAIL` VARCHAR(80) NOT NULL,
  `USS_SENHA` VARCHAR(200) NOT NULL,
  `USS_TELEFONE` VARCHAR(20) NOT NULL,
  `USS_CELULAR` VARCHAR(20) NULL,
  `USS_SITUACAO` TINYINT(1) NOT NULL DEFAULT 1,
  `USS_DATA_CADASTRO` DATE NOT NULL,
  `USS_DATA_EXPIRACAO` DATE NULL,
  `USS_URL_IMAGE_USER` VARCHAR(255) NULL,
  PRIMARY KEY (`USS_ID`),
  UNIQUE INDEX `uk_usuarios_super_email` (`USS_EMAIL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inserir usuário super padrão
-- Email: admin@super.com
-- Senha: admin123
INSERT INTO `usuarios_super` (
  `USS_NOME`,
  `USS_CPF`,
  `USS_EMAIL`,
  `USS_SENHA`,
  `USS_TELEFONE`,
  `USS_SITUACAO`,
  `USS_DATA_CADASTRO`
) VALUES (
  'Administrador Super',
  '000.000.000-00',
  'admin@super.com',
  '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', -- senha: admin123 (hash correto)
  '(00) 0000-0000',
  1,
  CURDATE()
) ON DUPLICATE KEY UPDATE `USS_NOME` = `USS_NOME`;


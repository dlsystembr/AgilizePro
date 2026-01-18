-- Criar perfil NFECom na tabela permissoes
INSERT INTO `permissoes` (`nome`, `permissoes`, `situacao`) VALUES
('NFECom', 'a:4:{s:8:"vNfecom";s:1:"1";s:8:"aNfecom";s:1:"1";s:8:"eNfecom";s:1:"1";s:8:"dNfecom";s:1:"1";}', 1)
ON DUPLICATE KEY UPDATE permissoes = VALUES(permissoes);
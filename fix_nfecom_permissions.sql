-- Remover permissões antigas criadas incorretamente
DELETE FROM permissoes WHERE nome IN ('vNfecom', 'aNfecom', 'eNfecom', 'dNfecom');

-- Adicionar permissões para NFECom (todas em uma linha serializada)
INSERT INTO `permissoes` (`nome`, `permissoes`, `situacao`) VALUES
('NFECom', 'a:4:{s:8:"vNfecom";s:1:"1";s:8:"aNfecom";s:1:"1";s:8:"eNfecom";s:1:"1";s:8:"dNfecom";s:1:"1";}', 1);
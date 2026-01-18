-- Corrigir a permissão vNfecom no Administrador
-- Primeiro, verificar o conteúdo atual
SELECT permissoes FROM permissoes WHERE nome = 'Administrador';

-- Adicionar vNfecom se não existir
UPDATE permissoes SET permissoes = REPLACE(permissoes, 's:7:"aNfecom";s:1:"1";s:7:"eNfecom";s:1:"1";s:7:"dNfecom";s:1:"1";', 's:7:"vNfecom";s:1:"1";s:7:"aNfecom";s:1:"1";s:7:"eNfecom";s:1:"1";s:7:"dNfecom";s:1:"1";') WHERE nome = 'Administrador' AND LOCATE('vNfecom', permissoes) = 0;

-- Verificar se funcionou
SELECT permissoes FROM permissoes WHERE nome = 'Administrador';
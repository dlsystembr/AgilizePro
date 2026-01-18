-- Verificar se as permissões da NFECom estão no Administrador
SELECT nome, permissoes FROM permissoes WHERE nome = 'Administrador';

-- Verificar se o perfil NFECom existe
SELECT nome, permissoes FROM permissoes WHERE nome = 'NFECom';
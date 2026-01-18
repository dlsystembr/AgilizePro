-- Forçar adição da permissão vNfecom no Administrador
-- Primeiro, verificar conteúdo atual
SELECT LENGTH(permissoes) as tamanho_atual, permissoes FROM permissoes WHERE nome = 'Administrador';

-- Extrair apenas a parte que queremos modificar (depois de dFaturamentoEntrada)
UPDATE permissoes SET permissoes = CONCAT(
    SUBSTRING(permissoes, 1, LOCATE('dFaturamentoEntrada";s:1:"1";}', permissoes) + LENGTH('dFaturamentoEntrada";s:1:"1";}')),
    's:7:"vNfecom";s:1:"1";',
    SUBSTRING(permissoes, LOCATE('dFaturamentoEntrada";s:1:"1";}', permissoes) + LENGTH('dFaturamentoEntrada";s:1:"1";}') + 1)
)
WHERE nome = 'Administrador' AND LOCATE('vNfecom', permissoes) = 0;

-- Verificar resultado
SELECT LENGTH(permissoes) as tamanho_novo, permissoes FROM permissoes WHERE nome = 'Administrador';
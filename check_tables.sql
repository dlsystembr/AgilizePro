-- Verificar tabelas relacionadas a clientes
SHOW TABLES LIKE '%cliente%';
SHOW TABLES LIKE '%pessoa%';
SHOW TABLES LIKE '%endereco%';
SHOW TABLES LIKE '%municipio%';
SHOW TABLES LIKE '%estado%';

-- Verificar se há dados
SELECT COUNT(*) as total_clientes FROM clientes;
SELECT COUNT(*) as total_pessoas FROM pessoas;
SELECT COUNT(*) as total_enderecos FROM enderecos;
-- Verificar estrutura da tabela enderecos
DESCRIBE enderecos;

-- Verificar estrutura da tabela pessoas (endereços)
DESCRIBE pessoas;

-- Verificar dados de exemplo
SELECT * FROM enderecos LIMIT 3;
SELECT PES_ID, PES_NOME, PES_RAZAO_SOCIAL, PES_CPFCNPJ FROM pessoas LIMIT 3;
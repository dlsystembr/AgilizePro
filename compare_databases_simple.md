# Comparação de Bancos de Dados - MapOS vs AgilizePro

## Método 1: Usando o Script PHP (Recomendado)

1. Edite o arquivo `compare_databases.php` e ajuste as configurações:
   ```php
   $agilizepro_config = [
       'host' => 'localhost',
       'user' => 'root',
       'pass' => '',
       'db' => 'agilizepro' // Altere para o nome correto
   ];
   ```

2. Acesse via navegador:
   ```
   http://localhost/mapos/compare_databases.php
   ```

3. O script irá:
   - Comparar todas as tabelas
   - Identificar diferenças de estrutura
   - Gerar arquivos SQL automaticamente

## Método 2: Usando MySQL Workbench (Gráfico)

1. Abra o MySQL Workbench
2. Database → Synchronize with Any Source
3. Selecione os dois bancos
4. O Workbench mostrará as diferenças e gerará scripts

## Método 3: Usando mysqldump (Linha de Comando)

### Passo 1: Exportar estrutura do MapOS
```bash
mysqldump -u root -p --no-data mapos > mapos_structure.sql
```

### Passo 2: Exportar estrutura do AgilizePro
```bash
mysqldump -u root -p --no-data agilizepro > agilizepro_structure.sql
```

### Passo 3: Comparar os arquivos
```bash
# Windows (PowerShell)
Compare-Object (Get-Content mapos_structure.sql) (Get-Content agilizepro_structure.sql)

# Linux/Mac
diff mapos_structure.sql agilizepro_structure.sql
```

## Método 4: Queries SQL Diretas

### Listar todas as tabelas do MapOS que não existem no AgilizePro:
```sql
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'mapos' 
AND TABLE_NAME NOT IN (
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = 'agilizepro'
);
```

### Comparar colunas de uma tabela específica:
```sql
-- No banco MapOS
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'mapos' AND TABLE_NAME = 'nome_da_tabela';

-- No banco AgilizePro
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'agilizepro' AND TABLE_NAME = 'nome_da_tabela';
```

### Encontrar colunas que existem no MapOS mas não no AgilizePro:
```sql
SELECT m.COLUMN_NAME, m.DATA_TYPE, m.IS_NULLABLE, m.COLUMN_DEFAULT
FROM information_schema.COLUMNS m
LEFT JOIN information_schema.COLUMNS a 
    ON m.TABLE_NAME = a.TABLE_NAME 
    AND m.COLUMN_NAME = a.COLUMN_NAME
    AND a.TABLE_SCHEMA = 'agilizepro'
WHERE m.TABLE_SCHEMA = 'mapos'
AND a.COLUMN_NAME IS NULL
ORDER BY m.TABLE_NAME, m.ORDINAL_POSITION;
```

## Método 5: Usando phpMyAdmin

1. Acesse phpMyAdmin
2. Selecione o banco MapOS
3. Vá em "Estrutura" de cada tabela
4. Compare manualmente com o AgilizePro

## Recomendação

Use o **Método 1 (Script PHP)** pois ele:
- ✅ Compara automaticamente tudo
- ✅ Gera scripts SQL prontos para executar
- ✅ Mostra relatório visual completo
- ✅ Identifica diferenças de tipos, nulls, defaults, etc.

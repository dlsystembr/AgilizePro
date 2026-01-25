# Padronização de Collation - Português Brasileiro

## Collation Recomendada

Para português brasileiro, a collation recomendada é:

**`utf8mb4_unicode_ci`**

### Por quê?

1. ✅ **Suporta emojis e caracteres especiais** (4 bytes)
2. ✅ **Melhor ordenação para português** (ç, ã, õ, etc.)
3. ✅ **Compatível com padrões modernos**
4. ✅ **Suporta todos os caracteres Unicode**

## Como Padronizar

### Método 1: Script PHP (Recomendado)

1. Acesse: `http://localhost/mapos/fix_collation_utf8mb4.php`
2. O script irá:
   - Verificar collation atual do banco
   - Listar todas as tabelas e colunas
   - Gerar script SQL automaticamente
3. Faça backup do banco
4. Execute o script SQL gerado

### Método 2: SQL Manual

```sql
-- 1. Alterar collation do banco
ALTER DATABASE `mapos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Alterar collation de todas as tabelas
ALTER TABLE `nome_tabela` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 3. Para uma coluna específica
ALTER TABLE `nome_tabela` MODIFY COLUMN `nome_coluna` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Método 3: Script Automático para Todas as Tabelas

```sql
-- Gerar scripts para todas as tabelas
SELECT CONCAT('ALTER TABLE `', TABLE_NAME, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;')
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'mapos'
AND TABLE_COLLATION != 'utf8mb4_unicode_ci';
```

## Configuração do CodeIgniter

A configuração já foi atualizada em `application/config/database.php`:

```php
'char_set' => 'utf8mb4',
'dbcollat' => 'utf8mb4_unicode_ci',
```

## Comparação de Collations

| Collation | Português | Emojis | Performance | Recomendado |
|-----------|-----------|--------|-------------|-------------|
| `utf8mb4_unicode_ci` | ✅ Excelente | ✅ Sim | ⚡ Boa | ✅ **SIM** |
| `utf8mb4_unicode_520_ci` | ✅ Excelente | ✅ Sim | ⚡ Boa | ✅ Se disponível |
| `utf8mb4_general_ci` | ⚠️ Razoável | ✅ Sim | ⚡⚡ Muito rápida | ⚠️ Não ideal |
| `utf8_general_ci` | ⚠️ Razoável | ❌ Não | ⚡⚡ Muito rápida | ❌ Não usar |
| `latin1_swedish_ci` | ❌ Ruim | ❌ Não | ⚡⚡⚡ Muito rápida | ❌ Não usar |

## Verificar Collation Atual

```sql
-- Ver collation do banco
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
FROM information_schema.SCHEMATA 
WHERE SCHEMA_NAME = 'mapos';

-- Ver collation de todas as tabelas
SELECT TABLE_NAME, TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'mapos'
ORDER BY TABLE_NAME;

-- Ver collation de todas as colunas
SELECT TABLE_NAME, COLUMN_NAME, CHARACTER_SET_NAME, COLLATION_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'mapos'
AND CHARACTER_SET_NAME IS NOT NULL
ORDER BY TABLE_NAME, ORDINAL_POSITION;
```

## Importante

⚠️ **SEMPRE faça backup antes de alterar collations!**

```bash
mysqldump -u root -p mapos > backup_antes_collation.sql
```

## Referências

- [MySQL Collation Chart](https://dev.mysql.com/doc/refman/8.0/en/charset-collation-names.html)
- [UTF-8 vs UTF8MB4](https://dev.mysql.com/doc/refman/8.0/en/charset-unicode-utf8mb4.html)

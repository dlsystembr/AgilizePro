# 📋 Guia de Padronização de Colunas para Minúsculas

Este guia explica como padronizar todas as colunas do banco de dados e código para minúsculas.

## 🎯 Objetivo

Padronizar todos os nomes de colunas do banco de dados e suas referências no código PHP para minúsculas.

## 📝 Processo Passo a Passo

### 1. Análise Inicial

Execute o script de análise para identificar todas as colunas que precisam ser renomeadas:

```
http://localhost/mapos/analyze_column_names.php
```

**O que este script faz:**
- Analisa todas as tabelas e colunas do banco
- Identifica colunas em MAIÚSCULAS, minúsculas ou mistas
- Gera script SQL para renomear colunas
- Cria arquivo JSON com mapeamento de colunas antigas → novas

**Arquivos gerados:**
- `rename_columns_lowercase_YYYY-MM-DD_HHMMSS.sql` - Script SQL para renomear
- `column_rename_mapping_YYYY-MM-DD_HHMMSS.json` - Mapeamento JSON

### 2. Buscar Referências no Código

Execute o script para encontrar todas as referências às colunas no código:

```
http://localhost/mapos/find_column_references.php
```

**O que este script faz:**
- Busca todas as referências às colunas antigas no código PHP
- Lista arquivos e linhas onde as colunas são usadas
- Gera relatório detalhado

### 3. Fazer Backup

⚠️ **IMPORTANTE:** Antes de continuar, faça backup de:
- Banco de dados completo
- Código fonte completo (git commit ou cópia)

### 4. Renomear Colunas no Banco

Execute o script SQL gerado no passo 1:

```sql
-- Via phpMyAdmin ou linha de comando
mysql -u root -p mapos < rename_columns_lowercase_YYYY-MM-DD_HHMMSS.sql
```

**Ou execute no phpMyAdmin:**
1. Abra o phpMyAdmin
2. Selecione o banco `mapos`
3. Vá em "SQL"
4. Cole o conteúdo do arquivo SQL
5. Execute

### 5. Atualizar Código

Execute o script de substituição:

**Via linha de comando (recomendado):**
```bash
cd c:\xampp\htdocs\mapos
php replace_column_names.php
```

**Via navegador (apenas visualização):**
```
http://localhost/mapos/replace_column_names.php
```

⚠️ **Nota:** O modo navegador apenas mostra o que seria feito. Use a linha de comando para realmente fazer as substituições.

### 6. Verificação

Após executar os scripts:

1. **Teste a aplicação:**
   - Acesse todas as funcionalidades principais
   - Verifique se não há erros de SQL
   - Confirme que os dados estão sendo salvos/carregados corretamente

2. **Verifique logs:**
   - Procure por erros no log do PHP
   - Verifique erros no log do MySQL

3. **Execute testes:**
   - Teste CRUD em todas as tabelas principais
   - Verifique relatórios e listagens

## 🔍 Padrões de Busca e Substituição

O script busca e substitui os seguintes padrões:

- `` `COLUNA` `` → `` `coluna` ``
- `'COLUNA'` → `'coluna'`
- `"COLUNA"` → `"coluna"`
- `->COLUNA` → `->coluna`
- `['COLUNA']` → `['coluna']`
- `["COLUNA"]` → `["coluna"]`

## ⚠️ Cuidados Importantes

1. **Backup obrigatório:** Sempre faça backup antes de executar scripts de modificação
2. **Teste em ambiente de desenvolvimento primeiro:** Nunca execute direto em produção
3. **Verifique substituições:** Algumas substituições podem ser ambíguas (ex: `COLUNA` dentro de uma string)
4. **Revisão manual:** Após a substituição automática, revise arquivos críticos manualmente
5. **Chaves estrangeiras:** Verifique se há constraints que referenciam as colunas renomeadas

## 📊 Exemplo de Transformação

### Antes:
```sql
CREATE TABLE produtos (
    PRO_ID INT PRIMARY KEY,
    PRO_NOME VARCHAR(100),
    PRO_PRECO DECIMAL(10,2)
);
```

```php
$produto = $this->db->get_where('produtos', ['PRO_ID' => $id])->row();
echo $produto->PRO_NOME;
```

### Depois:
```sql
CREATE TABLE produtos (
    pro_id INT PRIMARY KEY,
    pro_nome VARCHAR(100),
    pro_preco DECIMAL(10,2)
);
```

```php
$produto = $this->db->get_where('produtos', ['pro_id' => $id])->row();
echo $produto->pro_nome;
```

## 🐛 Troubleshooting

### Erro: "Column doesn't exist"
- Verifique se o script SQL foi executado completamente
- Confirme que todas as colunas foram renomeadas

### Erro: "Unknown column in field list"
- Verifique se o código foi atualizado após renomear as colunas
- Execute novamente o script de substituição

### Substituições incorretas
- Revise o arquivo JSON de mapeamento
- Verifique se há colunas com nomes muito genéricos
- Faça substituições manuais se necessário

## 📁 Arquivos do Processo

- `analyze_column_names.php` - Análise inicial
- `find_column_references.php` - Busca de referências
- `replace_column_names.php` - Substituição automática
- `rename_columns_lowercase_*.sql` - Scripts SQL gerados
- `column_rename_mapping_*.json` - Mapeamentos JSON

## ✅ Checklist Final

- [ ] Backup do banco de dados feito
- [ ] Backup do código feito
- [ ] Análise executada e revisada
- [ ] Referências no código identificadas
- [ ] Script SQL executado no banco
- [ ] Código atualizado
- [ ] Aplicação testada
- [ ] Logs verificados
- [ ] Funcionalidades principais testadas

## 📞 Suporte

Se encontrar problemas durante o processo:
1. Verifique os logs de erro
2. Revise os arquivos gerados
3. Teste em ambiente de desenvolvimento
4. Faça rollback se necessário (use os backups)

---

**Última atualização:** 2026-01-22

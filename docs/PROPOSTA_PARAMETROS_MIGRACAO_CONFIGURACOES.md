# Proposta: Parâmetros do Sistema (Migração de Configurações)

## 1. Objetivo

Substituir a tabela **configuracoes** (chave/valor simples) por uma tabela **parametros** com:
- Valor em uma coluna **prm_valor** (TEXT); tipo indicado por **prm_tipo_dado** (string, integer, float, boolean, datetime, text/json) para conversão na aplicação
- Caption e descrição para a tela de configuração
- Controle de visibilidade e agrupamento (módulo/grupo)
- Escopo por **empresa** (emp_id)
- Convenção de nomes do projeto: minúsculo, **prefixo de 3 letras** (prm_) nas colunas

---

## 2. Estrutura de referência (sistema externo)

Você trouxe um modelo com colunas como:

| Campo ref.     | Uso |
|----------------|-----|
| PRMT_ID        | PK |
| PRMT_NOME      | Código/chave do parâmetro |
| PRMT_CAPTION   | Rótulo para a interface |
| PRMT_TIPO_DADO | Tipo: Boolean, String, DateTime, etc. |
| PRMT_DESCRICAO | Texto explicativo |
| PRMT_STRING / PRMT_INTEGER / PRMT_FLOAT / PRMT_DATE_TIME / PRMT_BOOLEAN | Valor armazenado conforme o tipo |
| PRMT_DADO_FORMATADO | Valor formatado para exibição (ex.: "02/01/2023") |
| PRMT_VISIVEL   | Se aparece na tela de parâmetros |
| SSTM_ID        | Agrupador (sistema/módulo) |
| PRMT_LASTUPDATE| Data da última alteração |

**O que aproveitamos no MapOS:** tipagem por coluna, caption, descrição, visível, agrupamento (módulo), last update. **O que adaptamos:** nomes em minúsculo, **prefixo de 3 letras** (`prm_`), e **emp_id** (empresa) em vez de tenant.

---

## 3. Proposta: tabela `parametros` (diretriz do projeto)

Convenções do projeto:
- Nomes de tabela e colunas em **minúsculo**.
- **Prefixo de colunas com 3 letras:** `prm_` (parametros).
- **emp_id** em tabelas de negócio (FK para `empresas`); não usar ten_id.
- PK numérica: **prm_id**.

### 3.1 DDL sugerida

Valor armazenado em **uma única coluna** `prm_valor` (TEXT). O campo `prm_tipo_dado` indica como a aplicação deve interpretar/converter (string, integer, float, boolean, datetime, text/json) na leitura e na gravação.

```sql
CREATE TABLE `parametros` (
  `prm_id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emp_id`            INT(11) UNSIGNED NOT NULL COMMENT 'FK empresas',
  `prm_nome`          VARCHAR(80)  NOT NULL COMMENT 'Código único do parâmetro (ex: app_name, per_page)',
  `prm_caption`       VARCHAR(120) NULL COMMENT 'Rótulo para a tela de configuração',
  `prm_tipo_dado`     VARCHAR(20)  NOT NULL DEFAULT 'string' COMMENT 'string|integer|float|boolean|datetime|text|json',
  `prm_descricao`     VARCHAR(255) NULL COMMENT 'Descrição/ajuda',
  `prm_valor`         TEXT         NULL COMMENT 'Valor em texto; conversão conforme prm_tipo_dado na aplicação',
  `prm_dado_formatado` VARCHAR(255) NULL COMMENT 'Valor formatado para exibição (opcional, ex: data dd/mm/yyyy)',
  `prm_visivel`       TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1=exibir na tela de parâmetros',
  `prm_grupo`         VARCHAR(50)  NULL COMMENT 'Agrupador: geral, os, fiscal, notificacoes, nfe, etc.',
  `prm_ordem`         INT(11)      NULL DEFAULT 0 COMMENT 'Ordem de exibição no grupo',
  `prm_data_atualizacao` DATETIME     NULL COMMENT 'Data de alteração',
  PRIMARY KEY (`prm_id`),
  UNIQUE KEY `uk_parametros_empresa_nome` (`emp_id`, `prm_nome`),
  KEY `idx_parametros_emp_id` (`emp_id`),
  KEY `idx_parametros_grupo` (`prm_grupo`),
  CONSTRAINT `fk_parametros_empresa` FOREIGN KEY (`emp_id`) REFERENCES `empresas` (`emp_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Parâmetros do sistema por empresa (substitui configuracoes)';
```

### 3.2 Regra de valor

- **Uma linha = um parâmetro.** O valor é sempre armazenado em `prm_valor` (TEXT), em formato string.
- Na **leitura**, a aplicação converte conforme `prm_tipo_dado`: string (direto), integer (intval), float (floatval), boolean (1/0 ou true/false), datetime (strtotime ou formato ISO), text/json (direto).
- Na **gravação**, a aplicação serializa o valor para string antes de salvar em `prm_valor`.
- `prm_dado_formatado` é opcional (ex.: data em "dd/mm/yyyy" apenas para exibir na tela).

---

## 4. Mapeamento: configurações atuais → parametros

Com base nos campos usados em `Mapos::salvarConfiguracoes` e na view `configurar.php`:

| prm_nome (ex-config) | prm_tipo_dado | prm_caption (sugestão) | prm_grupo |
|------------------------|----------------|--------------------------|------------|
| app_name               | string         | Nome do sistema          | geral      |
| app_theme              | string         | Tema                     | geral      |
| per_page               | integer        | Itens por página         | geral      |
| control_datatable      | boolean        | Usar DataTables          | geral      |
| control_estoque        | boolean        | Controle de estoque      | geral      |
| control_baixa          | boolean        | Baixa automática         | geral      |
| control_editos         | boolean        | Editar OS                | os         |
| control_edit_vendas    | boolean        | Editar vendas            | geral      |
| control_2vias          | boolean        | Impressão 2 vias OS      | os         |
| os_notification        | string         | Notificação OS           | notificacoes |
| email_automatico       | boolean        | E-mail automático        | notificacoes |
| notifica_whats         | text           | Mensagem WhatsApp        | notificacoes |
| os_status_list         | json           | Lista status OS          | os         |
| pix_key                | string         | Chave PIX                | geral      |
| regime_tributario      | string         | Regime tributário        | fiscal     |
| mensagem_simples_nacional | text       | Mensagem Simples Nacional| fiscal     |
| aliq_cred_icms         | string/float   | Alíq. crédito ICMS       | fiscal     |
| tributacao_produto    | boolean        | Tributação por produto   | fiscal     |

Outros parâmetros (NFe, NFCom, emitente, etc.) podem ser incluídos na mesma tabela com `prm_grupo` apropriado.

---

## 5. O que não replicar do modelo de referência

- **SSTM_ID** como FK para outra tabela de “sistemas”: no projeto não existe essa entidade; usamos **prm_grupo** (VARCHAR) para agrupar na tela (geral, os, fiscal, notificacoes, nfe, nfcom).
- **PRMT_ID** no formato longo (ex.: 00000000040000000001): no projeto usamos PK inteira simples (**prm_id**).
- Múltiplos “sistemas” (SSTM): um único conjunto de parâmetros por empresa, diferenciado só por **prm_grupo**.

---

## 6. Vantagens em relação à tabela atual

| Aspecto | configuracoes (atual) | parametros (proposta) |
|---------|------------------------|------------------------|
| Tipo do valor | Tudo em `valor` (string) | Coluna por tipo (string, int, float, boolean, datetime, text/json) |
| Documentação | Sem caption/descrição | prm_caption, prm_descricao |
| Tela de config | Lista fixa no código | Pode listar por prm_grupo, prm_ordem, prm_visivel |
| Escopo | ten_id/emp_id às vezes ignorado | UNIQUE(emp_id, prm_nome), sempre por empresa |
| Auditoria | Sem data alteração | prm_data_atualizacao |
| Novos parâmetros | Alterar controller/view | INSERT na tabela + uso do grupo; tela pode ser genérica |

---

## 7. Passos sugeridos para a migração

1. **Criar a tabela**  
   - Rodar o DDL da seção 3.1 (ou virar migration em `application/database/migrations/`).

2. **Seed inicial**  
   - Inserir em `parametros` uma linha por chave atual de `configuracoes` (por emp_id, ex.: primeira empresa de cada grupo ou emp_id da sessão), com:
     - `prm_nome` = chave (ex.: app_name, per_page)
     - `prm_tipo_dado` e `prm_valor` preenchidos conforme a tabela da seção 4
     - `prm_caption`, `prm_grupo`, `prm_visivel` = 1

3. **Migrar dados**  
   - Script (ou migration) que:
     - Lê cada linha de `configuracoes` (por ten_id ou por contexto atual)
     - Mapeia ten_id → emp_id (ex.: primeira empresa do tenant/grupo)
     - Localiza o parâmetro em `parametros` por (emp_id, prm_nome)
     - Atualiza `prm_valor` (valor em string)

4. **Código**  
   - **Parametros_model**: métodos para get/set por (emp_id, prm_nome), lendo/gravando em `prm_valor` e convertendo conforme `prm_tipo_dado`.
   - **MY_Controller::load_configuration()**: passar a ler de `parametros` (por emp_id da sessão) e montar o array `configuration` no mesmo formato atual (chave => valor), para não quebrar as views.
   - **Mapos::salvarConfiguracoes** e **Mapos_model::saveConfiguracao**: passar a atualizar `parametros` em vez de `configuracoes`.

5. **Depois de validar**  
   - Desativar ou remover o uso de `configuracoes`; opcionalmente renomear/arquivar a tabela.

---

## 8. Resumo

- **Estrutura:** tabela **parametros** com **prefixo de colunas em 3 letras** (`prm_`): prm_id, emp_id, prm_nome, prm_caption, prm_tipo_dado, prm_descricao, colunas de valor tipadas (prm_string, prm_integer, prm_float, prm_date_time, prm_boolean, prm_text), prm_dado_formatado, prm_visivel, prm_grupo, prm_ordem, prm_data_atualizacao.
- **Diretriz:** convenção do projeto: minúsculo, prefixo **prm_** (3 letras), **emp_id** (FK empresas), sem ten_id.
- **Uso:** um valor por parâmetro, na coluna do tipo; tela de configuração pode ser gerada por grupo/ordem/visível; migração em etapas (criar tabela → seed → migrar dados → trocar código → desligar configuracoes).

Próximo passo (após validar esta estrutura): (1) migration PHP para criar `parametros`, (2) seed com os nomes/caption/grupo/tipo da seção 4, (3) esboço do `Parametros_model` (get/set por emp_id e nome/tipo).

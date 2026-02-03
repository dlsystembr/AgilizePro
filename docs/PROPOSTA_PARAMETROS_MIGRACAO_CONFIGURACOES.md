# Proposta: Parâmetros do Sistema (Migração de Configurações)

## 1. Objetivo

Substituir a tabela **configuracoes** (chave/valor simples) por uma tabela **parametros** com:
- Tipagem explícita (string, integer, float, boolean, datetime, text/json)
- Caption e descrição para a tela de configuração
- Controle de visibilidade e agrupamento (módulo/grupo)
- Multi-tenant (ten_id)
- Convenção de nomes do AgilizePro (minúsculo, prefixo da tabela)

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

**O que aproveitamos no MapOS:** tipagem por coluna, caption, descrição, visível, agrupamento (módulo), last update. **O que adaptamos:** nomes em minúsculo, prefixo `prmt_`, e **ten_id** para multi-tenant.

---

## 3. Proposta: tabela `parametros` (diretriz AgilizePro)

Convenções do projeto:
- Nomes de tabela e colunas em **minúsculo**.
- Prefixo da tabela nas colunas: **prmt_**.
- **ten_id** em tabelas de negócio (FK para `tenants`).
- PK numérica: **prmt_id**.

### 3.1 DDL sugerida

```sql
CREATE TABLE `parametros` (
  `prmt_id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten_id`            INT(11) UNSIGNED NOT NULL COMMENT 'FK tenants',
  `prmt_nome`         VARCHAR(80)  NOT NULL COMMENT 'Código único do parâmetro (ex: app_name, per_page)',
  `prmt_caption`      VARCHAR(120) NULL COMMENT 'Rótulo para a tela de configuração',
  `prmt_tipo_dado`    VARCHAR(20)  NOT NULL DEFAULT 'string' COMMENT 'string|integer|float|boolean|datetime|text|json',
  `prmt_descricao`    VARCHAR(255) NULL COMMENT 'Descrição/ajuda',
  `prmt_string`       VARCHAR(255) NULL,
  `prmt_integer`      INT(11)      NULL,
  `prmt_float`        DECIMAL(18,6) NULL,
  `prmt_date_time`    DATETIME     NULL,
  `prmt_boolean`      TINYINT(1)   NULL COMMENT '0 ou 1',
  `prmt_text`         TEXT         NULL COMMENT 'Texto longo ou JSON em string',
  `prmt_dado_formatado` VARCHAR(255) NULL COMMENT 'Valor formatado para exibição (opcional)',
  `prmt_visivel`      TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1=exibir na tela de parâmetros',
  `prmt_grupo`        VARCHAR(50)  NULL COMMENT 'Agrupador: geral, os, fiscal, notificacoes, nfe, etc.',
  `prmt_ordem`        INT(11)      NULL DEFAULT 0 COMMENT 'Ordem de exibição no grupo',
  `prmt_last_update`  DATETIME     NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`prmt_id`),
  UNIQUE KEY `uk_parametros_tenant_nome` (`ten_id`, `prmt_nome`),
  KEY `idx_parametros_ten_id` (`ten_id`),
  KEY `idx_parametros_grupo` (`prmt_grupo`),
  CONSTRAINT `fk_parametros_ten_id` FOREIGN KEY (`ten_id`) REFERENCES `tenants` (`ten_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Parâmetros do sistema por tenant (substitui configuracoes)';
```

### 3.2 Regra de valor

- **Uma linha = um parâmetro.** O valor fica na coluna correspondente ao `prmt_tipo_dado`:
  - `string`   → `prmt_string`
  - `integer`  → `prmt_integer`
  - `float`    → `prmt_float`
  - `boolean`  → `prmt_boolean`
  - `datetime` → `prmt_date_time`
  - `text` / `json` → `prmt_text`
- As demais colunas de valor ficam NULL. Na leitura/gravação o código usa só a coluna do tipo.
- `prmt_dado_formatado` é opcional (ex.: data em "dd/mm/yyyy" para exibir na tela).

---

## 4. Mapeamento: configurações atuais → parametros

Com base nos campos usados em `Mapos::salvarConfiguracoes` e na view `configurar.php`:

| prmt_nome (ex-config) | prmt_tipo_dado | prmt_caption (sugestão) | prmt_grupo |
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

Outros parâmetros (NFe, NFCom, emitente, etc.) podem ser incluídos na mesma tabela com `prmt_grupo` apropriado.

---

## 5. O que não replicar do modelo de referência

- **SSTM_ID** como FK para outra tabela de “sistemas”: no AgilizePro não existe essa entidade; usamos **prmt_grupo** (VARCHAR) para agrupar na tela (geral, os, fiscal, notificacoes, nfe, nfcom).
- **PRMT_ID** no formato longo (ex.: 00000000040000000001): no AgilizePro usamos PK inteira simples (**prmt_id**).
- Múltiplos “sistemas” (SSTM): um único conjunto de parâmetros por tenant, diferenciado só por **prmt_grupo**.

---

## 6. Vantagens em relação à tabela atual

| Aspecto | configuracoes (atual) | parametros (proposta) |
|---------|------------------------|------------------------|
| Tipo do valor | Tudo em `valor` (string) | Coluna por tipo (string, int, float, boolean, datetime, text/json) |
| Documentação | Sem caption/descrição | prmt_caption, prmt_descricao |
| Tela de config | Lista fixa no código | Pode listar por prmt_grupo, prmt_ordem, prmt_visivel |
| Multi-tenant | ten_id às vezes ignorado | UNIQUE(ten_id, prmt_nome), sempre por tenant |
| Auditoria | Sem last update | prmt_last_update |
| Novos parâmetros | Alterar controller/view | INSERT na tabela + uso do grupo; tela pode ser genérica |

---

## 7. Passos sugeridos para a migração

1. **Criar a tabela**  
   - Rodar o DDL da seção 3.1 (ou virar migration em `application/database/migrations/`).

2. **Seed inicial**  
   - Inserir em `parametros` uma linha por chave atual de `configuracoes` (para ten_id = 1 ou para cada tenant), com:
     - `prmt_nome` = chave (ex.: app_name, per_page)
     - `prmt_tipo_dado` e coluna de valor preenchida conforme a tabela da seção 4
     - `prmt_caption`, `prmt_grupo`, `prmt_visivel` = 1

3. **Migrar dados**  
   - Script (ou migration) que:
     - Lê cada linha de `configuracoes` (por ten_id)
     - Localiza o parâmetro em `parametros` por (ten_id, prmt_nome)
     - Atualiza a coluna de valor correspondente ao `prmt_tipo_dado`

4. **Código**  
   - **Parametros_model**: métodos para get/set por (ten_id, prmt_nome), lendo/gravando na coluna certa conforme `prmt_tipo_dado`.
   - **MY_Controller::load_configuration()**: passar a ler de `parametros` (por ten_id) e montar o array `configuration` no mesmo formato atual (chave => valor), para não quebrar as views.
   - **Mapos::salvarConfiguracoes** e **Mapos_model::saveConfiguracao**: passar a atualizar `parametros` em vez de `configuracoes`.

5. **Depois de validar**  
   - Desativar ou remover o uso de `configuracoes`; opcionalmente renomear/arquivar a tabela.

---

## 8. Resumo

- **Estrutura:** tabela **parametros** com prmt_id, ten_id, prmt_nome, prmt_caption, prmt_tipo_dado, prmt_descricao, colunas de valor tipadas (prmt_string, prmt_integer, prmt_float, prmt_date_time, prmt_boolean, prmt_text), prmt_dado_formatado, prmt_visivel, prmt_grupo, prmt_ordem, prmt_last_update.
- **Diretriz:** mesma convenção do projeto (minúsculo, prmt_, ten_id, FK tenants).
- **Uso:** um valor por parâmetro, na coluna do tipo; tela de configuração pode ser gerada por grupo/ordem/visível; migração em etapas (criar tabela → seed → migrar dados → trocar código → desligar configuracoes).

Se quiser, o próximo passo pode ser: (1) migration PHP para criar `parametros`, (2) seed com os nomes/caption/grupo/tipo da seção 4, (3) esboço do `Parametros_model` (get/set por nome e tipo).

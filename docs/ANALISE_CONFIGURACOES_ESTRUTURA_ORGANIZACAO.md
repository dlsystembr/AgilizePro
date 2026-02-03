# Análise: Configurações do Sistema, Estrutura de Tabelas e Organização

## 1. Estrutura atual

### 1.1 Organização do projeto (MVC)

| Camada | Caminho | Observação |
|--------|---------|------------|
| **Config** | `application/config/` | config.php, database.php, routes.php, permission.php, etc. |
| **Controllers** | `application/controllers/` | Um controller por módulo (Pessoas, Mapos, Nfecom, etc.) + API em `api/v1/` |
| **Models** | `application/models/` | Um model por entidade; vários usam `ten_id` para multi-tenant |
| **Views** | `application/views/` | Uma pasta por módulo (pessoas/, nfecom/, tema/, etc.) |
| **Core** | `application/core/MY_Controller.php` | Controller base: login, carrega configurações do sistema |
| **Database** | `application/database/migrations/` | Migrations CodeIgniter; seeds em `seeds/` |
| **SQL** | `sql/` (raiz) e `application/sql/` | Scripts manuais (add_pes_regime_tributario, add_pes_id_end_id_nfecom_capa, etc.) |

A estrutura segue o padrão **CodeIgniter 3** (MVC) e está coerente. O sistema é denominado **AgilizePro**. Há duplicação de fontes de verdade: migrations oficiais em `database/migrations/` e vários scripts SQL soltos na raiz e em `sql/`, o que pode gerar divergência de schema.

---

### 1.2 Multi-tenant (ten_id)

- **Tabela `tenants`**: criada pela migration `20260121000001_add_tenants_and_fk.php` (ten_id, ten_nome, ten_cnpj, ten_email, ten_telefone, ten_data_cadastro).
- **Tenant padrão**: “Matriz” (ten_id = 1).
- **Demais tabelas**: a mesma migration adiciona `ten_id` em **todas** as tabelas exceto `tenants`, `migrations`, `ci_sessions`, com FK para `tenants.ten_id`.
- **Super usuário**: `MY_Controller` verifica `is_super`; se for super, não carrega configurações do banco (evita dependência de ten_id na sessão).
- **Models**: Pessoas_model, Tipos_clientes_model, ClassificacaoFiscal_model, Relatorios_model, Mapos (parcialmente), etc., filtram por `ten_id` da sessão.

Isso está **correto** para multi-tenant: dados isolados por organização (tenant).

---

### 1.3 Tabela de configurações do sistema (`configuracoes`)

- **Origem**: migration `20121031100537_create_base.php` – tabela com `idConfig`, `config` (VARCHAR chave), `valor` (VARCHAR/TEXT).
- **Constraint original**: `UNIQUE(config)` – uma linha por chave (modelo global, não por tenant).
- **Alterações posteriores**:
  - Migration de tenants adiciona `ten_id` em todas as tabelas, **incluindo** `configuracoes`.
  - Scripts de rename (ex.: rename_columns_lowercase_*.sql) alteram `idConfig` para `idconfig` (minúsculo).
  - Outras migrations alteram tamanho/collation de `config` e `valor` (VARCHAR/TEXT).

Com `ten_id` na tabela, o desenho esperado é **uma configuração por (ten_id, config)**. A constraint `UNIQUE(config)` passa a conflitar com multi-tenant; o ideal é `UNIQUE(ten_id, config)` (ou remover UNIQUE em `config` e usar índice único composto).

---

### 1.4 Carregamento das configurações (MY_Controller)

- **Método**: `load_configuration()` em `MY_Controller`.
- **Comportamento**: `$this->CI->db->get('configuracoes')` **sem filtrar por ten_id**.
- **Efeito**: em ambiente multi-tenant, todas as linhas de todos os tenants são lidas; o merge por chave `config` faz com que o “último” valor prevaleça. Ou seja, **o tenant da sessão não é respeitado** na tela – todos veem a mesma configuração (a de quem “escreveu por último” no merge).

**Conclusão**: está **incorreto** para multi-tenant. O carregamento deveria filtrar por `ten_id` da sessão (exceto para super, se desejado).

---

### 1.5 Salvamento das configurações (Mapos_model::saveConfiguracao)

- **Método**: `saveConfiguracao($data)`.
- **Comportamento**:
  - Verifica existência por `config` apenas: `$this->db->where('config', $key)`.
  - Atualiza ou insere **sem** usar `ten_id` (nem no WHERE nem no INSERT).
- **Efeito**: em multi-tenant, todas as organizações alteram o **mesmo** conjunto de linhas (ou inserem sem ten_id, dependendo do default da coluna). Não há isolamento por tenant.

**Conclusão**: está **incorreto** para multi-tenant. O salvamento deveria:
- Filtrar por `ten_id` da sessão ao buscar/atualizar.
- Incluir `ten_id` em novos INSERTs.

---

### 1.6 Leitura no Mapos_model (getConfiguracao / initConfiguracoes)

- **getConfiguracao()**: já filtra por `ten_id` da sessão ao ler – **correto**.
- **initConfiguracoes()**: insere configurações padrão **sem** `ten_id` e verifica existência só por `config` – mesmo problema do save: não é por tenant.

---

### 1.7 Nome da coluna PK da tabela configuracoes (idConfig vs idconfig)

- Migrations antigas e parte do código usam `idConfig`.
- Scripts de rename (lowercase) alteram para `idconfig`.
- No `Mapos_model::saveConfiguracao()`, ao tratar registro “legado”, usa-se `$legacy->idConfig`. Em bancos onde a coluna foi renomeada para `idconfig`, o objeto retornado pelo driver pode ter apenas `idconfig`, gerando **Undefined property: stdClass::$idConfig** (como visto em log).

**Conclusão**: o código deve aceitar **ambos** os nomes (idConfig e idconfig) ao acessar a PK do registro legado, ou usar o nome real da coluna no banco.

---

### 1.8 Permissões

- Já existe o documento **docs/ANALISE_PERMISSOES_E_PROPOSTA_RBAC.md**, que descreve:
  - Uso de blob serializado em `permissoes` e tabela `tenant_permissoes_menu`.
  - Dois fluxos (com tenant vs sem tenant).
  - Proposta RBAC (catálogo de permissões, roles, role_permissoes).

Nada a alterar nesta análise; a evolução desejada está naquele documento.

---

### 1.9 Banco de dados e configuração

- **database.php**: usa variáveis de ambiente (DB_HOSTNAME, DB_DATABASE, etc.); um único banco.
- **config.php**: base_url, app_version, charset, etc.; uso de ENV onde faz sentido.
- **Migrações**: muitas migrations em `application/database/migrations/`; ordem por timestamp. Há muitas migrations e alguns scripts SQL manuais fora do fluxo de migrations (risco de schema divergente).

---

## 2. Resumo do que está certo

- Estrutura MVC e organização de pastas (config, controllers, models, views, core).
- Uso de `ten_id` e tabela `tenants` para multi-tenant nas tabelas de negócio.
- Filtro por `ten_id` nos models de dados (Pessoas, Tipos_clientes, ClassificacaoFiscal, Relatorios, etc.).
- `getConfiguracao()` no Mapos_model filtrando por `ten_id`.
- Super usuário sem depender de configuração por tenant no carregamento inicial.

---

## 3. O que melhorar

### 3.1 Configurações do sistema (configuracoes)

| Item | Ação sugerida |
|------|----------------|
| **MY_Controller::load_configuration()** | Filtrar por `ten_id` da sessão ao carregar (ex.: `$this->db->where('ten_id', $this->session->userdata('ten_id'))` antes do `get('configuracoes')`). Se a tabela ainda não tiver `ten_id`, adicionar por migration e popular para o tenant padrão. |
| **Mapos_model::saveConfiguracao()** | Incluir `ten_id` da sessão em todos os WHERE (existência/atualização) e em todos os INSERT. Garantir isolamento por tenant. |
| **Mapos_model::initConfiguracoes()** | Incluir `ten_id` ao verificar existência e ao inserir. |
| **Constraint UNIQUE** | Se a tabela tiver `ten_id`, trocar `UNIQUE(config)` por `UNIQUE(ten_id, config)` (via migration). |
| **PK idConfig / idconfig** | No código que lê o id do registro (ex.: legado em saveConfiguracao), usar algo como `isset($row->idConfig) ? $row->idConfig : $row->idconfig` ou normalizar o nome da coluna no banco e no código. |

### 3.2 Schema e migrations

| Item | Ação sugerida |
|------|----------------|
| **Scripts SQL soltos** | Sempre que possível, converter alterações em migrations (em `application/database/migrations/`) com timestamp, para manter um único histórico e facilitar deploy. |
| **Documentação do schema** | Manter um único lugar (ex.: um README em `docs/` ou comentários no primeiro migration “base”) listando tabelas principais e relação com tenant. |

### 3.3 Permissões

- Seguir a proposta do **docs/ANALISE_PERMISSOES_E_PROPOSTA_RBAC.md** quando for evoluir o módulo de permissões (RBAC, catálogo, roles).

---

## 4. Conclusão

- **Organização geral**: adequada (MVC, multi-tenant com ten_id).
- **Configurações do sistema**: tabela e conceito corretos, mas **carregamento e salvamento não respeitam tenant** (e há risco de erro por nome da coluna idConfig/idconfig). Ajustando load_configuration, saveConfiguracao e initConfiguracoes para usar `ten_id` e tratando o nome da PK, o comportamento fica alinhado ao restante do sistema.
- **Schema**: consolidar alterações em migrations e reduzir scripts SQL soltos evita divergência e facilita manutenção.

As mudanças sugeridas são compatíveis com o que já existe e preparam o sistema para múltiplos tenants com configurações independentes por organização.

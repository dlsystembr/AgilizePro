# Análise do sistema de permissões e proposta de melhoria (RBAC)

## 1. Como está hoje

### 1.1 Tabelas envolvidas

| Tabela | Função |
|--------|--------|
| **permissoes** | Perfis de acesso. Cada linha = um “perfil” (ex: Administrador, Vendedor). Coluna `permissoes` = **TEXT com array PHP serializado** (ex: `a:92:{s:8:"aCliente";s:1:"1";...}`). |
| **usuarios** | Campo `permissoes_id` → FK para `permissoes.idPermissao`. Um usuário tem **um único perfil**. |
| **tenant_permissoes_menu** | Por tenant: quais **códigos** de permissão estão habilitados (ex: `vCliente`, `aNfecom`). Usado para exibir/ocultar itens de menu. |

### 1.2 Fluxo na biblioteca `Permission.php`

1. **Super usuário** (`is_super`) → sempre liberado.
2. **Usuário com tenant** (`ten_id`):
   - Só consulta **tenant_permissoes_menu** (tenant + código da permissão + ativo).
   - **Não usa** o perfil do usuário (`permissoes_id` / tabela `permissoes`).
   - Resultado: todos os usuários do mesmo tenant acabam com o mesmo “acesso” do ponto de vista do menu.
3. **Usuário sem tenant**:
   - Carrega a linha de `permissoes` pelo `idPermissao` da sessão.
   - Deserializa o blob `permissoes` e verifica se a chave da ação (ex: `vCliente`) existe e é `1`.

### 1.3 Onde as permissões são definidas

- **Config**: `application/config/permission.php` – lista código → label (ex: `vCliente` => "Visualizar Cliente").
- **Controller Permissoes**: em adicionar/editar, um array fixo com dezenas de chaves (aCliente, eCliente, vPessoa, …) montado do POST e **serializado** para gravar em `permissoes.permissoes`.
- **Views**: formulário de permissões com muitos checkboxes; menu e controllers usam `checkPermission($permissao, 'código')`.

---

## 2. Pontos fracos (o que é ruim)

### 2.1 Blob serializado

- **Não é queryável**: não dá para fazer “listar perfis que podem vNfecom” ou “dar vNfecom a todos os perfis de um tenant” em SQL.
- **Alteração frágil**: qualquer nova permissão exige:
  - alterar `permission.php`,
  - alterar arrays no controller Permissoes (adicionar/editar),
  - alterar view (novo checkbox),
  - e, em muitos casos, scripts SQL com strings serializadas gigantes (ex: `fix_admin_permissions.sql`).
- **Risco de inconsistência**: se alguém esquecer um desses pontos, o sistema fica incoerente (ex: código novo no config, mas não no formulário).

### 2.2 Duas regras diferentes (tenant x não-tenant)

- **Com tenant**: só `tenant_permissoes_menu` importa; o perfil do usuário (`permissoes_id`) **não** é usado na checagem.
- **Sem tenant**: só o perfil (blob em `permissoes`) é usado.
- Consequência: difícil explicar “quem pode o quê” e difícil garantir que, no futuro, “por tenant” e “por perfil” funcionem juntos de forma clara.

### 2.3 Sem modelo “permissões” como entidade

- Não existe tabela **permissão** (uma linha por ação: vCliente, aNfecom, etc.).
- Tudo é “código mágico” em config + no código. Adicionar módulo novo = tocar em vários arquivos e em blob.

### 2.4 Perfil = cópia de 90+ chaves

- Cada perfil é um array completo; não há “papel” + “lista de permissões”.
- Duplicar um perfil ou “dar mais uma permissão a todos os vendedores” exige atualizar muitos registros e blobs.

### 2.5 Manutenção e auditoria

- Difícil saber “quem tem permissão X” sem deserializar todos os perfis.
- Scripts de correção (ex: `fix_admin_permissions.sql`) mostram que o modelo atual incentiva correções manuais e pouco escaláveis.

---

## 3. Proposta: modelo RBAC (Role-Based Access Control)

Objetivo: **permissões como dados** (tabelas), **perfis como conjuntos de permissões** (roles), **fácil de administrar e de estender**, sem blob serializado.

### 3.1 Visão geral

- **Permissões** = tabela de “ações” (código + descrição). Uma linha por permissão (vCliente, aNfecom, etc.).
- **Roles (papéis)** = tabela de perfis (ex: Administrador, Vendedor, Financeiro). Só nome e tenant.
- **Role + Permissão** = tabela N:N: qual papel tem qual permissão.
- **Usuário** = continua com um “perfil”, mas o perfil passa a ser um **role_id** (FK para a tabela de roles).
- **Tenant** = pode continuar com uma tabela “o que este tenant pode usar” (ex: tenant_permissoes_menu), mas agora como “quais permissões estão habilitadas para o tenant”; a checagem final seria: “esta permissão está habilitada para o tenant?” **e** “o papel do usuário tem esta permissão?”.

### 3.2 Estrutura sugerida de tabelas

```sql
-- 1) Catálogo de permissões (uma linha por ação)
CREATE TABLE permissoes_catalogo (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo      VARCHAR(50)  NOT NULL UNIQUE COMMENT 'ex: vCliente, aNfecom',
    descricao   VARCHAR(120) NOT NULL,
    modulo      VARCHAR(50)  NULL COMMENT 'agrupador para tela: Pessoas, NFCom, etc.',
    ativo       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NULL,
    updated_at  DATETIME     NULL
);

-- 2) Papéis (perfis) – substitui o “nome” do perfil atual
CREATE TABLE roles (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten_id      INT UNSIGNED NULL COMMENT 'NULL = global (super)',
    nome        VARCHAR(80)  NOT NULL,
    descricao   VARCHAR(255) NULL,
    ativo       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NULL,
    updated_at  DATETIME     NULL,
    UNIQUE KEY uk_tenant_nome (ten_id, nome)
);

-- 3) Qual papel tem qual permissão (N:N)
CREATE TABLE role_permissoes (
    role_id       INT UNSIGNED NOT NULL,
    permissao_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permissao_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permissao_id) REFERENCES permissoes_catalogo(id) ON DELETE CASCADE
);

-- 4) Usuário aponta para um papel (em vez de permissoes_id)
-- Na tabela usuarios: trocar permissoes_id por role_id (FK para roles).
-- Ou manter permissoes_id por compatibilidade e preencher role_id a partir do id do perfil antigo.

-- 5) Por tenant: quais permissões estão “habilitadas” para aquele tenant (menu/funcionalidade)
-- Pode ser a mesma ideia do tenant_permissoes_menu, mas referenciando permissoes_catalogo.id
-- ou mantendo por código (tpm_permissao) para não quebrar.
CREATE TABLE tenant_permissoes (
    ten_id        INT UNSIGNED NOT NULL,
    permissao_id  INT UNSIGNED NOT NULL,
    ativo         TINYINT(1)  NOT NULL DEFAULT 1,
    PRIMARY KEY (ten_id, permissao_id),
    FOREIGN KEY (ten_id) REFERENCES tenants(ten_id) ON DELETE CASCADE,
    FOREIGN KEY (permissao_id) REFERENCES permissoes_catalogo(id) ON DELETE CASCADE
);
```

### 3.3 Fluxo da checagem (Permission.php)

1. Super usuário → liberado.
2. Buscar `role_id` do usuário (ou, na migração, derivar de `permissoes_id` para um role equivalente).
3. Se houver tenant:
   - Verificar se a permissão (por código) está em `tenant_permissoes` (ou tenant_permissoes_menu) e ativa.
   - Verificar se o **role** do usuário tem essa permissão em `role_permissoes`.
   - Liberar só se **as duas** forem verdade.
4. Sem tenant: verificar só se o role tem a permissão em `role_permissoes`.

Assim, tenant controla “o que está disponível” e o papel controla “o que este usuário pode fazer”.

### 3.4 Vantagens

| Aspecto | Hoje | Com RBAC |
|--------|------|-----------|
| Nova permissão | Vários arquivos + blob + possivelmente SQL | INSERT em `permissoes_catalogo` + uma tela que lista do catálogo |
| “Quem tem permissão X?” | Deserializar todos os perfis | `SELECT * FROM role_permissoes WHERE permissao_id = ?` |
| Novo perfil | Novo row em permissoes + montar blob inteiro | Novo row em roles + marcar permissões em role_permissoes (checkboxes por permissão do catálogo) |
| Dar uma permissão a um papel | Editar um blob enorme | INSERT em role_permissoes |
| Auditoria / relatório | Quase inviável | JOINs simples entre usuarios, roles, role_permissoes, permissoes_catalogo |
| Tenant + perfil | Só tenant importa na prática | Tenant (habilitado?) + Role (tem permissão?) |

### 3.5 Migração sugerida (resumida)

1. Criar `permissoes_catalogo` e popular a partir de `config/permission.php` (código + descrição + módulo).
2. Criar `roles` e `role_permissoes`.
3. Para cada linha em `permissoes`: criar um `roles` com o mesmo nome/ten_id; deserializar o blob e para cada chave com valor 1, encontrar `permissoes_catalogo.id` pelo código e inserir em `role_permissoes`.
4. Em `usuarios`: adicionar `role_id`; preencher com o id do role que corresponde ao atual `permissoes_id`.
5. Ajustar `Permission.php` para usar role + role_permissoes (e, se quiser, manter tenant_permissoes_menu como “habilitado para o tenant”).
6. Tela de “Editar permissão” (perfil): em vez de 90 checkboxes fixos, listar permissões de `permissoes_catalogo` (agrupadas por módulo) e marcar/desmarcar em `role_permissoes`.
7. Depois de estável, desativar uso do blob em `permissoes` e, em outro passo, deixar de usar a coluna ou remover a tabela antiga.

---

## 4. Conclusão

- **Hoje**: funciona, mas o uso de blob serializado, duas regras (tenant vs não-tenant) e a necessidade de tocar em vários arquivos para cada nova permissão tornam o sistema **rígido e difícil de administrar e auditar**.
- **Proposta**: modelo **RBAC** com catálogo de permissões, papéis (roles) e tabela N:N (role_permissoes) torna o sistema **mais claro, extensível e administrável**, com migração possível sem quebrar o que já existe, desde que feita em etapas (novas tabelas, compatibilidade por role_id/permissoes_id, depois desligar o blob).

Se quiser, o próximo passo pode ser um script SQL concreto de criação das tabelas + um seed que popule `permissoes_catalogo` a partir do `permission.php` atual, e um esboço da nova lógica em `Permission.php` (checagem por role + tenant).

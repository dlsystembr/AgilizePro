# Plano: Usuários, Pessoas e Acesso por Grupo/Empresas

## Objetivo

1. Padronizar a tabela **usuarios** conforme as regras de nomenclatura (prefixos, FKs).
2. Cadastrar o usuário do sistema **dentro do cadastro de Pessoas**, usando o tipo "Pessoa Usuário".
3. Usuário vinculado a um **grupo empresarial** (`gre_id`); acesso às **empresas** controlado pela tabela de ligação **usuario_empresa**.

---

## 1. Estrutura desejada do usuário

Campos do usuário (apenas o necessário para login e contexto):

| Campo            | Descrição                          |
|------------------|------------------------------------|
| id               | PK                                 |
| nome             | Nome do usuário                    |
| email            | Login (único)                      |
| senha            | Hash da senha                      |
| situação         | Ativo (1) / Inativo (0)            |
| data_cadastro    | Data de criação                    |
| data_atualizacao | Data de alteração                  |
| url_imagem       | Foto do usuário                    |
| gre_id           | Grupo empresarial ao qual pertence |
| pes_id           | Vínculo com a pessoa (cadastro em Pessoas) |

Opcional (manter se fizer sentido para o negócio):

- **permissoes_id** – perfil de permissão (como hoje).
- **data_expiracao** – validade do acesso.

O usuário **pode ter acesso a mais de uma empresa** dentro do grupo. Por isso:

- **gre_id** → define o grupo do usuário.
- **usuario_empresa** → define em **quais empresas** daquele grupo ele pode entrar.

---

## 2. Padronização (regras_banco.md)

- **Tabelas principais:** plural → `usuarios`, `pessoas`, `empresas`, `grupos_empresariais`.
- **Tabelas de ligação:** singular → `usuario_empresa`, `pessoa_tipos`, `menu_empresa`.
- **Colunas:** prefixo de 3 letras da tabela.
  - Em `usuarios` → prefixo **USU_** (ou **usu_** em minúsculo): `usu_id`, `usu_nome`, `usu_email`, etc.
- **FKs:** manter o nome da coluna da tabela pai (sem concatenar prefixos).
  - Em `usuarios`: `gre_id` (referência a `grupos_empresariais.gre_id`), `pes_id` (referência a `pessoas.pes_id`).
  - Em `usuario_empresa`: `usu_id`, `emp_id`.

---

## 3. Tabelas envolvidas

### 3.1 Tabela `usuarios` (padronizada)

Estrutura proposta (nomenclatura padrão):

```sql
usuarios (
  usu_id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usu_nome              VARCHAR(255) NOT NULL,
  usu_email             VARCHAR(100) NOT NULL,
  usu_senha             VARCHAR(255) NOT NULL,
  usu_situacao          TINYINT(1) NOT NULL DEFAULT 1,
  usu_data_cadastro     DATETIME NULL,
  usu_data_atualizacao  DATETIME NULL,
  usu_url_imagem        VARCHAR(255) NULL,
  gre_id                INT UNSIGNED NULL,          -- Grupo empresarial
  pes_id                INT UNSIGNED NULL,          -- Pessoa (cadastro em Pessoas, tipo Usuário)
  permissoes_id         INT NULL,                   -- Perfil de permissão (opcional)
  usu_data_expiracao    DATE NULL,                  -- Opcional
  -- Constraint UNIQUE (usu_email)
  -- FK gre_id -> grupos_empresariais(gre_id)
  -- FK pes_id -> pessoas(pes_id)
  -- FK permissoes_id -> permissoes(idPermissao)
)
```

- **gre_id:** grupo ao qual o usuário pertence (obrigatório para usuário “cliente”).
- **pes_id:** vínculo com o cadastro em Pessoas (quando o usuário é criado a partir de uma pessoa com tipo “Usuário”).

### 3.2 Tabela `usuario_empresa` (ligação N:N)

Define a quais **empresas** (do grupo) o usuário tem acesso:

```sql
usuario_empresa (
  uem_id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usu_id             INT UNSIGNED NOT NULL,   -- FK usuarios(usu_id)
  emp_id             INT NOT NULL,            -- FK empresas(emp_id)
  uem_data_cadastro  DATETIME NULL,
  UNIQUE (usu_id, emp_id),
  FK (usu_id) -> usuarios(usu_id) ON DELETE CASCADE,
  FK (emp_id) -> empresas(emp_id) ON DELETE CASCADE
)
```

- Um usuário pode ter várias linhas (uma por empresa).
- Ao abrir uma empresa no Super, o administrador seleciona os usuários e “adiciona” na empresa; isso gera registros em `usuario_empresa`.

### 3.3 Tabela `pessoas` e tipo “Usuário”

- Já existe **tipos_pessoa** com tipo “Usuário”.
- **pessoa_tipos** (ou equivalente) relaciona pessoa ↔ tipo.
- Fluxo: em **Pessoas**, ao cadastrar/editar uma pessoa, marcar tipo “Usuário” e, a partir daí, permitir “Criar usuário de acesso” (ou “Vincular usuário”), preenchendo login/senha e salvando em `usuarios` com `pes_id` = essa pessoa.

### 3.4 Tabela `grupos_empresariais`

- Já existe com `gre_id`, `gre_nome`, `gre_situacao`, `gre_data_cadastro`, `gre_data_atualizacao`.
- **gre_situacao** já está no SQL (`create_grupos_empresariais_empresas.sql`): 1 = ativo, 0 = inativo.

---

## 4. Fluxo de uso

1. **Grupo empresarial**  
   Super cadastra grupos em `grupos_empresariais`.

2. **Empresas**  
   Super cadastra empresas e associa cada uma a um grupo (`empresas.gre_id`).

3. **Pessoas**  
   No módulo Pessoas (por empresa/tenant), cadastra-se a pessoa e se marca tipo “Usuário”.

4. **Criação do usuário**  
   - Opção A: na tela de Pessoas, botão “Criar usuário de acesso” (abre modal ou tela com email, senha, grupo `gre_id`).  
   - Opção B: no Super, em “Usuários do grupo”, cadastrar usuário informando nome, email, senha, grupo e opcionalmente `pes_id` (se já existir pessoa).

5. **Acesso às empresas**  
   - No Super: dentro de um **grupo**, lista de **empresas**; ao abrir uma **empresa**, há “Usuários com acesso a esta empresa”.  
   - Ao selecionar um usuário e “dar acesso”, insere-se em `usuario_empresa` (usu_id, emp_id).  
   - Ao “remover acesso”, remove-se o registro em `usuario_empresa` (não apaga usuário nem empresa).

6. **Login**  
   - Usuário informa email/senha.  
   - Sistema identifica `usu_id`, `gre_id` e busca empresas em `usuario_empresa` para esse `usu_id`.  
   - Se houver apenas uma empresa, pode logar direto nela; se houver várias, pode mostrar seletor de empresa e gravar `emp_id` na sessão (como hoje com `emp_id`).

---

## 5. Migração da tabela `usuarios` atual

A tabela atual tem, entre outros:

- `idUsuarios`, `nome`, `email`, `senha`, `situacao`, `dataCadastro`, `permissoes_id`, `dataExpiracao`, `url_image_user`, `ten_id`, além de rg, cpf, endereço, telefone, etc.

Passos sugeridos:

1. **Criar nova estrutura** (em migration):
   - Adicionar colunas padronizadas: `usu_id` (ou renomear `idUsuarios` → `usu_id`), `usu_nome`, `usu_email`, `usu_senha`, `usu_situacao`, `usu_data_cadastro`, `usu_data_atualizacao`, `usu_url_imagem`, `gre_id`, `pes_id`.
   - Manter `permissoes_id` e `usu_data_expiracao` se forem usados.
2. **Migrar dados:**
   - Copiar valores das colunas antigas para as novas (nome → usu_nome, email → usu_email, etc.).
   - Definir regra para `ten_id` → `gre_id` (ex.: um tenant antigo vira um grupo ou um gre_id fixo).
   - Se houver tabela de pessoas e vínculo por email/nome, preencher `pes_id` onde der.
3. **Criar tabela `usuario_empresa`:**
   - Inserir linhas iniciais: para cada usuário migrado, criar um registro (usu_id, emp_id) para a empresa padrão daquele tenant (ex.: primeira empresa com mesmo ten_id).
4. **Remover colunas antigas** (ou deprecar) e **remover `ten_id`** quando todo o fluxo passar a usar `gre_id` + `usuario_empresa`.
5. **Ajustar código:** controllers, models, login e sessão para usar apenas `usu_*`, `gre_id` e `usuario_empresa` (emp_id na sessão).

---

## 6. Grupos de usuário, permissões por grupo e vínculo por empresa (tudo por emp_id)

**Tudo é vinculado à empresa (emp_id), não ao GRE.** Para saber o GRE, buscar a qual GRE a empresa está vinculada: `empresas.gre_id` onde `empresas.emp_id` = emp_id.

- **grupos_usuario:** gpu_id, **emp_id** (FK empresas), gpu_nome, gpu_descricao, gpu_situacao. Cada grupo pertence a uma **empresa**.
- **grupos_usuario_permissoes:** gup_id, gpu_id, **mep_id** (FK menu_empresa; só menus que a empresa tem). Colunas boolean: gup_visualizar, gup_editar, gup_deletar, gup_alterar, gup_relatorio. UNIQUE (gpu_id, mep_id). O grupo (gpu_id) já está vinculado à empresa; o recurso é o menu via menu_empresa.
- **grupo_usuario_empresa:** uge_id, usu_id, gpu_id, emp_id. UNIQUE (usu_id, emp_id) — um grupo por usuário por empresa. O grupo (gpu_id) deve ser da mesma emp_id (grupos_usuario.emp_id = grupo_usuario_empresa.emp_id).

**Arquivos:** migrations 20260202100008, 20260202100009, 20260202100010 e `sql/create_grupos_usuario_permissoes_uge.sql`.

---

## 7. Resumo das tabelas

| Tabela             | Tipo     | Papel                                                                 |
|--------------------|----------|-----------------------------------------------------------------------|
| grupos_empresariais | Principal | Grupo; tem gre_situacao (ativo/inativo).                              |
| empresas           | Principal | Pertence a um grupo (gre_id).                                         |
| pessoas            | Principal | Cadastro de pessoas; tipo “Usuário” para vincular a um usuario.      |
| usuarios           | Principal | Usuário do sistema: login, senha, gre_id, pes_id (padronizado usu_*).|
| usuario_empresa   | Ligação  | Quais empresas (emp_id) o usuário (usu_id) pode acessar.             |
| pessoa_tipos       | Ligação  | Quais tipos (ex.: Usuário) a pessoa tem.                             |
| grupo_usuario     | Principal | Grupos de usuário por EMPRESA (emp_id); GRE via empresas.gre_id.     |
| grupo_usuario_permissoes | Ligação | Por menu_empresa (mep_id): só menus que a empresa tem; colunas boolean visualizar, editar, deletar, alterar, relatório. |
| grupo_usuario_empresa | Ligação | Um grupo por usuário por empresa (emp_id); grupo da mesma emp_id. |

---

## 8. Trabalho no usuário primeiro (implementado)

Foi criada a migration **20260202100007_refactor_usuarios_table** que:

- **Remove** da tabela `usuarios`: rg, cpf, rua, numero, bairro, cidade, estado, telefone, celular, cep, ten_id (e as colunas antigas nome, email, senha, situacao, dataCadastro, url_image_user, dataExpiracao após copiar para as novas).
- **Adiciona/renomeia** para o padrão (prefixo usu_ e FKs): `usu_id` (PK), `usu_nome`, `usu_email`, `usu_senha`, `usu_situacao`, `usu_data_cadastro`, `usu_data_atualizacao`, `usu_url_imagem`, `usu_data_expiracao`, `gre_id`, `pes_id`; mantém `permissoes_id`.
- **Tabelas filhas:** renomeia `usuarios_id` para `usu_id` em garantias, os/ordem_servico, vendas, lancamentos e recria as FKs.

**Após rodar a migration**, atualizar o código para usar usu_id, usu_nome, usu_email, etc. A sessão deve usar `gre_id` em vez de `ten_id`. **Cadastro dentro de Pessoas:** fluxo "Criar usuário de acesso" a partir de uma pessoa com tipo "Usuário" (pes_id em usuarios) será implementado na sequência.

---

## 9. Próximos passos sugeridos

1. **Confirmar** se a tabela `usuarios` deve ser reduzida aos campos listados (e opcionais) e se `permissoes_id` e `data_expiracao` permanecem.
2. **Implementar migration** que:
   - Cria/ajusta `usuarios` com nomenclatura padrão e `gre_id`, `pes_id`.
   - Cria `usuario_empresa` com FKs corretas.
   - Migra dados de `ten_id` para `gre_id`/`usuario_empresa` conforme regra definida.
3. **Na área do cliente (Pessoas):** incluir tipo “Usuário” e fluxo “Criar/Vincular usuário de acesso” (com email, senha, grupo).
4. **No Super:** telas para:
   - Por grupo: listar usuários do grupo.
   - Por empresa: listar e adicionar/remover usuários com acesso àquela empresa (uso de `usuario_empresa`).
5. **Login e sessão:** usar `gre_id` e `usuario_empresa` para definir `emp_id` (e empresas disponíveis) após o login.

---

## 9. Observação sobre `create_grupos_empresariais_empresas.sql`

O arquivo **já inclui** o campo **gre_situacao** (1 = ativo, 0 = inativo). Nenhuma alteração é necessária nesse script para situação do grupo.

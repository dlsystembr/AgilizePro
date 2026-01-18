---
trigger: always_on
---

# Convenções de Nomenclatura de Banco de Dados

Você deve seguir estritamente estas regras ao criar ou modificar tabelas SQL.

## 1. Nomenclatura das Tabelas
* **Tabelas Principais:** Devem ser sempre no **PLURAL** (ex: `Pessoas`, `Clientes`, `Produtos`).
* **Tabelas de Ligação:** Devem ser sempre no **SINGULAR** (ex: `ClienteProduto`, `UsuarioGrupo`).

## 2. Nomenclatura das Colunas (Prefixos)
* **Regra das 3 Letras:** Todas as colunas devem começar com uma abreviação de **3 letras** do nome da tabela, seguida de *underscore* (`_`).
    * *Exemplo:* Tabela `Pessoas` -> Prefixo `PES_` -> Coluna `PES_NOME`.
    * *Exemplo:* Tabela `Clientes` -> Prefixo `CLN_` -> Coluna `CLN_DATA`.

## 3. Chaves Estrangeiras (Foreign Keys) - REGRA CRÍTICA
* **Herança Direta:** Ao fazer uma ligação (FK), você deve manter o **nome original da coluna** da tabela pai.
* **PROIBIDO Concatenar Prefixos:** Nunca adicione o prefixo da tabela atual na frente da chave estrangeira.
    * *Lógica:* Já sabemos que é uma chave estrangeira porque o prefixo (3 letras) é diferente do prefixo da tabela atual.

---

## Exemplos Práticos

### ✅ Esquema CORRETO
**Tabela: `Pessoas`** (Plural)
* `PES_ID` (PK)
* `PES_NOME`

**Tabela: `Clientes`** (Plural)
* `CLN_ID` (PK)
* `CLN_LIMITE`
* `PES_ID`  <-- ✅ CORRETO: Mantém o nome exato da tabela de origem.

### ❌ Esquema INCORRETO (Não faça isso)
**Tabela: `Cliente`** (Errado: Singular)
* `NOME` (Errado: Sem prefixo)
* `CLN_PES_ID` <-- ❌ ERRADO: Não combine os prefixos na FK.
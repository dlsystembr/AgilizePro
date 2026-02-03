# Regime tributário do destinatário (NFCom)

## Por que armazenar

Na NFCom, se o **destinatário** for **MEI** ou **Simples Nacional**, a SEFAZ exige que seja informado como **não contribuinte (indIEDest=9)** e **sem inscrição estadual** para efeito de validação, mesmo que a empresa tenha IE cadastrada. Caso contrário pode ocorrer rejeição 428 (IE não cadastrada).

Por isso precisamos saber, no cadastro, se a pessoa jurídica é **MEI**, **Simples Nacional** ou **Regime Normal**.

---

## Onde armazenar: tabela **pessoas**

| Tabela      | Motivo |
|------------|--------|
| **pessoas** | O regime tributário é uma característica da **pessoa jurídica** (CNPJ), não do “cliente” nem do documento. Uma mesma pessoa pode ser cliente, fornecedor etc.; o regime é único por CNPJ. Por isso o campo fica em `pessoas`. |
| clientes   | Refere-se ao vínculo comercial (é cliente da empresa). O regime é da Receita Federal, não do nosso cadastro comercial. |
| documentos | Guarda IE, RG, etc. O regime não é um documento, é uma classificação da PJ. |

**Campo criado:** `pessoas.pes_regime_tributario` (VARCHAR(30) NULL).

**Valores aceitos:**

- `MEI` — Microempreendedor Individual
- `Simples Nacional` — Optante pelo Simples Nacional (não MEI)
- `Regime Normal` — Lucro Real, Lucro Presumido, etc.
- `NULL` — Não informado (opcional para CPF; **obrigatório para CNPJ** no cadastro de pessoas)

---

## Origem do dado: API CNPJ.WS

Na **busca por CNPJ** no cadastro de pessoas (botão “Buscar CNPJ”), a API **publica.cnpj.ws** já retorna:

- **Simples Nacional / MEI:** objeto `simples_nacional` (ou no nível da empresa/estabelecimento), com por exemplo:
  - `mei`: "Sim" / "Não"
  - `simples`: "Sim" / "Não"
  - `data_opcao_simples`, `data_exclusao_simples`, `data_opcao_mei`, `data_exclusao_mei`

**Regra sugerida ao preencher automaticamente:**

1. Se `simples_nacional.mei === "Sim"` (e não excluído) → `pes_regime_tributario = 'MEI'`
2. Senão, se `simples_nacional.simples === "Sim"` (e não excluído) → `pes_regime_tributario = 'SIMPLES_NACIONAL'`
3. Senão → `pes_regime_tributario = 'REGIME_NORMAL'`

(Considerar datas de exclusão quando a API as enviar.)

---

## Uso na NFCom

Ao montar o destinatário da NFCom:

- Se `pes_regime_tributario` for `MEI` ou `SIMPLES_NACIONAL`: usar **indIEDest=9** (não contribuinte) e não enviar IE para validação (ou enviar apenas informativo, conforme regra do layout).
- Se for `REGIME_NORMAL` ou `NULL`: manter a lógica atual (usar IE e indIEDest=1 quando houver IE).

O script de alteração da base está em `sql/add_pes_regime_tributario.sql`.

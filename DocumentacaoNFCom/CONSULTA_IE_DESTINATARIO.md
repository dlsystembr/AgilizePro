# Consulta à Inscrição Estadual (IE) do Destinatário – NFCom

## 1. O que a documentação NFCom diz (XSD)

No arquivo **`nfcomTiposBasico_v1.00.xsd`**:

- **indIEDest** (Indicador da IE do Destinatário):
  - **1** – Contribuinte ICMS (informar a IE do destinatário)
  - **2** – Contribuinte isento de Inscrição no **cadastro de Contribuintes do ICMS**
  - **9** – Não Contribuinte, que pode ou não possuir IE no **Cadastro de Contribuintes do ICMS**

- **IE** (Inscrição Estadual do destinatário): tag opcional (`minOccurs="0"`).

- **Nota:** Para indIEDest=2 informar a tag IE com o literal **ISENTO**.

---

## 2. Rejeição 428 – “IE do Destinatário não cadastrada”

Conforme regras de validação da NFCom:

- **Se IE do destinatário for informada:** a SEFAZ consulta o **Cadastro de Contribuinte da UF** (chave: **IE do destinatário**).
- A IE **deve estar cadastrada** nesse cadastro da UF para a nota ser aceita.
- **Observação:** desconsiderar a situação da IE perante o fisco; o que vale é estar **cadastrada** na UF.

Ou seja: para **indIEDest=1** a IE informada precisa constar no cadastro da UF usada na validação da NFCom.

### 2.1 Não é problema de código do município (IBGE)

A rejeição 428 refere-se **apenas à IE do destinatário** (não cadastrada na UF). O código do município (cMun / IBGE) **não** é a causa dessa rejeição.

- **Posse-GO:** código IBGE oficial é **5218300** (correto).
- Se muitas notas rejeitadas forem de clientes do mesmo município (ex.: Posse), isso costuma ocorrer porque a maioria dos destinatários é desse município; a SEFAZ rejeita porque a **IE** não consta no cadastro, não por causa do município.
- O sistema já envia **cMun** com 7 dígitos (zeros à esquerda quando necessário) e **IE** para Goiás com 12 dígitos (zeros à esquerda), conforme exigências da SEFAZ-GO.

---

## 3. Onde consultar se a IE está cadastrada

### 3.1 Portal – Cadastro Centralizado de Contribuinte (CCC)

- **URL (exemplo):**  
  [Portal NFe – Cadastro Centralizado de Contribuinte](https://www.nfe.fazenda.gov.br/portal/principal.aspx) (ou o portal da SEFAZ do seu estado).
- **Uso:** consulta por **CNPJ** (ou IE) para ver se o contribuinte está habilitado e em qual UF.
- **Observação:** o portal pode listar “DFe Habilitados” (ex.: NFe, NFCe) e **não** citar NFCom; a validação da NFCom pode usar o mesmo cadastro da UF ou regras específicas.

### 3.2 Web Service – CadConsultaCadastro2

A SEFAZ disponibiliza o **Web Service CadConsultaCadastro2** para consulta ao cadastro de contribuintes (IE/CNPJ) por estado.

- **Função:** consultar se um CNPJ/IE está cadastrado na UF.
- **Protocolo:** SOAP (1.1 ou 1.2).
- **Parâmetros típicos:** `versaoDados`, `cUF` (código da UF, ex.: 52 para GO), e dados da consulta (CNPJ/IE).
- **Endpoint:** varia por **UF**. O endereço é diferente para cada estado (ex.: ES tem um URL; GO terá outro).
- **Onde achar o endpoint da sua UF:**
  - Manual da **NFe** (ou NFCom) da SEFAZ do seu estado – tabela de Web Services ou “Consulta Cadastro”.
  - Site da **SEFAZ-GO** (ou do estado do destinatário), seção de integração/desenvolvedor.

**Exemplo de estrutura (genérico):**

- ES: `https://app.sefaz.es.gov.br/ConsultaCadastroService/CadConsultaCadastro2.asmx`
- Para **Goiás (GO)** é necessário consultar a documentação oficial da SEFAZ-GO para o URL correto do CadConsultaCadastro2.

### 3.3 Uso no sistema

- **Antes de enviar a NFCom:** usar o **portal CCC** para checar se o destinatário (CNPJ/IE) está habilitado na UF.
- **Para automatizar:** implementar chamada ao **CadConsultaCadastro2** da UF do destinatário (consultar manual da SEFAZ da UF para URL e formato exato).

---

## 4. Resumo

| Objetivo                         | Onde consultar                                      |
|----------------------------------|-----------------------------------------------------|
| Ver se IE/CNPJ está “habilitado” | Portal NFe/SEFAZ – Cadastro Centralizado (CCC)      |
| Consulta programática (IE/CNPJ)  | Web Service **CadConsultaCadastro2** da UF desejada  |
| Entender rejeição 428            | Regras de validação NFCom – Cadastro da UF (chave IE) |

Para **Goiás**, consulte a documentação oficial da SEFAZ-GO (manuais NFe/NFCom ou área de desenvolvedor) para obter o **endpoint exato** do CadConsultaCadastro2 e o layout da mensagem SOAP.

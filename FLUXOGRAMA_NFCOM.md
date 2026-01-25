# Fluxograma - Processo NFCom e Cálculos Tributários

## 1. FLUXO PRINCIPAL - CRIAÇÃO DA NFCOM

```
┌─────────────────────────────────────────────────────────────┐
│                    INÍCIO - Tela Adicionar NFCom            │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Usuário preenche formulário:                              │
│  - Cliente (com endereço selecionado)                      │
│  - Operação Comercial                                      │
│  - Serviços (produtos) com quantidade e valor              │
│  - Datas (emissão, contrato, vencimento)                   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Controller: Nfecom->adicionar()                           │
│  Validação de dados do formulário                          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  VALIDAÇÕES INICIAIS                                        │
│  ✓ Cliente existe e tem endereço com UF                    │
│  ✓ Operação comercial informada                            │
│  ✓ Pelo menos um serviço válido                            │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  PROCESSAMENTO DOS SERVIÇOS                                 │
│  Para cada serviço:                                         │
│  - Calcula valor total (qtd × valor unitário)             │
│  - Aplica desconto e outros valores                         │
│  - Soma ao total bruto                                      │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  BUSCAR DADOS DO CLIENTE                                    │
│  - UF do endereço selecionado                              │
│  - Natureza do contribuinte                                │
│  - Objetivo comercial                                       │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  CÁLCULO DE TRIBUTAÇÃO (LOOP POR SERVIÇO)                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Para cada serviço:                                   │   │
│  │                                                       │   │
│  │ 1. Verificar se produto tem NCM                      │   │
│  │ 2. Chamar API CalculoTributacaoApi                   │   │
│  │ 3. Somar PIS/COFINS ao total                         │   │
│  └─────────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  VALIDAÇÃO DE TRIBUTAÇÃO                                    │
│  ✓ Todos os serviços têm NCM                               │
│  ✓ API retornou dados válidos                              │
│  ✓ PIS/COFINS calculados corretamente                      │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  PREPARAR DADOS DA CAPA NFCOM                               │
│  - Dados do emitente (empresa)                             │
│  - Dados do destinatário (cliente)                         │
│  - Valores totais (PIS, COFINS, IRRF)                      │
│  - Informações do contrato                                  │
│  - Adicionar ten_id (obrigatório)                          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  SALVAR CAPA NFCOM                                          │
│  Nfecom_model->add('nfecom_capa', $nfecomData)            │
│  Retorna: $idNfecom                                         │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  SALVAR ITENS (LOOP POR SERVIÇO)                            │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Para cada serviço:                                   │   │
│  │                                                       │   │
│  │ 1. Calcular tributação específica do item            │   │
│  │ 2. Preparar dados do item                            │   │
│  │ 3. Salvar em nfecom_itens                           │   │
│  └─────────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  SUCESSO - NFCom Salva                                      │
│  Redirect para visualizar                                  │
└─────────────────────────────────────────────────────────────┘
```

## 2. FLUXO DETALHADO - CÁLCULO DE TRIBUTAÇÃO (API)

```
┌─────────────────────────────────────────────────────────────┐
│  Nfecom->calcularTributacao()                               │
│  Parâmetros:                                                │
│  - produto_id, cliente_id, operacao_id                     │
│  - valor, quantidade, tipo_operacao                        │
│  - endereco_id (opcional)                                  │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Montar URL da API                                          │
│  base_url/index.php/calculotributacaoapi/calcular          │
│  + parâmetros GET                                           │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Requisição HTTP (cURL)                                     │
│  - Seguir redirecionamentos                                 │
│  - Timeout 30s                                              │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  CalculoTributacaoApi->calcular()                           │
│  (extends CI_Controller - sem sessão)                       │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  VALIDAÇÃO DE PARÂMETROS                                    │
│  ✓ ten_id, produto_id, cliente_id                          │
│  ✓ operacao_id, valor                                       │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  BUSCAR DADOS DO PRODUTO                                    │
│  Query direta (sem sessão):                                │
│  - NCM_ID (obrigatório)                                    │
│  - PRO_ORIGEM (origem do produto)                          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  BUSCAR DADOS DO CLIENTE                                    │
│  Query com JOINs:                                           │
│  - UF do endereço (selecionado ou padrão)                  │
│  - Natureza do contribuinte                                 │
│  - Objetivo comercial                                       │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  DETERMINAR DESTINAÇÃO                                      │
│  Comparar UF Empresa vs UF Cliente:                        │
│  - Mesma UF = Estadual                                      │
│  - UFs diferentes = Interestadual                           │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  BUSCAR CLASSIFICAÇÃO FISCAL                                │
│  Query direta (sem sessão):                                 │
│  - Filtros: OPC_ID, natureza, destinacao, objetivo         │
│  - Retorna: CST, CSOSN, CFOP, tipo_tributacao             │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  BUSCAR TRIBUTAÇÃO FEDERAL                                  │
│  TributacaoFederal_model->getByTenantAndNcm()              │
│  - Filtros: ten_id, ncm_id, tipo_operacao                  │
│  - Retorna: CST IPI, CST PIS, CST COFINS                   │
│  - Retorna: Alíquotas IPI, PIS, COFINS                     │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  BUSCAR TRIBUTAÇÃO ESTADUAL                                 │
│  TributacaoEstadual_model->getByTenantAndNcmAndUf()        │
│  - Filtros: ten_id, ncm_id, uf                             │
│  - Retorna: Alíquota ICMS, MVA, ST, FCP                    │
│  - Retorna: Percentuais de redução                         │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  CÁLCULOS DE IMPOSTOS FEDERAIS                              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ IPI:                                                  │   │
│  │ - Calcula se CST = 00 ou 50                           │   │
│  │ - Base = valor × quantidade                           │   │
│  │ - Valor = base × alíquota                             │   │
│  │                                                       │   │
│  │ PIS:                                                  │   │
│  │ - Calcula se CST = 01 ou 02                           │   │
│  │ - Base = valor × quantidade                           │   │
│  │ - Valor = base × alíquota                             │   │
│  │                                                       │   │
│  │ COFINS:                                               │   │
│  │ - Calcula se CST = 01 ou 02                           │   │
│  │ - Base = valor × quantidade                           │   │
│  │ - Valor = base × alíquota                             │   │
│  └─────────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  CÁLCULOS DE IMPOSTOS ESTADUAIS                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Verificar se calcula ICMS:                           │   │
│  │ - NÃO calcula se: tipo_tributacao = 'Serviço'      │   │
│  │ - NÃO calcula se: CST = 40, 41, 50, 51, 60           │   │
│  │ - CALCULA se: CST = 00, 10, 20, 30, 70               │   │
│  │                                                       │   │
│  │ Base ICMS:                                            │   │
│  │ - Inicia com valor × quantidade                      │   │
│  │ - Adiciona IPI (se aplicável)                       │   │
│  │ - Aplica redução (CST 20, 70)                        │   │
│  │                                                       │   │
│  │ Valor ICMS:                                           │   │
│  │ - Se alíquota = 0, buscar na tabela aliquotas        │   │
│  │ - Valor = base × alíquota                            │   │
│  │                                                       │   │
│  │ ICMS ST (CST 10, 30, 70):                            │   │
│  │ - Base ST = valor × (1 + MVA/100)                    │   │
│  │ - Aplica redução ST                                   │   │
│  │ - Valor ST = (Base ST × Alíq ST) - ICMS Normal       │   │
│  │                                                       │   │
│  │ FCP:                                                  │   │
│  │ - Base = Base ICMS                                    │   │
│  │ - Valor = base × alíquota FCP                        │   │
│  └─────────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  MONTAR RESPOSTA JSON                                        │
│  {                                                           │
│    "sucesso": true,                                         │
│    "dados": {                                               │
│      "classificacao_fiscal": {...},                         │
│      "impostos_federais": {                                │
│        "ipi": {...},                                        │
│        "pis": {...},                                        │
│        "cofins": {...}                                      │
│      },                                                     │
│      "impostos_estaduais": {                                │
│        "icms": {...},                                       │
│        "icms_st": {...},                                    │
│        "fcp": {...}                                         │
│      }                                                      │
│    }                                                        │
│  }                                                          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Retornar para Nfecom                                       │
│  Usar valores para salvar nos itens                         │
└─────────────────────────────────────────────────────────────┘
```

## 3. FLUXO - BUSCA DE CLASSIFICAÇÃO FISCAL

```
┌─────────────────────────────────────────────────────────────┐
│  CalculoTributacaoApi precisa de:                          │
│  - CST, CSOSN, CFOP                                         │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Query direta na tabela classificacao_fiscal                │
│  WHERE:                                                     │
│  - OPC_ID = operacao_id                                    │
│  - CLF_NATUREZA_CONTRIBUINTE = natureza                    │
│  - CLF_DESTINACAO = destinacao (estadual/interestadual)    │
│  - CLF_OBJETIVO_COMERCIAL = objetivo                       │
│  - ten_id = ten_id                                         │
│  - CLF_SITUACAO = 'ativa'                                  │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Retorna:                                                   │
│  - CLF_ID (id)                                             │
│  - CLF_CST (cst)                                           │
│  - CLF_CSOSN (csosn)                                       │
│  - CLF_CFOP (cfop)                                         │
│  - CLF_TIPO_TRIBUTACAO (tipo_tributacao)                   │
└─────────────────────────────────────────────────────────────┘
```

## 4. FLUXO - BUSCA DE TRIBUTAÇÃO FEDERAL

```
┌─────────────────────────────────────────────────────────────┐
│  CalculoTributacaoApi precisa de:                          │
│  - CST IPI, CST PIS, CST COFINS                            │
│  - Alíquotas IPI, PIS, COFINS                              │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  TributacaoFederal_model->getByTenantAndNcm()              │
│  WHERE:                                                     │
│  - ten_id = ten_id                                         │
│  - ncm_id = ncm_id                                         │
│  - tipo_operacao = 'entrada' ou 'saida'                    │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Retorna campos baseado em tipo_operacao:                   │
│  Se entrada:                                                │
│  - tbf_cst_ipi_entrada → cst_ipi                           │
│  - tbf_aliquota_ipi_entrada → aliquota_ipi                 │
│  - tbf_cst_pis_cofins_entrada → cst_pis, cst_cofins        │
│  - tbf_aliquota_pis_entrada → aliquota_pis                 │
│  - tbf_aliquota_cofins_entrada → aliquota_cofins           │
│                                                             │
│  Se saída:                                                  │
│  - tbf_cst_ipi_saida → cst_ipi                             │
│  - tbf_aliquota_ipi_saida → aliquota_ipi                   │
│  - tbf_cst_pis_cofins_saida → cst_pis, cst_cofins          │
│  - tbf_aliquota_pis_saida → aliquota_pis                   │
│  - tbf_aliquota_cofins_saida → aliquota_cofins             │
└─────────────────────────────────────────────────────────────┘
```

## 5. FLUXO - BUSCA DE TRIBUTAÇÃO ESTADUAL

```
┌─────────────────────────────────────────────────────────────┐
│  CalculoTributacaoApi precisa de:                          │
│  - Alíquota ICMS, MVA, ST, FCP                             │
│  - Percentuais de redução                                  │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  TributacaoEstadual_model->getByTenantAndNcmAndUf()        │
│  WHERE:                                                     │
│  - ten_id = ten_id                                         │
│  - ncm_id = ncm_id                                         │
│  - tbe_uf = uf_cliente                                     │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Retorna:                                                   │
│  - tbe_tipo_tributacao → tipo_tributacao                   │
│  - tbe_aliquota_icms → aliquota_icms                       │
│  - tbe_mva → mva                                           │
│  - tbe_aliquota_icms_st → aliquota_icms_st                 │
│  - tbe_percentual_reducao_icms → percentual_reducao_icms   │
│  - tbe_percentual_reducao_st → percentual_reducao_st       │
│  - tbe_aliquota_fcp → aliquota_fcp                         │
└─────────────────────────────────────────────────────────────┘
```

## 6. FLUXO - REGRAS DE CÁLCULO POR CST

### 6.1. ICMS - Códigos de Situação Tributária

```
CST 00 - Tributada integralmente
├─ Calcula ICMS normalmente
├─ Base = Valor + IPI (se aplicável)
└─ Valor = Base × Alíquota

CST 10 - Tributada com ST
├─ Calcula ICMS normal
├─ Calcula ICMS ST
│  ├─ Base ST = Valor × (1 + MVA/100)
│  ├─ Aplica redução ST (se houver)
│  └─ Valor ST = (Base ST × Alíq ST) - ICMS Normal
└─ Pode ter FCP

CST 20 - Tributada com redução de base
├─ Calcula ICMS com redução
├─ Base = (Valor + IPI) × (1 - %Redução/100)
└─ Valor = Base × Alíquota

CST 30 - Isenta ou não tributada com ST
├─ NÃO calcula ICMS normal
├─ Calcula ICMS ST
└─ Base e Valor ICMS = 0

CST 40 - Isenta
└─ Base e Valor ICMS = 0

CST 41 - Não tributada
└─ Base e Valor ICMS = 0

CST 50 - Suspensão
└─ Base e Valor ICMS = 0

CST 51 - Diferimento
└─ Base e Valor ICMS = 0

CST 60 - ICMS cobrado anteriormente por ST
└─ Base e Valor ICMS = 0

CST 70 - Tributada com redução de base e ST
├─ Calcula ICMS com redução
├─ Calcula ICMS ST
└─ Pode ter FCP
```

### 6.2. IPI - Códigos de Situação Tributária

```
CST 00 - Entrada com recuperação / Saída tributada
└─ Calcula IPI
   ├─ Base = Valor × Quantidade
   └─ Valor = Base × Alíquota

CST 01-05, 49 - Entrada (isento, suspenso, etc.)
└─ Base e Valor IPI = 0

CST 51-55, 99 - Saída (isento, suspenso, etc.)
└─ Base e Valor IPI = 0
```

### 6.3. PIS/COFINS - Códigos de Situação Tributária

```
CST 01 - Operação Tributável (Alíquota Normal)
└─ Calcula PIS/COFINS
   ├─ Base = Valor × Quantidade
   └─ Valor = Base × Alíquota

CST 02 - Operação Tributável (Alíquota Diferenciada)
└─ Calcula PIS/COFINS
   ├─ Base = Valor × Quantidade
   └─ Valor = Base × Alíquota Diferenciada

CST 03-09, 49, 50-99 - Isento, não tributado, suspenso, etc.
└─ Base e Valor PIS/COFINS = 0
```

## 7. FLUXO - SALVAMENTO DOS DADOS

```
┌─────────────────────────────────────────────────────────────┐
│  DADOS DA CAPA (nfecom_capa)                                │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ - Dados do emitente (empresa)                        │   │
│  │ - Dados do destinatário (cliente)                    │   │
│  │ - Valores totais:                                     │   │
│  │   • NFC_V_PROD (valor produtos)                      │   │
│  │   • NFC_V_PIS (total PIS)                            │   │
│  │   • NFC_V_COFINS (total COFINS)                      │   │
│  │   • NFC_V_IRRF (total IRRF)                          │   │
│  │   • NFC_V_NF (valor total NF)                        │   │
│  │ - Informações do contrato                             │   │
│  │ - ten_id (obrigatório)                                │   │
│  └─────────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  DADOS DOS ITENS (nfecom_itens) - Um registro por serviço  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ - NFC_ID (FK para capa)                              │   │
│  │ - NFI_N_ITEM (número do item)                        │   │
│  │ - NFI_C_PROD (código produto)                        │   │
│  │ - NFI_X_PROD (descrição)                             │   │
│  │ - NFI_CFOP (CFOP)                                    │   │
│  │ - NFI_C_CLASS (código classificação)                 │   │
│  │ - NFI_CST_ICMS (CST ICMS)                            │   │
│  │ - NFI_CST_PIS (CST PIS)                              │   │
│  │ - NFI_CST_COFINS (CST COFINS)                        │   │
│  │ - NFI_V_BC_PIS (base cálculo PIS)                    │   │
│  │ - NFI_P_PIS (alíquota PIS)                           │   │
│  │ - NFI_V_PIS (valor PIS)                              │   │
│  │ - NFI_V_BC_COFINS (base cálculo COFINS)              │   │
│  │ - NFI_P_COFINS (alíquota COFINS)                     │   │
│  │ - NFI_V_COFINS (valor COFINS)                        │   │
│  │ - NFI_V_BC_FUST, NFI_P_FUST, NFI_V_FUST              │   │
│  │ - NFI_V_BC_FUNTEL, NFI_P_FUNTEL, NFI_V_FUNTEL        │   │
│  │ - NFI_V_BC_IRRF, NFI_V_IRRF                          │   │
│  │ - ten_id (obrigatório)                                │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## 8. FLUXO - VISUALIZAÇÃO DA NFCOM

```
┌─────────────────────────────────────────────────────────────┐
│  Nfecom->visualizar($id)                                    │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Buscar dados da capa                                        │
│  Nfecom_model->getByIdWithOperation($id)                   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Buscar itens                                                │
│  Nfecom_model->getItens($id)                                │
│  Retorna todos os campos dos itens                          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Exibir na view visualizar.php                              │
│  - Aba 1: Itens/Serviços (tabela simples)                  │
│  - Aba 2: Tributos dos Itens (detalhado)                    │
│  - Aba 3: Informações Gerais                                │
│  - Aba 4: Totais e Impostos                                 │
└─────────────────────────────────────────────────────────────┘
```

## 9. FLUXO - GERAÇÃO DO DANFE (PDF)

```
┌─────────────────────────────────────────────────────────────┐
│  Nfecom->danfe($id)                                         │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Buscar dados da capa e itens                               │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Preparar dados formatados                                  │
│  - Dados do emitente                                        │
│  - Dados do destinatário                                    │
│  - Itens com tributos (usando getBaseCalculoPisCofins)      │
│  - Totais (todos da nota)                                   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Função getBaseCalculoPisCofins()                           │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Valida base de cálculo:                               │   │
│  │ - Se CST isento (03-09): retorna 0                    │   │
│  │ - Se base salva > produto × 1.5: usa produto          │   │
│  │ - Se base salva = 0: usa produto                      │   │
│  │ - Caso contrário: usa base salva                      │   │
│  └─────────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│  Gerar PDF com NFComPreview                                 │
│  Exibir todos os dados tributários corretos                 │
└─────────────────────────────────────────────────────────────┘
```

## 10. RESUMO DAS APIS UTILIZADAS

```
┌─────────────────────────────────────────────────────────────┐
│  1. ClassificacaoFiscalApi                                   │
│     GET /classificacaofiscalapi/listar                      │
│     Filtros incrementais com fallback                       │
│     Retorna: id, cst, csosn, cfop                           │
│                                                              │
│  2. TributacaoFederalApi                                    │
│     GET /tributacaofederalapi/listar                        │
│     Filtros: ten_id, ncm_id, tipo_operacao                  │
│     Retorna: CST e alíquotas IPI, PIS, COFINS               │
│                                                              │
│  3. TributacaoEstadualApi                                   │
│     GET /tributacaoestadualapi/listar                       │
│     Filtros: ten_id, ncm_id, uf                             │
│     Retorna: Alíquotas ICMS, MVA, ST, FCP                   │
│                                                              │
│  4. CalculoTributacaoApi                                    │
│     GET /calculotributacaoapi/calcular                      │
│     Filtros: ten_id, produto_id, cliente_id, operacao_id,   │
│              valor, quantidade, tipo_operacao, endereco_id  │
│     Retorna: Todos os cálculos consolidados                 │
└─────────────────────────────────────────────────────────────┘
```

## 11. PONTOS CRÍTICOS E VALIDAÇÕES

```
┌─────────────────────────────────────────────────────────────┐
│  VALIDAÇÕES OBRIGATÓRIAS:                                   │
│  ✓ Cliente existe e tem endereço com UF                    │
│  ✓ Operação comercial informada                             │
│  ✓ Produto tem NCM configurado                              │
│  ✓ API retornou dados válidos                               │
│  ✓ ten_id presente na sessão                                │
│                                                              │
│  TRATAMENTO DE ERROS:                                       │
│  - Se API retornar erro: não salva NFCom                    │
│  - Se falta NCM: não salva item                             │
│  - Se falta tributação: mostra erro específico              │
│  - Se base incorreta: corrige na visualização               │
│                                                              │
│  FALLBACKS:                                                 │
│  - Alíquota ICMS = 0: busca na tabela aliquotas             │
│  - Base PIS/COFINS incorreta: usa valor do produto          │
│  - Endereço não selecionado: usa endereço padrão            │
└─────────────────────────────────────────────────────────────┘
```

---

**Versão:** 1.0  
**Data:** 23/01/2026  
**Sistema:** Map-OS - NFCom

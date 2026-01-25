# Análise Completa de Campos Faltantes na NFCom

## Resumo Executivo

Baseado na especificação oficial da NFCom e na estrutura atual do banco de dados, foram identificados campos faltantes tanto na tabela `nfecom_capa` quanto na tabela `nfecom_itens`.

---

## 1. TABELA `nfecom_capa` - Campos Faltantes

### Campos de ICMS e FCP (Totais)

| Campo Especificação | Campo Banco | Tipo | Status |
|---------------------|-------------|------|--------|
| vBC_total | NFC_V_BC_ICMS | DECIMAL(15,2) | ❌ FALTA |
| vICMS_total | NFC_V_ICMS | DECIMAL(15,2) | ❌ FALTA |
| vICMSDeson_total | NFC_V_ICMS_DESON | DECIMAL(15,2) | ❌ FALTA |
| vFCP_total | NFC_V_FCP | DECIMAL(15,2) | ❌ FALTA |

**Total faltante na CAPA: 4 campos**

---

## 2. TABELA `nfecom_itens` - Campos Faltantes

### 2.1 Campos de ICMS Básico (5 campos)
- NFI_V_BC_ICMS - Base de Cálculo do ICMS
- NFI_P_ICMS - Alíquota do ICMS (%)
- NFI_V_ICMS - Valor do ICMS
- NFI_V_ICMS_DESON - Valor do ICMS Desonerado
- NFI_MOT_DES_ICMS - Motivo da Desoneração

### 2.2 Campos de ICMS ST (7 campos)
- NFI_V_BC_ICMS_ST - Base de Cálculo do ICMS ST
- NFI_P_ICMS_ST - Alíquota do ICMS ST (%)
- NFI_V_ICMS_ST - Valor do ICMS ST
- NFI_V_BC_ST_RET - Base de Cálculo do ST Retido
- NFI_V_ICMS_ST_RET - Valor do ICMS ST Retido
- NFI_P_ST - Alíquota do ST (%)
- NFI_V_ICMS_SUBST - Valor do ICMS Próprio do Substituto

### 2.3 Campos de FCP (5 campos)
- NFI_V_BC_FCP - Base de Cálculo do FCP
- NFI_P_FCP - Alíquota do FCP (%)
- NFI_V_FCP - Valor do FCP
- NFI_V_FCP_ST - Valor do FCP ST
- NFI_V_FCP_ST_RET - Valor do FCP ST Retido

### 2.4 Campos de CSOSN (1 campo)
- NFI_CSOSN - Código de Situação da Operação - Simples Nacional

**Total faltante nos ITENS: 18 campos**

---

## 3. Resumo Geral

- **CAPA:** 4 campos faltantes
- **ITENS:** 18 campos faltantes
- **TOTAL:** 22 campos faltantes

---

## 4. Scripts SQL Disponíveis

1. `add_missing_nfecom_capa_fields.sql` - Adiciona 4 campos na CAPA
2. `add_missing_nfecom_itens_fields.sql` - Adiciona 18 campos nos ITENS

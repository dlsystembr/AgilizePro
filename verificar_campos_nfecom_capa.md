# Verificação de Campos na Tabela nfecom_capa

## Campos Necessários (conforme especificação NFCom)

| Campo Especificação | Campo Banco | Status | Observação |
|---------------------|-------------|--------|------------|
| vProd_total | NFC_V_PROD | ✅ EXISTE | Valor Total dos produtos e serviços |
| vBC_total | NFC_V_BC_ICMS | ❌ **FALTA** | BC do ICMS |
| vICMS_total | NFC_V_ICMS | ❌ **FALTA** | Valor Total do ICMS |
| vICMSDeson_total | NFC_V_ICMS_DESON | ❌ **FALTA** | Valor Total do ICMS desonerado |
| vFCP_total | NFC_V_FCP | ❌ **FALTA** | Valor Total do FCP (Fundo de Combate à Pobreza) |
| vCOFINS_total | NFC_V_COFINS | ✅ EXISTE | Valor do COFINS |
| vPIS_total | NFC_V_PIS | ✅ EXISTE | Valor do PIS |
| vFUNTTEL_total | NFC_V_FUNTEL | ✅ EXISTE | Valor do FUNTTEL |
| vFUST_total | NFC_V_FUST | ✅ EXISTE | Valor do FUST |
| vRetTribTot_total | NFC_V_RET_TRIB_TOT | ✅ EXISTE | Total da retenção de tributos federais |
| vRetPIS_total | NFC_V_RET_PIS | ✅ EXISTE | Valor do PIS retido |
| vRetCofins_total | NFC_V_RET_COFINS | ✅ EXISTE | Valor do COFINS retido |
| vRetCSLL_total | NFC_V_RET_CSLL | ✅ EXISTE | Valor da CSLL retida |
| vIRRF_total | NFC_V_IRRF | ✅ EXISTE | Valor do IRRF retido |
| vDesc_total | NFC_V_DESC | ✅ EXISTE | Valor Total do Desconto |
| vOutro_total | NFC_V_OUTRO | ✅ EXISTE | Outras Despesas acessórias |
| vNF_total | NFC_V_NF | ✅ EXISTE | Valor Total da NFCom |

## Resumo

- **Total de campos necessários:** 17
- **Campos existentes:** 13 ✅
- **Campos faltantes:** 4 ❌

### Campos que precisam ser adicionados:

1. **NFC_V_BC_ICMS** - Base de Cálculo do ICMS (DECIMAL(15,2))
2. **NFC_V_ICMS** - Valor Total do ICMS (DECIMAL(15,2))
3. **NFC_V_ICMS_DESON** - Valor Total do ICMS Desonerado (DECIMAL(15,2))
4. **NFC_V_FCP** - Valor Total do FCP (DECIMAL(15,2))

## Script SQL para adicionar os campos faltantes

O arquivo `add_missing_nfecom_capa_fields.sql` contém os comandos ALTER TABLE para adicionar os campos faltantes.

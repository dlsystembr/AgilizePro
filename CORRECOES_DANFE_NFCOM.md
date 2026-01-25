# Correções Realizadas no DANFE da NFCom

## Problema Identificado
A base de cálculo estava aparecendo no DANFE mesmo quando estava zero no banco de dados, devido a fallbacks e cálculos incorretos.

## Correções Implementadas

### 1. **Remoção de Fallbacks nos Totais** ✅
**Arquivo:** `application/controllers/Nfecom.php` (linhas 1925 e 2174)

**Antes:**
```php
'valor_base_calculo' => floatval($nfecom->NFC_V_BC_ICMS ?? 0) > 0 ? floatval($nfecom->NFC_V_BC_ICMS ?? 0) : floatval($totalBasePis > 0 ? $totalBasePis : ($totalBaseCofins > 0 ? $totalBaseCofins : ($nfecom->NFC_V_PROD ?? 0)))
```

**Depois:**
```php
'valor_base_calculo' => floatval($nfecom->NFC_V_BC_ICMS ?? 0), // APENAS valor do banco, sem fallback
```

**Problema:** Estava usando `totalBasePis`, `totalBaseCofins` ou `NFC_V_PROD` como fallback quando `NFC_V_BC_ICMS` era zero.

---

### 2. **Remoção de Fallback na Classe NFComPreview** ✅
**Arquivo:** `application/libraries/NFComPreview.php` (linha 413)

**Antes:**
```php
['BASE CÁLCULO', $tot['valor_base_calculo'] ?? ($tot['valor_produtos'] ?? 0.00)],
```

**Depois:**
```php
['BASE CÁLCULO', $tot['valor_base_calculo'] ?? 0.00], // APENAS valor do banco, sem fallback
```

**Problema:** Estava usando `valor_produtos` como fallback quando `valor_base_calculo` não existia.

---

### 3. **Correção do Cálculo de Valor Unitário** ✅
**Arquivo:** `application/controllers/Nfecom.php` (linhas 1837 e 2085)

**Antes:**
```php
// Linha 1837
'valor_unitario' => ($item->NFI_Q_FATURADA > 0) ? (floatval($item->NFI_V_ITEM ?? 0) / floatval($item->NFI_Q_FATURADA)) : 0,

// Linha 2085
'valor_unitario' => floatval($item->NFI_V_ITEM ?? 0),
```

**Depois:**
```php
// Ambas as linhas agora usam:
'valor_unitario' => ($item->NFI_Q_FATURADA > 0 && $item->NFI_Q_FATURADA != 0) ? (floatval($item->NFI_V_PROD ?? 0) / floatval($item->NFI_Q_FATURADA)) : 0,
```

**Problema:** Estava usando `NFI_V_ITEM` (valor antes de desconto/outros) em vez de `NFI_V_PROD` (valor final).

---

### 4. **Remoção de Cálculo de Fallback na Classe NFComPreview** ✅
**Arquivo:** `application/libraries/NFComPreview.php` (linha 745)

**Antes:**
```php
$vUnit = $item['valor_unitario'] ?? ($qtd > 0 ? $vTotal / $qtd : 0.00);
```

**Depois:**
```php
// Usar APENAS valor_unitario do item (vem do banco), sem cálculo de fallback
$vUnit = $item['valor_unitario'] ?? 0.00;
```

**Problema:** Estava calculando `valor_unitario` dividindo `valor_total` por `quantidade` quando não existia, em vez de usar o valor do banco.

---

### 5. **Adição de Logs de Debug Detalhados** ✅
**Arquivo:** `application/controllers/Nfecom.php`

Adicionados logs que mostram:
- Todos os valores de ICMS, ICMS ST e FCP
- Valores de base de cálculo
- Totais calculados vs valores da capa

**Localização:**
- Linha ~1824: Log detalhado de cada item formatado
- Linha ~2073: Log detalhado de cada item na função `baixarDanfe()`
- Linha ~1954: Log dos totais calculados

---

## Regras Aplicadas

### ✅ **TODOS os valores devem vir do banco de dados:**
- **Base de cálculo:** `NFI_V_BC_ICMS` (itens) e `NFC_V_BC_ICMS` (capa)
- **Alíquota ICMS:** `NFI_P_ICMS` (itens)
- **Valor ICMS:** `NFI_V_ICMS` (itens) e `NFC_V_ICMS` (capa)
- **ICMS ST:** `NFI_V_BC_ICMS_ST`, `NFI_V_ICMS_ST` (itens)
- **FCP:** `NFI_V_BC_FCP`, `NFI_V_FCP` (itens) e `NFC_V_FCP` (capa)
- **Valor unitário:** Calculado apenas como `NFI_V_PROD / NFI_Q_FATURADA`

### ❌ **NÃO usar:**
- Fallbacks com outros valores quando o campo está zero
- Cálculos que substituem valores do banco
- Valores fixos ou defaults incorretos

---

## Como Verificar

1. **Verificar logs:** Os logs de debug mostrarão exatamente quais valores estão sendo usados
2. **Verificar banco:** Confirmar que os campos `NFI_V_BC_ICMS`, `NFI_V_ICMS`, etc. estão salvos corretamente
3. **Verificar DANFE:** O DANFE deve mostrar exatamente os valores do banco, sem cálculos ou fallbacks

---

---

### 6. **Correção de Valores Fixos no XML** ✅
**Arquivo:** `application/controllers/Nfecom.php` (linha 3145)

**Antes:**
```php
$xml .= '<ICMSTot><vBC>0.00</vBC><vICMS>0.00</vICMS><vICMSDeson>0.00</vICMSDeson><vFCP>0.00</vFCP></ICMSTot>' . "\n";
```

**Depois:**
```php
// Totais de ICMS - APENAS valores do banco, sem valores fixos
$xml .= '<ICMSTot>' . "\n";
$xml .= '<vBC>' . number_format(floatval($nfecom->NFC_V_BC_ICMS ?? 0), 2, '.', '') . '</vBC>' . "\n";
$xml .= '<vICMS>' . number_format(floatval($nfecom->NFC_V_ICMS ?? 0), 2, '.', '') . '</vICMS>' . "\n";
$xml .= '<vICMSDeson>' . number_format(floatval($nfecom->NFC_V_ICMS_DESON ?? 0), 2, '.', '') . '</vICMSDeson>' . "\n";
$xml .= '<vFCP>' . number_format(floatval($nfecom->NFC_V_FCP ?? 0), 2, '.', '') . '</vFCP>' . "\n";
$xml .= '</ICMSTot>' . "\n";
```

**Problema:** Estava usando valores fixos (0.00) em vez de usar os valores salvos no banco.

---

### 7. **Correção de Estrutura ICMS no XML dos Itens** ✅
**Arquivo:** `application/controllers/Nfecom.php` (linha 3132)

**Antes:**
```php
$xml .= '<ICMS40><CST>' . $item->NFI_CST_ICMS . '</CST></ICMS40>' . "\n";
```

**Depois:**
- Estrutura dinâmica baseada no CST
- Inclui valores de base, alíquota e valor quando existirem
- Adiciona ICMS ST e FCP quando houver valores

**Problema:** Estava sempre usando ICMS40 e não incluía os valores de base, alíquota e valor.

---

### 8. **Correção de Valores Fixos na Função gerarXmlPreEmissao** ✅
**Arquivo:** `application/controllers/Nfecom.php` (linha 3979)

**Antes:**
```php
'icms' => [
    'vBC' => 0.00,
    'vICMS' => 0.00,
    'vICMSDeson' => 0.00,
    'vFCP' => 0.00
],
```

**Depois:**
```php
'icms' => [
    'vBC' => floatval($nfecom->NFC_V_BC_ICMS ?? 0), // APENAS valor do banco
    'vICMS' => floatval($nfecom->NFC_V_ICMS ?? 0), // APENAS valor do banco
    'vICMSDeson' => floatval($nfecom->NFC_V_ICMS_DESON ?? 0), // APENAS valor do banco
    'vFCP' => floatval($nfecom->NFC_V_FCP ?? 0) // APENAS valor do banco
],
```

**Problema:** Estava usando valores fixos (0.00) em vez de usar os valores salvos no banco.

---

## Próximos Passos

1. Executar os scripts SQL para adicionar os campos faltantes (se ainda não executados)
2. Testar criando uma nova NFCom e verificar se os valores são salvos corretamente
3. Verificar o DANFE e comparar com os valores do banco
4. Verificar os logs para identificar qualquer problema restante
5. **IMPORTANTE:** Se a base de cálculo ainda aparecer incorreta no DANFE, verificar os logs de debug que foram adicionados para identificar a origem do problema

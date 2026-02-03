<?php
// Decodificador de Permissões Serializadas
// Cole o array serializado abaixo:

$serialized = 'a:92:{s:8:"aCliente";s:1:"1";s:8:"eCliente";s:1:"1";s:8:"dCliente";s:1:"1";s:8:"vCliente";s:1:"1";s:7:"aPessoa";s:1:"1";s:7:"ePessoa";s:1:"1";s:7:"dPessoa";s:1:"1";s:7:"vPessoa";s:1:"1";s:8:"aProduto";s:1:"1";s:8:"eProduto";s:1:"1";s:8:"dProduto";s:1:"1";s:8:"vProduto";s:1:"1";s:8:"aServico";s:1:"1";s:8:"eServico";s:1:"1";s:8:"dServico";s:1:"1";s:8:"vServico";s:1:"1";s:3:"aOs";s:1:"1";s:3:"eOs";s:1:"1";s:3:"dOs";s:1:"1";s:3:"vOs";s:1:"1";s:6:"aVenda";s:1:"1";s:6:"eVenda";s:1:"1";s:6:"dVenda";s:1:"1";s:6:"vVenda";s:1:"1";s:9:"aGarantia";s:1:"1";s:9:"eGarantia";s:1:"1";s:9:"dGarantia";s:1:"1";s:9:"vGarantia";s:1:"1";s:8:"aArquivo";s:1:"1";s:8:"eArquivo";s:1:"1";s:8:"dArquivo";s:1:"1";s:8:"vArquivo";s:1:"1";s:10:"aPagamento";N;s:10:"ePagamento";N;s:10:"dPagamento";N;s:10:"vPagamento";N;s:11:"aLancamento";s:1:"1";s:11:"eLancamento";s:1:"1";s:11:"dLancamento";s:1:"1";s:11:"vLancamento";s:1:"1";s:8:"cUsuario";s:1:"1";s:9:"cEmitente";s:1:"1";s:10:"cPermissao";s:1:"1";s:7:"cBackup";s:1:"1";s:10:"cAuditoria";s:1:"1";s:6:"cEmail";s:1:"1";s:8:"cSistema";s:1:"1";s:8:"rCliente";s:1:"1";s:8:"rProduto";s:1:"1";s:8:"rServico";s:1:"1";s:3:"rOs";s:1:"1";s:6:"rVenda";s:1:"1";s:11:"rFinanceiro";s:1:"1";s:4:"rNfe";s:1:"1";s:9:"aCobranca";s:1:"1";s:9:"eCobranca";s:1:"1";s:9:"dCobranca";s:1:"1";s:9:"vCobranca";s:1:"1";s:4:"vNfe";s:1:"1";s:4:"eNfe";s:1:"1";s:4:"vNcm";s:1:"1";s:4:"aNcm";s:1:"1";s:4:"eNcm";s:1:"1";s:4:"dNcm";s:1:"1";s:11:"vTributacao";s:1:"1";s:18:"vTributacaoProduto";s:1:"1";s:18:"aTributacaoProduto";s:1:"1";s:18:"eTributacaoProduto";s:1:"1";s:18:"dTributacaoProduto";s:1:"1";s:18:"vOperacaoComercial";s:1:"1";s:18:"aOperacaoComercial";s:1:"1";s:18:"eOperacaoComercial";s:1:"1";s:18:"dOperacaoComercial";s:1:"1";s:20:"vClassificacaoFiscal";s:1:"1";s:20:"aClassificacaoFiscal";s:1:"1";s:20:"eClassificacaoFiscal";s:1:"1";s:20:"dClassificacaoFiscal";s:1:"1";s:9:"vAliquota";s:1:"1";s:9:"aAliquota";s:1:"1";s:9:"eAliquota";N;s:9:"dAliquota";N;s:19:"vFaturamentoEntrada";s:1:"1";s:19:"aFaturamentoEntrada";s:1:"1";s:19:"eFaturamentoEntrada";s:1:"1";s:19:"dFaturamentoEntrada";s:1:"1";s:7:"vNfecom";s:1:"1";s:7:"aNfecom";s:1:"1";s:7:"eNfecom";s:1:"1";s:7:"dNfecom";s:1:"1";}';

$permissions = unserialize($serialized);

echo "<h2>Permissões Decodificadas</h2>";
echo "<p><strong>Total:</strong> " . count($permissions) . " permissões</p>";
echo "<hr>";

// Agrupar por tipo
$grouped = [
    'Visualizar (v)' => [],
    'Adicionar (a)' => [],
    'Editar (e)' => [],
    'Deletar (d)' => [],
    'Configurar (c)' => [],
    'Relatório (r)' => [],
];

foreach ($permissions as $code => $value) {
    $prefix = substr($code, 0, 1);
    $module = substr($code, 1);
    $status = $value === '1' ? '✓ Ativo' : '✗ Inativo';
    $color = $value === '1' ? 'green' : 'red';

    $item = "<span style='color: $color;'>$status</span> - <strong>$code</strong> ($module)";

    switch ($prefix) {
        case 'v':
            $grouped['Visualizar (v)'][] = $item;
            break;
        case 'a':
            $grouped['Adicionar (a)'][] = $item;
            break;
        case 'e':
            $grouped['Editar (e)'][] = $item;
            break;
        case 'd':
            $grouped['Deletar (d)'][] = $item;
            break;
        case 'c':
            $grouped['Configurar (c)'][] = $item;
            break;
        case 'r':
            $grouped['Relatório (r)'][] = $item;
            break;
    }
}

// Exibir agrupado
foreach ($grouped as $type => $items) {
    if (empty($items))
        continue;

    echo "<h3>$type</h3>";
    echo "<ul>";
    foreach ($items as $item) {
        echo "<li>$item</li>";
    }
    echo "</ul>";
}

// Verificar se cSistema está presente
echo "<hr>";
echo "<h3>Verificação Específica</h3>";
if (isset($permissions['cSistema'])) {
    $status = $permissions['cSistema'] === '1' ? 'ATIVA' : 'INATIVA';
    $color = $permissions['cSistema'] === '1' ? 'green' : 'red';
    echo "<p style='font-size: 18px;'><strong>Permissão cSistema:</strong> <span style='color: $color;'>$status</span></p>";
} else {
    echo "<p style='font-size: 18px; color: red;'><strong>Permissão cSistema:</strong> NÃO ENCONTRADA</p>";
}

// Listar permissões inativas
echo "<hr>";
echo "<h3>Permissões Inativas (NULL)</h3>";
$inactive = array_filter($permissions, function ($v) {
    return $v !== '1'; });
if (!empty($inactive)) {
    echo "<ul>";
    foreach ($inactive as $code => $value) {
        echo "<li style='color: orange;'><strong>$code</strong></li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: green;'>Todas as permissões estão ativas!</p>";
}

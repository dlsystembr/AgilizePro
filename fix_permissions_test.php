<?php
// Script para testar e corrigir deserialização de permissões
$conn = new mysqli('localhost', 'root', '', 'mapos');

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

echo "<h2>Teste de Deserialização de Permissões</h2>";
echo "<style>body { font-family: monospace; } .error { color: red; } .success { color: green; }</style>";

// Buscar perfil administrador
$sql = "SELECT idPermissao, nome, permissoes FROM permissoes WHERE idPermissao = 1";
$result = $conn->query($sql);
$perfil = $result->fetch_assoc();

echo "<h3>Perfil: {$perfil['nome']}</h3>";
echo "<p>Tamanho: " . strlen($perfil['permissoes']) . " bytes</p>";

// Mostrar primeiros e últimos caracteres
echo "<p><strong>Primeiros 100 caracteres:</strong><br>";
echo htmlspecialchars(substr($perfil['permissoes'], 0, 100)) . "</p>";

echo "<p><strong>Últimos 100 caracteres:</strong><br>";
echo htmlspecialchars(substr($perfil['permissoes'], -100)) . "</p>";

// Tentar deserializar
echo "<h3>Teste de Deserialização</h3>";

$data = $perfil['permissoes'];

// Teste 1
echo "<p><strong>Teste 1:</strong> unserialize() direto... ";
$test1 = @unserialize($data);
if ($test1 !== false && is_array($test1)) {
    echo "<span class='success'>✓ SUCESSO! (" . count($test1) . " itens)</span></p>";

    // Verificar cSistema
    if (isset($test1['cSistema'])) {
        echo "<p class='success'>✓ cSistema encontrado: " . ($test1['cSistema'] === '1' ? 'ATIVO' : 'INATIVO') . "</p>";
    } else {
        echo "<p class='error'>✗ cSistema NÃO encontrado</p>";
    }

    // Listar primeiras 10 permissões
    echo "<details><summary>Ver primeiras 10 permissões</summary><pre>";
    $count = 0;
    foreach ($test1 as $key => $value) {
        echo "$key => $value\n";
        if (++$count >= 10)
            break;
    }
    echo "</pre></details>";
} else {
    echo "<span class='error'>✗ FALHOU</span></p>";

    // Teste 2: Verificar se é um problema de encoding
    echo "<p><strong>Teste 2:</strong> Verificando encoding... ";
    $encoding = mb_detect_encoding($data, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
    echo "Encoding detectado: <strong>$encoding</strong></p>";

    // Teste 3: Tentar converter
    if ($encoding !== 'UTF-8') {
        echo "<p><strong>Teste 3:</strong> Convertendo para UTF-8... ";
        $converted = mb_convert_encoding($data, 'UTF-8', $encoding);
        $test3 = @unserialize($converted);
        if ($test3 !== false && is_array($test3)) {
            echo "<span class='success'>✓ SUCESSO após conversão!</span></p>";
        } else {
            echo "<span class='error'>✗ FALHOU mesmo após conversão</span></p>";
        }
    }

    // Teste 4: Verificar se há caracteres nulos ou especiais
    echo "<p><strong>Teste 4:</strong> Verificando caracteres especiais... ";
    $has_null = strpos($data, "\0") !== false;
    $has_special = preg_match('/[^\x20-\x7E\s]/', $data);
    echo "Null bytes: " . ($has_null ? 'SIM' : 'NÃO') . ", ";
    echo "Caracteres especiais: " . ($has_special ? 'SIM' : 'NÃO') . "</p>";

    // Teste 5: Criar um array válido e comparar
    echo "<h3>Solução: Recriar Permissões</h3>";
    echo "<p>Criando array de permissões válido...</p>";

    $valid_perms = [
        'aCliente' => '1',
        'eCliente' => '1',
        'dCliente' => '1',
        'vCliente' => '1',
        'aPessoa' => '1',
        'ePessoa' => '1',
        'dPessoa' => '1',
        'vPessoa' => '1',
        'aProduto' => '1',
        'eProduto' => '1',
        'dProduto' => '1',
        'vProduto' => '1',
        'aServico' => '1',
        'eServico' => '1',
        'dServico' => '1',
        'vServico' => '1',
        'aOs' => '1',
        'eOs' => '1',
        'dOs' => '1',
        'vOs' => '1',
        'aVenda' => '1',
        'eVenda' => '1',
        'dVenda' => '1',
        'vVenda' => '1',
        'aGarantia' => '1',
        'eGarantia' => '1',
        'dGarantia' => '1',
        'vGarantia' => '1',
        'aArquivo' => '1',
        'eArquivo' => '1',
        'dArquivo' => '1',
        'vArquivo' => '1',
        'aPagamento' => null,
        'ePagamento' => null,
        'dPagamento' => null,
        'vPagamento' => null,
        'aLancamento' => '1',
        'eLancamento' => '1',
        'dLancamento' => '1',
        'vLancamento' => '1',
        'cUsuario' => '1',
        'cEmitente' => '1',
        'cPermissao' => '1',
        'cBackup' => '1',
        'cAuditoria' => '1',
        'cEmail' => '1',
        'cSistema' => '1',
        'rCliente' => '1',
        'rProduto' => '1',
        'rServico' => '1',
        'rOs' => '1',
        'rVenda' => '1',
        'rFinanceiro' => '1',
        'rNfe' => '1',
        'aCobranca' => '1',
        'eCobranca' => '1',
        'dCobranca' => '1',
        'vCobranca' => '1',
        'vNfe' => '1',
        'eNfe' => '1',
        'vNcm' => '1',
        'aNcm' => '1',
        'eNcm' => '1',
        'dNcm' => '1',
        'vTributacao' => '1',
        'vTributacaoProduto' => '1',
        'aTributacaoProduto' => '1',
        'eTributacaoProduto' => '1',
        'dTributacaoProduto' => '1',
        'vOperacaoComercial' => '1',
        'aOperacaoComercial' => '1',
        'eOperacaoComercial' => '1',
        'dOperacaoComercial' => '1',
        'vClassificacaoFiscal' => '1',
        'aClassificacaoFiscal' => '1',
        'eClassificacaoFiscal' => '1',
        'dClassificacaoFiscal' => '1',
        'vAliquota' => '1',
        'aAliquota' => '1',
        'eAliquota' => null,
        'dAliquota' => null,
        'vFaturamentoEntrada' => '1',
        'aFaturamentoEntrada' => '1',
        'eFaturamentoEntrada' => '1',
        'dFaturamentoEntrada' => '1',
        'vNfecom' => '1',
        'aNfecom' => '1',
        'eNfecom' => '1',
        'dNfecom' => '1'
    ];

    $new_serialized = serialize($valid_perms);

    echo "<p>Novo array criado com " . count($valid_perms) . " permissões</p>";
    echo "<p>Tamanho do novo array serializado: " . strlen($new_serialized) . " bytes</p>";

    // Testar se o novo array funciona
    $test_new = unserialize($new_serialized);
    if ($test_new !== false && is_array($test_new)) {
        echo "<p class='success'>✓ Novo array é válido!</p>";

        echo "<form method='post' style='margin: 20px 0;'>";
        echo "<input type='hidden' name='fix' value='1'>";
        echo "<button type='submit' style='padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;'>APLICAR CORREÇÃO NO BANCO DE DADOS</button>";
        echo "</form>";
    }
}

// Processar correção
if (isset($_POST['fix'])) {
    $valid_perms = [
        'aCliente' => '1',
        'eCliente' => '1',
        'dCliente' => '1',
        'vCliente' => '1',
        'aPessoa' => '1',
        'ePessoa' => '1',
        'dPessoa' => '1',
        'vPessoa' => '1',
        'aProduto' => '1',
        'eProduto' => '1',
        'dProduto' => '1',
        'vProduto' => '1',
        'aServico' => '1',
        'eServico' => '1',
        'dServico' => '1',
        'vServico' => '1',
        'aOs' => '1',
        'eOs' => '1',
        'dOs' => '1',
        'vOs' => '1',
        'aVenda' => '1',
        'eVenda' => '1',
        'dVenda' => '1',
        'vVenda' => '1',
        'aGarantia' => '1',
        'eGarantia' => '1',
        'dGarantia' => '1',
        'vGarantia' => '1',
        'aArquivo' => '1',
        'eArquivo' => '1',
        'dArquivo' => '1',
        'vArquivo' => '1',
        'aPagamento' => null,
        'ePagamento' => null,
        'dPagamento' => null,
        'vPagamento' => null,
        'aLancamento' => '1',
        'eLancamento' => '1',
        'dLancamento' => '1',
        'vLancamento' => '1',
        'cUsuario' => '1',
        'cEmitente' => '1',
        'cPermissao' => '1',
        'cBackup' => '1',
        'cAuditoria' => '1',
        'cEmail' => '1',
        'cSistema' => '1',
        'rCliente' => '1',
        'rProduto' => '1',
        'rServico' => '1',
        'rOs' => '1',
        'rVenda' => '1',
        'rFinanceiro' => '1',
        'rNfe' => '1',
        'aCobranca' => '1',
        'eCobranca' => '1',
        'dCobranca' => '1',
        'vCobranca' => '1',
        'vNfe' => '1',
        'eNfe' => '1',
        'vNcm' => '1',
        'aNcm' => '1',
        'eNcm' => '1',
        'dNcm' => '1',
        'vTributacao' => '1',
        'vTributacaoProduto' => '1',
        'aTributacaoProduto' => '1',
        'eTributacaoProduto' => '1',
        'dTributacaoProduto' => '1',
        'vOperacaoComercial' => '1',
        'aOperacaoComercial' => '1',
        'eOperacaoComercial' => '1',
        'dOperacaoComercial' => '1',
        'vClassificacaoFiscal' => '1',
        'aClassificacaoFiscal' => '1',
        'eClassificacaoFiscal' => '1',
        'dClassificacaoFiscal' => '1',
        'vAliquota' => '1',
        'aAliquota' => '1',
        'eAliquota' => null,
        'dAliquota' => null,
        'vFaturamentoEntrada' => '1',
        'aFaturamentoEntrada' => '1',
        'eFaturamentoEntrada' => '1',
        'dFaturamentoEntrada' => '1',
        'vNfecom' => '1',
        'aNfecom' => '1',
        'eNfecom' => '1',
        'dNfecom' => '1'
    ];

    $new_serialized = serialize($valid_perms);

    $stmt = $conn->prepare("UPDATE permissoes SET permissoes = ? WHERE idPermissao = 1");
    $stmt->bind_param("s", $new_serialized);

    if ($stmt->execute()) {
        echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border: 1px solid #c3e6cb;'>";
        echo "<h3 style='color: #155724;'>✓ CORREÇÃO APLICADA COM SUCESSO!</h3>";
        echo "<p>O perfil Administrador foi atualizado com permissões válidas.</p>";
        echo "<p><a href='view_permissions.php'>Ver permissões atualizadas</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border: 1px solid #f5c6cb;'>";
        echo "<h3 style='color: #721c24;'>✗ ERRO AO APLICAR CORREÇÃO</h3>";
        echo "<p>" . $conn->error . "</p>";
        echo "</div>";
    }
}

$conn->close();

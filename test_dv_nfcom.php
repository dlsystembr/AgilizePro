<?php
/**
 * Script de Teste - Verificação do Dígito Verificador da NFCom
 * 
 * Este script testa se o cálculo do dígito verificador está correto
 * usando a chave fornecida pelo usuário como exemplo.
 */

// Função de cálculo do DV (módulo 11)
function calculateDV($chave) {
    $multiplicador = 2;
    $soma = 0;

    // Percorre a chave de trás para frente
    for ($i = strlen($chave) - 1; $i >= 0; $i--) {
        $soma += intval($chave[$i]) * $multiplicador;
        $multiplicador = ($multiplicador == 9) ? 2 : $multiplicador + 1;
    }

    $resto = $soma % 11;

    if ($resto == 0 || $resto == 1) {
        return 0;
    }

    return 11 - $resto;
}

// Teste com a chave fornecida pelo usuário
$chaveCompleta = '52260154314807000128620010000000021040813355';
$chaveSemDV = substr($chaveCompleta, 0, -1); // Remove o último dígito
$dvEsperado = substr($chaveCompleta, -1); // Pega o último dígito

echo "=== TESTE DE DÍGITO VERIFICADOR NFCom ===\n\n";
echo "Chave completa fornecida: $chaveCompleta\n";
echo "Chave sem DV: $chaveSemDV\n";
echo "DV esperado (da chave): $dvEsperado\n\n";

$dvCalculado = calculateDV($chaveSemDV);
echo "DV calculado: $dvCalculado\n\n";

if ($dvCalculado == $dvEsperado) {
    echo "✓ SUCESSO! O dígito verificador está CORRETO!\n";
} else {
    echo "✗ ERRO! O dígito verificador está INCORRETO!\n";
    echo "  Esperado: $dvEsperado\n";
    echo "  Calculado: $dvCalculado\n";
}

echo "\n=== DETALHAMENTO DA CHAVE ===\n";
echo "UF: " . substr($chaveCompleta, 0, 2) . "\n";
echo "AAMM: " . substr($chaveCompleta, 2, 4) . "\n";
echo "CNPJ: " . substr($chaveCompleta, 6, 14) . "\n";
echo "Modelo: " . substr($chaveCompleta, 20, 2) . "\n";
echo "Série: " . substr($chaveCompleta, 22, 3) . "\n";
echo "Número: " . substr($chaveCompleta, 25, 9) . "\n";
echo "Tipo Emissão: " . substr($chaveCompleta, 34, 1) . "\n";
echo "Código Numérico: " . substr($chaveCompleta, 35, 8) . "\n";
echo "DV: " . substr($chaveCompleta, 43, 1) . "\n";

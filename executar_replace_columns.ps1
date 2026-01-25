# Script PowerShell para executar o replace de colunas
# Uso: .\executar_replace_columns.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Replace de Colunas - Maiúsculas para Minúsculas" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se o PHP está disponível
$phpPath = "C:\xampp\php\php.exe"
if (-not (Test-Path $phpPath)) {
    Write-Host "ERRO: PHP não encontrado em $phpPath" -ForegroundColor Red
    Write-Host "Por favor, ajuste o caminho do PHP no script." -ForegroundColor Yellow
    exit 1
}

Write-Host "⚠️  IMPORTANTE: Faça backup do código antes de continuar!" -ForegroundColor Yellow
Write-Host ""
$confirm = Read-Host "Deseja continuar? (S/N)"

if ($confirm -ne "S" -and $confirm -ne "s") {
    Write-Host "Operação cancelada." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "Executando script de replace..." -ForegroundColor Green
Write-Host ""

# Executar o script PHP
& $phpPath fix_uppercase_columns_complete.php

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Processo concluído!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Pressione qualquer tecla para sair..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

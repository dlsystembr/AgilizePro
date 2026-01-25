# Script de Atualização do Servidor - AgilizePro
# Execute este script para atualizar o servidor via FTP

# Configurações FTP
$FtpServer = "ftp.agilizepro.dlsystem.com.br"
$FtpUser = "u204502606.dlsystem"
$FtpPass = "DiegoLucas@2024"
$RemotePath = "/"
$LocalPath = $PSScriptRoot

Write-Host ""
Write-Host "╔══════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   ATUALIZAÇÃO DO SERVIDOR - AgilizePro                  ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Perguntar tipo de atualização
Write-Host "Escolha o tipo de atualização:" -ForegroundColor Yellow
Write-Host "  1. Atualização GERAL (todos os arquivos modificados)" -ForegroundColor White
Write-Host "  2. Apenas Relatório de Contratos" -ForegroundColor White
Write-Host ""

$opcao = Read-Host "Digite a opção (1 ou 2)"

if ($opcao -eq "1") {
    Write-Host ""
    Write-Host "Iniciando atualização GERAL..." -ForegroundColor Green
    Write-Host ""
    & "$PSScriptRoot\atualizar_geral_via_ftp.ps1" -FtpServer $FtpServer -FtpUser $FtpUser -FtpPass $FtpPass -RemotePath $RemotePath -LocalPath $LocalPath
} elseif ($opcao -eq "2") {
    Write-Host ""
    Write-Host "Iniciando atualização do Relatório de Contratos..." -ForegroundColor Green
    Write-Host ""
    & "$PSScriptRoot\atualizar_geral_via_ftp.ps1" -FtpServer $FtpServer -FtpUser $FtpUser -FtpPass $FtpPass -RemotePath $RemotePath -LocalPath $LocalPath -ApenasRelatorio
} else {
    Write-Host "Opção inválida!" -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "Pressione qualquer tecla para sair..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

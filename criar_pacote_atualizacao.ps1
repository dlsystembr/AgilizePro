# Script PowerShell para criar pacote de atualização
# Relatório de Contratos - v1.0
# Execute: .\criar_pacote_atualizacao.ps1

$ErrorActionPreference = "Stop"

# Lista de arquivos para incluir no pacote
$arquivos = @(
    "application\controllers\Relatorios.php",
    "application\controllers\Permissoes.php",
    "application\models\Relatorios_model.php",
    "application\views\relatorios\rel_contratos.php",
    "application\views\relatorios\imprimir\imprimirContratos.php",
    "application\views\relatorios\imprimir\imprimirTopo.php",
    "application\views\permissoes\adicionarPermissao.php",
    "application\views\permissoes\editarPermissao.php",
    "application\views\permissoes\permissoes.php",
    "application\views\tema\topo.php",
    "application\views\menu.php",
    "application\config\permission.php",
    "application\helpers\mpdf_helper.php"
    # Opcional - descomente se precisar incluir correções do mPDF
    # "application\vendor\mpdf\mpdf\src\Language\ScriptToLanguage.php",
    # "application\vendor\mpdf\mpdf\src\Mpdf.php"
)

# Nome do pacote
$dataAtual = Get-Date -Format "yyyyMMdd_HHmmss"
$nomePacote = "atualizacao_relatorio_contratos_$dataAtual.zip"
$caminhoPacote = Join-Path $PSScriptRoot $nomePacote

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Criando Pacote de Atualização" -ForegroundColor Cyan
Write-Host "Relatório de Contratos v1.0" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se o arquivo ZIP já existe e removê-lo
if (Test-Path $caminhoPacote) {
    Remove-Item $caminhoPacote -Force
    Write-Host "Arquivo ZIP anterior removido." -ForegroundColor Yellow
}

# Criar arquivo ZIP
Write-Host "Criando arquivo ZIP..." -ForegroundColor Green
Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::Open($caminhoPacote, [System.IO.Compression.ZipArchiveMode]::Create)

$arquivosEncontrados = 0
$arquivosNaoEncontrados = @()

foreach ($arquivo in $arquivos) {
    $caminhoCompleto = Join-Path $PSScriptRoot $arquivo
    
    if (Test-Path $caminhoCompleto) {
        # Adicionar arquivo ao ZIP mantendo a estrutura de pastas
        $entradaZip = $zip.CreateEntry($arquivo)
        $stream = $entradaZip.Open()
        $conteudo = [System.IO.File]::ReadAllBytes($caminhoCompleto)
        $stream.Write($conteudo, 0, $conteudo.Length)
        $stream.Close()
        
        $arquivosEncontrados++
        Write-Host "  ✓ $arquivo" -ForegroundColor Green
    } else {
        $arquivosNaoEncontrados += $arquivo
        Write-Host "  ✗ $arquivo (NÃO ENCONTRADO)" -ForegroundColor Red
    }
}

$zip.Dispose()

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Resumo" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Arquivos encontrados: $arquivosEncontrados" -ForegroundColor Green
Write-Host "Arquivos não encontrados: $($arquivosNaoEncontrados.Count)" -ForegroundColor $(if ($arquivosNaoEncontrados.Count -gt 0) { "Red" } else { "Green" })

if ($arquivosNaoEncontrados.Count -gt 0) {
    Write-Host ""
    Write-Host "Arquivos não encontrados:" -ForegroundColor Yellow
    foreach ($arquivo in $arquivosNaoEncontrados) {
        Write-Host "  - $arquivo" -ForegroundColor Yellow
    }
}

$tamanhoArquivo = (Get-Item $caminhoPacote).Length / 1MB
Write-Host ""
Write-Host "Pacote criado com sucesso!" -ForegroundColor Green
Write-Host "Localização: $caminhoPacote" -ForegroundColor Cyan
Write-Host "Tamanho: $([math]::Round($tamanhoArquivo, 2)) MB" -ForegroundColor Cyan
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Próximos Passos:" -ForegroundColor Yellow
Write-Host "1. Faça backup do servidor antes de atualizar" -ForegroundColor White
Write-Host "2. Extraia o ZIP no servidor mantendo a estrutura" -ForegroundColor White
Write-Host "3. Execute a migração do banco de dados" -ForegroundColor White
Write-Host "4. Verifique as permissões no sistema" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan

# Perguntar se deseja abrir a pasta
$resposta = Read-Host "`nDeseja abrir a pasta do pacote? (S/N)"
if ($resposta -eq "S" -or $resposta -eq "s") {
    Start-Process "explorer.exe" -ArgumentList "/select,`"$caminhoPacote`""
}

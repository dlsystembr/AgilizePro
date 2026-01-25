# Script PowerShell para atualização via FTP
# Compara arquivos locais com servidor e faz upload apenas dos modificados
# Relatório de Contratos - Atualização Geral

param(
    [string]$FtpServer = "ftp.agilizepro.dlsystem.com.br",
    [string]$FtpUser = "u204502606.dlsystem",
    [string]$FtpPass = "DiegoLucas@2024",
    [string]$RemotePath = "/",
    [string]$LocalPath = $PSScriptRoot
)

$ErrorActionPreference = "Stop"

# Arquivos e pastas a IGNORAR (específicos do cliente)
$arquivosIgnorar = @(
    ".env",
    ".env.local",
    ".env.production",
    "application\.env",
    "application\config\config.php",
    "application\config\database.php",
    "application\config\email.php",
    "application\logs\*",
    "assets\uploads\*",
    "assets\anexos\*",
    "assets\arquivos\*",
    "assets\userImage\*",
    "vendor\*",
    "application\vendor\*",
    "node_modules\*",
    ".git\*",
    ".gitignore",
    "*.log",
    "*.sql",
    "*.md",
    "*.txt",
    "*.ps1",
    "*.bat",
    "*.sh"
)

# Lista de arquivos para atualizar (apenas os modificados para o relatório)
$arquivosAtualizar = @(
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
)

# Função para verificar se arquivo deve ser ignorado
function DeveIgnorar {
    param([string]$caminho)
    
    foreach ($padrao in $arquivosIgnorar) {
        $padraoRegex = $padrao -replace '\*', '.*' -replace '\\', '\\'
        if ($caminho -match $padraoRegex) {
            return $true
        }
    }
    return $false
}

# Função para criar conexão FTP
function CriarConexaoFtp {
    $ftpUri = "ftp://$FtpServer$RemotePath"
    $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
    $ftpRequest.UseBinary = $true
    $ftpRequest.UsePassive = $true
    return $ftpRequest
}

# Função para listar arquivos no servidor FTP
function ListarArquivosFtp {
    param([string]$caminho = "")
    
    $arquivos = @{}
    try {
        $ftpUri = "ftp://$FtpServer$RemotePath$caminho"
        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
        $ftpRequest.UsePassive = $true
        
        $response = $ftpRequest.GetResponse()
        $responseStream = $response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($responseStream)
        
        while ($null -ne ($linha = $reader.ReadLine())) {
            if ($linha -match "(\d{2}-\d{2}-\d{2,?\s+\d{1,2}:\d{2}(AM|PM)?)\s+(.+)$") {
                $nomeArquivo = $matches[3]
                $dataModificacao = $matches[1]
                $arquivos[$nomeArquivo] = $dataModificacao
            }
        }
        
        $reader.Close()
        $response.Close()
    } catch {
        Write-Host "  Aviso: Não foi possível listar $caminho - $($_.Exception.Message)" -ForegroundColor Yellow
    }
    
    return $arquivos
}

# Função para obter data de modificação do arquivo no FTP
function ObterDataModificacaoFtp {
    param([string]$caminho)
    
    try {
        $ftpUri = "ftp://$FtpServer$RemotePath$caminho"
        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::GetDateTimestamp
        $ftpRequest.UsePassive = $true
        
        $response = $ftpRequest.GetResponse()
        $dataModificacao = $response.LastModified
        $response.Close()
        
        return $dataModificacao
    } catch {
        return $null
    }
}

# Função para fazer upload de arquivo
function FazerUpload {
    param(
        [string]$arquivoLocal,
        [string]$arquivoRemoto
    )
    
    try {
        $ftpUri = "ftp://$FtpServer$RemotePath$arquivoRemoto"
        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        
        $fileContent = [System.IO.File]::ReadAllBytes($arquivoLocal)
        $ftpRequest.ContentLength = $fileContent.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        
        $response = $ftpRequest.GetResponse()
        $response.Close()
        
        return $true
    } catch {
        Write-Host "  ERRO ao fazer upload: $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# Função para criar diretório no FTP
function CriarDiretorioFtp {
    param([string]$caminho)
    
    try {
        $ftpUri = "ftp://$FtpServer$RemotePath$caminho"
        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $ftpRequest.UsePassive = $true
        
        $response = $ftpRequest.GetResponse()
        $response.Close()
        return $true
    } catch {
        # Diretório pode já existir
        return $false
    }
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Atualização via FTP - Relatório Contratos" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Servidor: $FtpServer" -ForegroundColor White
Write-Host "Usuário: $FtpUser" -ForegroundColor White
Write-Host "Caminho Local: $LocalPath" -ForegroundColor White
Write-Host "Caminho Remoto: $RemotePath" -ForegroundColor White
Write-Host ""

# Confirmar antes de continuar
$confirmar = Read-Host "Deseja continuar com a atualização? (S/N)"
if ($confirmar -ne "S" -and $confirmar -ne "s") {
    Write-Host "Operação cancelada." -ForegroundColor Yellow
    exit
}

Write-Host ""
Write-Host "Comparando arquivos..." -ForegroundColor Green

$arquivosParaUpload = @()
$arquivosIgnorados = @()
$erros = @()

foreach ($arquivo in $arquivosAtualizar) {
    $caminhoLocal = Join-Path $LocalPath $arquivo
    
    # Verificar se deve ignorar
    if (DeveIgnorar $arquivo) {
        $arquivosIgnorados += $arquivo
        Write-Host "  ⊘ IGNORADO: $arquivo" -ForegroundColor Gray
        continue
    }
    
    # Verificar se arquivo existe localmente
    if (-not (Test-Path $caminhoLocal)) {
        Write-Host "  ✗ NÃO ENCONTRADO: $arquivo" -ForegroundColor Red
        $erros += $arquivo
        continue
    }
    
    # Obter informações do arquivo local
    $infoLocal = Get-Item $caminhoLocal
    $tamanhoLocal = $infoLocal.Length
    $dataLocal = $infoLocal.LastWriteTime
    
    # Converter caminho para formato FTP (usar / ao invés de \)
    $arquivoRemoto = $arquivo -replace '\\', '/'
    
    # Verificar se arquivo existe no servidor
    $dataRemota = ObterDataModificacaoFtp $arquivoRemoto
    
    $precisaUpload = $false
    
    if ($null -eq $dataRemota) {
        # Arquivo não existe no servidor
        $precisaUpload = $true
        Write-Host "  + NOVO: $arquivo" -ForegroundColor Cyan
    } elseif ($dataLocal -gt $dataRemota) {
        # Arquivo local é mais recente
        $precisaUpload = $true
        Write-Host "  ↻ ATUALIZAR: $arquivo (Local: $($dataLocal.ToString('dd/MM/yyyy HH:mm')), Remoto: $($dataRemota.ToString('dd/MM/yyyy HH:mm')))" -ForegroundColor Yellow
    } else {
        Write-Host "  ✓ OK: $arquivo (já está atualizado)" -ForegroundColor Green
    }
    
    if ($precisaUpload) {
        $arquivosParaUpload += @{
            Local = $caminhoLocal
            Remoto = $arquivoRemoto
            Nome = $arquivo
        }
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Resumo" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Arquivos para upload: $($arquivosParaUpload.Count)" -ForegroundColor $(if ($arquivosParaUpload.Count -gt 0) { "Yellow" } else { "Green" })
Write-Host "Arquivos ignorados: $($arquivosIgnorados.Count)" -ForegroundColor Gray
Write-Host "Arquivos não encontrados: $($erros.Count)" -ForegroundColor $(if ($erros.Count -gt 0) { "Red" } else { "Green" })
Write-Host ""

if ($arquivosParaUpload.Count -eq 0) {
    Write-Host "Nenhum arquivo precisa ser atualizado!" -ForegroundColor Green
    exit
}

# Confirmar upload
$confirmarUpload = Read-Host "Deseja fazer upload de $($arquivosParaUpload.Count) arquivo(s)? (S/N)"
if ($confirmarUpload -ne "S" -and $confirmarUpload -ne "s") {
    Write-Host "Upload cancelado." -ForegroundColor Yellow
    exit
}

Write-Host ""
Write-Host "Fazendo upload..." -ForegroundColor Green

$sucesso = 0
$falhas = 0

foreach ($item in $arquivosParaUpload) {
    Write-Host "  ↑ Enviando: $($item.Nome)..." -ForegroundColor Cyan -NoNewline
    
    # Criar diretórios necessários
    $diretorioRemoto = Split-Path $item.Remoto -Parent
    if ($diretorioRemoto -and $diretorioRemoto -ne ".") {
        $partes = $diretorioRemoto -split '/'
        $caminhoAtual = ""
        foreach ($parte in $partes) {
            if ($parte) {
                $caminhoAtual += "/$parte"
                CriarDiretorioFtp $caminhoAtual | Out-Null
            }
        }
    }
    
    if (FazerUpload $item.Local $item.Remoto) {
        Write-Host " ✓" -ForegroundColor Green
        $sucesso++
    } else {
        Write-Host " ✗" -ForegroundColor Red
        $falhas++
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Resultado Final" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Sucesso: $sucesso" -ForegroundColor Green
Write-Host "Falhas: $falhas" -ForegroundColor $(if ($falhas -gt 0) { "Red" } else { "Green" })
Write-Host ""

if ($falhas -eq 0) {
    Write-Host "Atualização concluída com sucesso!" -ForegroundColor Green
    Write-Host ""
    Write-Host "PRÓXIMOS PASSOS:" -ForegroundColor Yellow
    Write-Host "1. Acesse o sistema como administrador" -ForegroundColor White
    Write-Host "2. Vá em: Configurações > Sistema" -ForegroundColor White
    Write-Host "3. Clique em: 'Atualizar Banco de Dados'" -ForegroundColor White
    Write-Host "4. Verifique as permissões do relatório" -ForegroundColor White
} else {
    Write-Host "Alguns arquivos falharam no upload. Verifique os erros acima." -ForegroundColor Red
}

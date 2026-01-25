# Script PowerShell para atualização GERAL via FTP
# Compara TODOS os arquivos locais com servidor e faz upload dos modificados
# Ignora arquivos sensíveis específicos do cliente

param(
    [string]$FtpServer = "ftp.agilizepro.dlsystem.com.br",
    [string]$FtpUser = "u204502606.dlsystem",
    [string]$FtpPass = "DiegoLucas@2024",
    [string]$RemotePath = "/",
    [string]$LocalPath = $PSScriptRoot,
    [switch]$ApenasRelatorio = $false
)

$ErrorActionPreference = "Continue"

# Arquivos e pastas a IGNORAR (específicos do cliente)
$arquivosIgnorar = @(
    "\.env",
    "\.env\.local",
    "\.env\.production",
    "application\\config\\config\.php",
    "application\\config\\database\.php",
    "application\\config\\email\.php",
    "application\\logs",
    "assets\\uploads",
    "assets\\anexos",
    "assets\\arquivos",
    "assets\\userImage",
    "vendor",
    "application\\vendor",
    "node_modules",
    "\.git",
    "\.gitignore",
    "\.gitattributes",
    "\.DS_Store",
    "\.idea",
    "\.vscode",
    "\.php_cs\.cache",
    "\.php-cs-fixer\.cache",
    "ci_sessions",
    "\.log$",
    "\.sql$",
    "\.md$",
    "\.txt$",
    "\.ps1$",
    "\.bat$",
    "\.sh$",
    "\.zip$",
    "atualizar.*\.ps1",
    "listar_arquivos.*\.php",
    "criar_pacote.*\.ps1",
    "ATUALIZACAO.*\.md",
    "INSTRUCOES.*\.txt"
)

# Lista específica para atualização apenas do relatório (se usar -ApenasRelatorio)
$arquivosRelatorio = @(
    "application\\controllers\\Relatorios\.php",
    "application\\controllers\\Permissoes\.php",
    "application\\models\\Relatorios_model\.php",
    "application\\views\\relatorios",
    "application\\views\\permissoes",
    "application\\views\\tema\\topo\.php",
    "application\\views\\menu\.php",
    "application\\config\\permission\.php",
    "application\\helpers\\mpdf_helper\.php"
)

# Função para verificar se arquivo deve ser ignorado
function DeveIgnorar {
    param([string]$caminho)
    
    # Converter caminho para formato de comparação
    $caminhoNormalizado = $caminho -replace '/', '\\'
    
    # Se modo apenas relatório, verificar se está na lista
    if ($ApenasRelatorio) {
        $estaNaLista = $false
        foreach ($padrao in $arquivosRelatorio) {
            if ($caminhoNormalizado -match $padrao) {
                $estaNaLista = $true
                break
            }
        }
        if (-not $estaNaLista) {
            return $true
        }
    }
    
    # Verificar padrões de ignorar
    foreach ($padrao in $arquivosIgnorar) {
        if ($caminhoNormalizado -match $padrao) {
            return $true
        }
    }
    
    return $false
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
        $ftpRequest.Timeout = 10000
        
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
        # Criar diretórios necessários primeiro
        $diretorioRemoto = Split-Path $arquivoRemoto -Parent
        if ($diretorioRemoto -and $diretorioRemoto -ne "." -and $diretorioRemoto -ne "/") {
            $partes = $diretorioRemoto -split '/'
            $caminhoAtual = ""
            foreach ($parte in $partes) {
                if ($parte) {
                    $caminhoAtual += "/$parte"
                    try {
                        $ftpUri = "ftp://$FtpServer$RemotePath$caminhoAtual"
                        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
                        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
                        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
                        $ftpRequest.UsePassive = $true
                        $ftpRequest.Timeout = 5000
                        $response = $ftpRequest.GetResponse()
                        $response.Close()
                    } catch {
                        # Diretório pode já existir, ignorar erro
                    }
                }
            }
        }
        
        # Fazer upload do arquivo
        $ftpUri = "ftp://$FtpServer$RemotePath$arquivoRemoto"
        $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        $ftpRequest.Timeout = 30000
        
        $fileContent = [System.IO.File]::ReadAllBytes($arquivoLocal)
        $ftpRequest.ContentLength = $fileContent.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        
        $response = $ftpRequest.GetResponse()
        $statusCode = $response.StatusCode
        $response.Close()
        
        return $true
    } catch {
        return $false
    }
}

# Função para obter todos os arquivos recursivamente
function ObterArquivosRecursivo {
    param(
        [string]$caminho,
        [string]$basePath
    )
    
    $arquivos = @()
    
    try {
        $itens = Get-ChildItem -Path $caminho -File -ErrorAction SilentlyContinue
        foreach ($item in $itens) {
            $caminhoRelativo = $item.FullName.Substring($basePath.Length + 1)
            $arquivos += $caminhoRelativo
        }
        
        $diretorios = Get-ChildItem -Path $caminho -Directory -ErrorAction SilentlyContinue
        foreach ($dir in $diretorios) {
            $arquivos += ObterArquivosRecursivo $dir.FullName $basePath
        }
    } catch {
        # Ignorar erros de acesso
    }
    
    return $arquivos
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Atualização GERAL via FTP" -ForegroundColor Cyan
if ($ApenasRelatorio) {
    Write-Host "Modo: Apenas Relatório de Contratos" -ForegroundColor Yellow
} else {
    Write-Host "Modo: Atualização Geral" -ForegroundColor Green
}
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Servidor: $FtpServer" -ForegroundColor White
Write-Host "Usuário: $FtpUser" -ForegroundColor White
Write-Host "Caminho Local: $LocalPath" -ForegroundColor White
Write-Host "Caminho Remoto: $RemotePath" -ForegroundColor White
Write-Host ""

# Confirmar antes de continuar
$confirmar = Read-Host "Deseja continuar? (S/N)"
if ($confirmar -ne "S" -and $confirmar -ne "s") {
    Write-Host "Operação cancelada." -ForegroundColor Yellow
    exit
}

Write-Host ""
Write-Host "Escaneando arquivos locais..." -ForegroundColor Green

# Obter todos os arquivos
$todosArquivos = ObterArquivosRecursivo $LocalPath $LocalPath

Write-Host "Total de arquivos encontrados: $($todosArquivos.Count)" -ForegroundColor Cyan
Write-Host ""

Write-Host "Comparando com servidor..." -ForegroundColor Green

$arquivosParaUpload = @()
$arquivosIgnorados = @()
$arquivosAtualizados = @()
$erros = @()
$contador = 0

foreach ($arquivo in $todosArquivos) {
    $contador++
    $porcentagem = [math]::Round(($contador / $todosArquivos.Count) * 100, 1)
    Write-Progress -Activity "Comparando arquivos" -Status "$contador de $($todosArquivos.Count) ($porcentagem%)" -PercentComplete $porcentagem
    
    # Verificar se deve ignorar
    if (DeveIgnorar $arquivo) {
        $arquivosIgnorados += $arquivo
        continue
    }
    
    $caminhoLocal = Join-Path $LocalPath $arquivo
    
    # Verificar se arquivo existe localmente
    if (-not (Test-Path $caminhoLocal)) {
        $erros += $arquivo
        continue
    }
    
    # Obter informações do arquivo local
    $infoLocal = Get-Item $caminhoLocal
    $dataLocal = $infoLocal.LastWriteTime
    
    # Converter caminho para formato FTP (usar / ao invés de \)
    $arquivoRemoto = $arquivo -replace '\\', '/'
    
    # Verificar se arquivo existe no servidor
    $dataRemota = ObterDataModificacaoFtp $arquivoRemoto
    
    $precisaUpload = $false
    
    if ($null -eq $dataRemota) {
        # Arquivo não existe no servidor
        $precisaUpload = $true
    } elseif ($dataLocal -gt $dataRemota) {
        # Arquivo local é mais recente
        $precisaUpload = $true
    } else {
        $arquivosAtualizados += $arquivo
    }
    
    if ($precisaUpload) {
        $arquivosParaUpload += @{
            Local = $caminhoLocal
            Remoto = $arquivoRemoto
            Nome = $arquivo
        }
    }
}

Write-Progress -Activity "Comparando arquivos" -Completed

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Resumo da Comparação" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Arquivos para upload: $($arquivosParaUpload.Count)" -ForegroundColor $(if ($arquivosParaUpload.Count -gt 0) { "Yellow" } else { "Green" })
Write-Host "Arquivos já atualizados: $($arquivosAtualizados.Count)" -ForegroundColor Green
Write-Host "Arquivos ignorados: $($arquivosIgnorados.Count)" -ForegroundColor Gray
Write-Host "Arquivos com erro: $($erros.Count)" -ForegroundColor $(if ($erros.Count -gt 0) { "Red" } else { "Green" })
Write-Host ""

if ($arquivosParaUpload.Count -eq 0) {
    Write-Host "Nenhum arquivo precisa ser atualizado!" -ForegroundColor Green
    exit
}

# Mostrar alguns arquivos que serão atualizados
Write-Host "Arquivos que serão atualizados (primeiros 10):" -ForegroundColor Cyan
$arquivosParaUpload | Select-Object -First 10 | ForEach-Object {
    Write-Host "  • $($_.Nome)" -ForegroundColor White
}
if ($arquivosParaUpload.Count -gt 10) {
    Write-Host "  ... e mais $($arquivosParaUpload.Count - 10) arquivo(s)" -ForegroundColor Gray
}
Write-Host ""

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
$contador = 0

foreach ($item in $arquivosParaUpload) {
    $contador++
    $porcentagem = [math]::Round(($contador / $arquivosParaUpload.Count) * 100, 1)
    Write-Progress -Activity "Fazendo upload" -Status "$contador de $($arquivosParaUpload.Count) ($porcentagem%) - $($item.Nome)" -PercentComplete $porcentagem
    
    if (FazerUpload $item.Local $item.Remoto) {
        $sucesso++
    } else {
        Write-Host "  ✗ Falha: $($item.Nome)" -ForegroundColor Red
        $falhas++
    }
}

Write-Progress -Activity "Fazendo upload" -Completed

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Resultado Final" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Sucesso: $sucesso" -ForegroundColor Green
Write-Host "Falhas: $falhas" -ForegroundColor $(if ($falhas -gt 0) { "Red" } else { "Green" })
Write-Host ""

if ($falhas -eq 0) {
    Write-Host "✓ Atualização concluída com sucesso!" -ForegroundColor Green
    Write-Host ""
    Write-Host "PRÓXIMOS PASSOS:" -ForegroundColor Yellow
    Write-Host "1. Acesse o sistema como administrador" -ForegroundColor White
    Write-Host "2. Vá em: Configurações > Sistema" -ForegroundColor White
    Write-Host "3. Clique em: 'Atualizar Banco de Dados'" -ForegroundColor White
    if ($ApenasRelatorio) {
        Write-Host "4. Verifique as permissões do relatório de contratos" -ForegroundColor White
    }
} else {
    Write-Host "⚠ Alguns arquivos falharam no upload. Verifique os erros acima." -ForegroundColor Red
}

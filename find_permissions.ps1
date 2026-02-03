# Script PowerShell para encontrar verificações de permissão em um controller

# Uso: .\find_permissions.ps1 NomeDoController

param(
    [string]$Controller = "Mapos"
)

$file = "c:\xampp\htdocs\mapos\application\controllers\$Controller.php"

if (Test-Path $file) {
    Write-Host "Buscando verificações de permissão em $Controller.php..." -ForegroundColor Cyan
    Write-Host ""
    
    $content = Get-Content $file
    $lineNumber = 0
    
    foreach ($line in $content) {
        $lineNumber++
        if ($line -match "checkPermission.*'([^']+)'") {
            $permission = $matches[1]
            Write-Host "Linha $lineNumber : Permissão '$permission'" -ForegroundColor Green
            Write-Host "  $line" -ForegroundColor Gray
            Write-Host ""
        }
    }
} else {
    Write-Host "Controller não encontrado: $file" -ForegroundColor Red
}

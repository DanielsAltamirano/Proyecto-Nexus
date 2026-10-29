<#
.SYNOPSIS
    Construye, publica en el registro e imprime el digest SHA256 exacto de las imágenes de Nexus ERP.
.EXAMPLE
    .\scripts\build-images.ps1 -RegistryUser "miusuario"
.EXAMPLE
    .\scripts\build-images.ps1 -RegistryUser "ghcr.io/miusuario" -Version "1.0.0"
#>
param(
    [Parameter(Mandatory = $true, Position = 0, HelpMessage = "Usuario de Docker Hub o ruta de GHCR (ej. 'miusuario' o 'ghcr.io/miusuario')")]
    [ValidateNotNullOrEmpty()]
    [string]$RegistryUser,

    [Parameter(Mandatory = $false, Position = 1)]
    [string]$Version = "1.0.0"
)

$ErrorActionPreference = "Stop"

$RegistryUser = $RegistryUser.TrimEnd('/')
$ProjectRoot = Split-Path -Parent $PSScriptRoot

$PhpImage   = "${RegistryUser}/nexus-php:${Version}"
$NginxImage = "${RegistryUser}/nexus-nginx:${Version}"

Push-Location $ProjectRoot
try {
    Write-Host "==================================================================" -ForegroundColor Cyan
    Write-Host " 1. Construyendo imagen PHP-FPM (No-Root): $PhpImage" -ForegroundColor Cyan
    Write-Host "==================================================================" -ForegroundColor Cyan
    docker build -f Dockerfile.php -t $PhpImage .
    if ($LASTEXITCODE -ne 0) { throw "Error al construir $PhpImage" }

    Write-Host "==================================================================" -ForegroundColor Cyan
    Write-Host " 2. Construyendo imagen Nginx (No-Root): $NginxImage" -ForegroundColor Cyan
    Write-Host "==================================================================" -ForegroundColor Cyan
    docker build -f Dockerfile.nginx -t $NginxImage .
    if ($LASTEXITCODE -ne 0) { throw "Error al construir $NginxImage" }

    Write-Host "==================================================================" -ForegroundColor Cyan
    Write-Host " 3. Publicando imágenes en el registro (docker push)" -ForegroundColor Cyan
    Write-Host "==================================================================" -ForegroundColor Cyan
    docker push $PhpImage
    if ($LASTEXITCODE -ne 0) { throw "Error al hacer push de $PhpImage" }

    docker push $NginxImage
    if ($LASTEXITCODE -ne 0) { throw "Error al hacer push de $NginxImage" }

    $PhpDigest   = docker inspect --format="{{if .RepoDigests}}{{index .RepoDigests 0}}{{else}}{{.Id}}{{end}}" $PhpImage
    $NginxDigest = docker inspect --format="{{if .RepoDigests}}{{index .RepoDigests 0}}{{else}}{{.Id}}{{end}}" $NginxImage

    Write-Host ""
    Write-Host "==================================================================" -ForegroundColor Green
    Write-Host " RESUMEN DE IMÁGENES INMUTABLES Y DIGESTS SHA256" -ForegroundColor Green
    Write-Host "==================================================================" -ForegroundColor Green
    Write-Host "PHP-FPM Image  : $PhpImage"
    Write-Host "PHP-FPM Digest : $PhpDigest"
    Write-Host "------------------------------------------------------------------"
    Write-Host "Nginx Image    : $NginxImage"
    Write-Host "Nginx Digest   : $NginxDigest"
    Write-Host "==================================================================" -ForegroundColor Green
}
finally {
    Pop-Location
}

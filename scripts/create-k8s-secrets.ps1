<#
.SYNOPSIS
    Genera de forma segura e idempotente los Secrets de Kubernetes para Nexus ERP
    (mysql-secret, mongodb-secret, nexus-app-secret y el Secret TLS del Ingress)
    cumpliendo con la regla de Cero Secretos en Git.

.EXAMPLE
    .\scripts\create-k8s-secrets.ps1 -Environment dev
.EXAMPLE
    .\scripts\create-k8s-secrets.ps1 -Environment prod -KubeContext "k8s-prod"
#>
param(
    [Parameter(Mandatory = $false, Position = 0)]
    [ValidateSet("dev", "prod")]
    [string]$Environment = "dev",

    [Parameter(Mandatory = $false, Position = 1)]
    [string]$KubeContext = ""
)

$ErrorActionPreference = "Stop"

if ($Environment -eq "dev") {
    $Namespace     = "nexus-dev"
    $IngressHost   = "dev.nexus.internal"
    $TlsSecretName = "dev-nexus-tls"
} else {
    $Namespace     = "nexus-prod"
    $IngressHost   = "prod.nexus.internal"
    $TlsSecretName = "prod-nexus-tls"
}

$KubeArgs = @()
if (-not [string]::IsNullOrWhiteSpace($KubeContext)) {
    $KubeArgs += "--context=$KubeContext"
}

function New-RandomHex {
    param([int]$Bytes = 20)
    $rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    $buffer = New-Object byte[] $Bytes
    $rng.GetBytes($buffer)
    return ([System.BitConverter]::ToString($buffer) -replace '-', '').ToLower()
}

function New-RandomBase64 {
    param([int]$Bytes = 32)
    $rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    $buffer = New-Object byte[] $Bytes
    $rng.GetBytes($buffer)
    return [Convert]::ToBase64String($buffer)
}

Write-Host "==================================================================" -ForegroundColor Cyan
Write-Host " Generando Secrets para ambiente: $Environment (Namespace: $Namespace)" -ForegroundColor Cyan
Write-Host "==================================================================" -ForegroundColor Cyan

# 1. Asegurar Namespace con etiquetas Pod Security Restricted
kubectl @KubeArgs create namespace $Namespace --dry-run=client -o yaml | kubectl @KubeArgs apply -f -

kubectl @KubeArgs label namespace $Namespace `
    pod-security.kubernetes.io/enforce=restricted `
    pod-security.kubernetes.io/enforce-version=latest `
    pod-security.kubernetes.io/audit=restricted `
    pod-security.kubernetes.io/audit-version=latest `
    pod-security.kubernetes.io/warn=restricted `
    pod-security.kubernetes.io/warn-version=latest `
    --overwrite

# 2. Generar credenciales aleatorias seguras en memoria
$MysqlRootPassword = if ($env:MYSQL_ROOT_PASSWORD) { $env:MYSQL_ROOT_PASSWORD } else { New-RandomHex -Bytes 20 }
$MysqlDatabase     = if ($env:MYSQL_DATABASE)     { $env:MYSQL_DATABASE }     else { "nexus" }
$MysqlUser         = if ($env:MYSQL_USER)         { $env:MYSQL_USER }         else { "nexus_user" }
$MysqlPassword     = if ($env:MYSQL_PASSWORD)     { $env:MYSQL_PASSWORD }     else { New-RandomHex -Bytes 20 }

$MongoRootUser     = if ($env:MONGO_INITDB_ROOT_USERNAME) { $env:MONGO_INITDB_ROOT_USERNAME } else { "nexus_mongo_admin" }
$MongoRootPassword = if ($env:MONGO_INITDB_ROOT_PASSWORD) { $env:MONGO_INITDB_ROOT_PASSWORD } else { New-RandomHex -Bytes 20 }
$MongoDatabase     = if ($env:MONGODB_DATABASE)           { $env:MONGODB_DATABASE }           else { "nexus_audit" }

$AppKey            = if ($env:APP_KEY)    { $env:APP_KEY }    else { "base64:" + (New-RandomBase64 -Bytes 32) }
$JwtSecret         = if ($env:JWT_SECRET) { $env:JWT_SECRET } else { New-RandomHex -Bytes 32 }
$MongoUri          = "mongodb://${MongoRootUser}:${MongoRootPassword}@mongodb-svc:27017/${MongoDatabase}?authSource=admin"

# 3. Crear Secret: mysql-secret
Write-Host "-> Aplicando Secret: mysql-secret..." -ForegroundColor Yellow
kubectl @KubeArgs -n $Namespace create secret generic mysql-secret `
    "--from-literal=MYSQL_ROOT_PASSWORD=$MysqlRootPassword" `
    "--from-literal=MYSQL_DATABASE=$MysqlDatabase" `
    "--from-literal=MYSQL_USER=$MysqlUser" `
    "--from-literal=MYSQL_PASSWORD=$MysqlPassword" `
    --dry-run=client -o yaml | kubectl @KubeArgs apply -f -

# 4. Crear Secret: mongodb-secret
Write-Host "-> Aplicando Secret: mongodb-secret..." -ForegroundColor Yellow
kubectl @KubeArgs -n $Namespace create secret generic mongodb-secret `
    "--from-literal=MONGO_INITDB_ROOT_USERNAME=$MongoRootUser" `
    "--from-literal=MONGO_INITDB_ROOT_PASSWORD=$MongoRootPassword" `
    "--from-literal=MONGODB_DATABASE=$MongoDatabase" `
    --dry-run=client -o yaml | kubectl @KubeArgs apply -f -

# 5. Crear Secret: nexus-app-secret
Write-Host "-> Aplicando Secret: nexus-app-secret..." -ForegroundColor Yellow
kubectl @KubeArgs -n $Namespace create secret generic nexus-app-secret `
    "--from-literal=APP_KEY=$AppKey" `
    "--from-literal=JWT_SECRET=$JwtSecret" `
    "--from-literal=DB_USERNAME=$MysqlUser" `
    "--from-literal=DB_PASSWORD=$MysqlPassword" `
    "--from-literal=MONGODB_USERNAME=$MongoRootUser" `
    "--from-literal=MONGODB_PASSWORD=$MongoRootPassword" `
    "--from-literal=MONGODB_URI=$MongoUri" `
    --dry-run=client -o yaml | kubectl @KubeArgs apply -f -

# 6. Crear Secret TLS si openssl está disponible
$TempDir = Join-Path ([System.IO.Path]::GetTempPath()) ([System.Guid]::NewGuid().ToString())
New-Item -ItemType Directory -Path $TempDir -Force | Out-Null
try {
    $CertFile = Join-Path $TempDir "tls.crt"
    $KeyFile  = Join-Path $TempDir "tls.key"
    if (Get-Command openssl -ErrorAction SilentlyContinue) {
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 `
            -keyout $KeyFile `
            -out $CertFile `
            -subj "/CN=$IngressHost/O=Nexus ERP" `
            -addext "subjectAltName=DNS:$IngressHost" 2>$null

        Write-Host "-> Aplicando Secret TLS: $TlsSecretName..." -ForegroundColor Yellow
        kubectl @KubeArgs -n $Namespace create secret tls $TlsSecretName `
            "--cert=$CertFile" `
            "--key=$KeyFile" `
            --dry-run=client -o yaml | kubectl @KubeArgs apply -f -
    } else {
        Write-Warning "OpenSSL no detectado en PATH; omitiendo creación automática de $TlsSecretName."
    }
} finally {
    Remove-Item -Path $TempDir -Recurse -Force -ErrorAction SilentlyContinue
}

Write-Host "==================================================================" -ForegroundColor Green
Write-Host " Secrets aplicados exitosamente en el namespace '$Namespace':" -ForegroundColor Green
kubectl @KubeArgs -n $Namespace get secrets
Write-Host "==================================================================" -ForegroundColor Green


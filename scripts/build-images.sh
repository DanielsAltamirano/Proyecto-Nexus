#!/usr/bin/env bash
# ==============================================================================
# build-images.sh - ConstrucciÃ³n, publicaciÃ³n e impresiÃ³n de Digest SHA256
# Uso: ./scripts/build-images.sh <usuario_dockerhub_o_ghcr> [version]
# Ejemplo Docker Hub: ./scripts/build-images.sh miusuario
# Ejemplo GHCR:       ./scripts/build-images.sh ghcr.io/miusuario
# ==============================================================================
set -euo pipefail

if [ $# -lt 1 ] || [ -z "${1:-}" ]; then
    echo "Error: Debes proporcionar tu usuario de Docker Hub o ruta de GHCR."
    echo "Uso: $0 <usuario_dockerhub_o_ghcr> [version]"
    exit 1
fi

REGISTRY_USER="${1%/}"
VERSION="${2:-1.0.0}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

PHP_IMAGE="${REGISTRY_USER}/nexus-php:${VERSION}"
NGINX_IMAGE="${REGISTRY_USER}/nexus-nginx:${VERSION}"

cd "${PROJECT_ROOT}"

echo "=================================================================="
echo " 1. Construyendo imagen PHP-FPM (No-Root): ${PHP_IMAGE}"
echo "=================================================================="
docker build -f Dockerfile.php -t "${PHP_IMAGE}" .

echo "=================================================================="
echo " 2. Construyendo imagen Nginx (No-Root): ${NGINX_IMAGE}"
echo "=================================================================="
docker build -f Dockerfile.nginx -t "${NGINX_IMAGE}" .

echo "=================================================================="
echo " 3. Publicando imÃ¡genes en el registro (docker push)"
echo "=================================================================="
docker push "${PHP_IMAGE}"
docker push "${NGINX_IMAGE}"

PHP_DIGEST=$(docker inspect --format='{{if .RepoDigests}}{{index .RepoDigests 0}}{{else}}{{.Id}}{{end}}' "${PHP_IMAGE}")
NGINX_DIGEST=$(docker inspect --format='{{if .RepoDigests}}{{index .RepoDigests 0}}{{else}}{{.Id}}{{end}}' "${NGINX_IMAGE}")

echo ""
echo "=================================================================="
echo " RESUMEN DE IMÃGENES INMUTABLES Y DIGESTS SHA256"
echo "=================================================================="
echo "PHP-FPM Image  : ${PHP_IMAGE}"
echo "PHP-FPM Digest : ${PHP_DIGEST}"
echo "------------------------------------------------------------------"
echo "Nginx Image    : ${NGINX_IMAGE}"
echo "Nginx Digest   : ${NGINX_DIGEST}"
echo "=================================================================="

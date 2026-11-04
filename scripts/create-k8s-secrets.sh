#!/usr/bin/env bash
# ==============================================================================
# create-k8s-secrets.sh
# Generación segura e idempotente de Secrets en Kubernetes (Cero Secretos en Git)
# Crea: mysql-secret, mongodb-secret, nexus-app-secret y el Secret TLS del Ingress
#
# Uso:
#   ./scripts/create-k8s-secrets.sh [dev|prod] [contexto_kubectl]
# Ejemplos:
#   ./scripts/create-k8s-secrets.sh dev
#   ./scripts/create-k8s-secrets.sh prod k8s-prod-context
# ==============================================================================
set -euo pipefail

ENV_TARGET="${1:-dev}"
KUBE_CONTEXT="${2:-}"

if [[ "${ENV_TARGET}" != "dev" && "${ENV_TARGET}" != "prod" ]]; then
    echo "Error: El primer argumento debe ser 'dev' o 'prod'."
    echo "Uso: $0 [dev|prod] [contexto_kubectl]"
    exit 1
fi

if [[ "${ENV_TARGET}" == "dev" ]]; then
    NAMESPACE="nexus-dev"
    INGRESS_HOST="dev.nexus.internal"
    TLS_SECRET_NAME="dev-nexus-tls"
else
    NAMESPACE="nexus-prod"
    INGRESS_HOST="prod.nexus.internal"
    TLS_SECRET_NAME="prod-nexus-tls"
fi

KUBECTL_ARGS=()
if [[ -n "${KUBE_CONTEXT}" ]]; then
    KUBECTL_ARGS+=("--context=${KUBE_CONTEXT}")
fi

echo "=================================================================="
echo " Generando Secrets para ambiente: ${ENV_TARGET} (Namespace: ${NAMESPACE})"
echo "=================================================================="

# 1. Asegurar que el Namespace exista con las etiquetas de Pod Security Restricted
kubectl "${KUBECTL_ARGS[@]}" create namespace "${NAMESPACE}" --dry-run=client -o yaml | \
    kubectl "${KUBECTL_ARGS[@]}" apply -f -

kubectl "${KUBECTL_ARGS[@]}" label namespace "${NAMESPACE}" \
    pod-security.kubernetes.io/enforce=restricted \
    pod-security.kubernetes.io/enforce-version=latest \
    pod-security.kubernetes.io/audit=restricted \
    pod-security.kubernetes.io/audit-version=latest \
    pod-security.kubernetes.io/warn=restricted \
    pod-security.kubernetes.io/warn-version=latest \
    --overwrite

# 2. Generar contraseñas criptográficamente seguras (o usar variables de entorno si se proveen)
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-$(openssl rand -hex 20)}"
MYSQL_DATABASE="${MYSQL_DATABASE:-nexus_db}"
MYSQL_USER="${MYSQL_USER:-nexus_user}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-$(openssl rand -hex 20)}"

MONGO_INITDB_ROOT_USERNAME="${MONGO_INITDB_ROOT_USERNAME:-nexus_mongo_admin}"
MONGO_INITDB_ROOT_PASSWORD="${MONGO_INITDB_ROOT_PASSWORD:-$(openssl rand -hex 20)}"
MONGODB_DATABASE="${MONGODB_DATABASE:-nexus_audit}"

APP_KEY="${APP_KEY:-base64:$(openssl rand -base64 32)}"
JWT_SECRET="${JWT_SECRET:-$(openssl rand -hex 32)}"
MONGODB_URI="mongodb://${MONGO_INITDB_ROOT_USERNAME}:${MONGO_INITDB_ROOT_PASSWORD}@mongodb-svc:27017/${MONGODB_DATABASE}?authSource=admin"

# 3. Crear Secret: mysql-secret
echo "-> Aplicando Secret: mysql-secret..."
kubectl "${KUBECTL_ARGS[@]}" -n "${NAMESPACE}" create secret generic mysql-secret \
    --from-literal=MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD}" \
    --from-literal=MYSQL_DATABASE="${MYSQL_DATABASE}" \
    --from-literal=MYSQL_USER="${MYSQL_USER}" \
    --from-literal=MYSQL_PASSWORD="${MYSQL_PASSWORD}" \
    --dry-run=client -o yaml | kubectl "${KUBECTL_ARGS[@]}" apply -f -

# 4. Crear Secret: mongodb-secret
echo "-> Aplicando Secret: mongodb-secret..."
kubectl "${KUBECTL_ARGS[@]}" -n "${NAMESPACE}" create secret generic mongodb-secret \
    --from-literal=MONGO_INITDB_ROOT_USERNAME="${MONGO_INITDB_ROOT_USERNAME}" \
    --from-literal=MONGO_INITDB_ROOT_PASSWORD="${MONGO_INITDB_ROOT_PASSWORD}" \
    --from-literal=MONGODB_DATABASE="${MONGODB_DATABASE}" \
    --dry-run=client -o yaml | kubectl "${KUBECTL_ARGS[@]}" apply -f -

# 5. Crear Secret: nexus-app-secret
echo "-> Aplicando Secret: nexus-app-secret..."
kubectl "${KUBECTL_ARGS[@]}" -n "${NAMESPACE}" create secret generic nexus-app-secret \
    --from-literal=APP_KEY="${APP_KEY}" \
    --from-literal=JWT_SECRET="${JWT_SECRET}" \
    --from-literal=DB_USERNAME="${MYSQL_USER}" \
    --from-literal=DB_PASSWORD="${MYSQL_PASSWORD}" \
    --from-literal=MONGODB_USERNAME="${MONGO_INITDB_ROOT_USERNAME}" \
    --from-literal=MONGODB_PASSWORD="${MONGO_INITDB_ROOT_PASSWORD}" \
    --from-literal=MONGODB_URI="${MONGODB_URI}" \
    --dry-run=client -o yaml | kubectl "${KUBECTL_ARGS[@]}" apply -f -

# 6. Crear Secret TLS autofirmado para Ingress (dev-nexus-tls / prod-nexus-tls)
TMP_CERT_DIR="$(mktemp -d)"
trap 'rm -rf "${TMP_CERT_DIR}"' EXIT

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout "${TMP_CERT_DIR}/tls.key" \
    -out "${TMP_CERT_DIR}/tls.crt" \
    -subj "/CN=${INGRESS_HOST}/O=Nexus ERP" \
    -addext "subjectAltName=DNS:${INGRESS_HOST}" 2>/dev/null

echo "-> Aplicando Secret TLS: ${TLS_SECRET_NAME}..."
kubectl "${KUBECTL_ARGS[@]}" -n "${NAMESPACE}" create secret tls "${TLS_SECRET_NAME}" \
    --cert="${TMP_CERT_DIR}/tls.crt" \
    --key="${TMP_CERT_DIR}/tls.key" \
    --dry-run=client -o yaml | kubectl "${KUBECTL_ARGS[@]}" apply -f -

echo "=================================================================="
echo " Secrets creados exitosamente en el namespace '${NAMESPACE}':"
kubectl "${KUBECTL_ARGS[@]}" -n "${NAMESPACE}" get secrets
echo "=================================================================="


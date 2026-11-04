#!/usr/bin/env bash
# ==============================================================================
# restore-test.sh
# Procedimiento documentado y automatizado para verificar y restaurar respaldos
# comprimidos de MySQL (.sql.gz) y MongoDB (.archive.gz) desde el PVC
# nexus-backups-pvc en una instancia aislada de prueba.
#
# Uso:
#   ./scripts/restore-test.sh [namespace]
# Ejemplo:
#   ./scripts/restore-test.sh nexus-prod
# ==============================================================================
set -euo pipefail

NAMESPACE="${1:-nexus-prod}"
MYSQL_BACKUP_FILE="${2:-/backups/mysql/nexus_mysql_latest.sql.gz}"
MONGO_BACKUP_FILE="${3:-/backups/mongodb/nexus_mongodb_latest.archive.gz}"

echo "=================================================================="
echo " PRUEBA DE RESTAURACIÓN AISLADA DE RESPALDOS - NEXUS ERP"
echo " Namespace origen : ${NAMESPACE}"
echo " Respaldo MySQL   : ${MYSQL_BACKUP_FILE}"
echo " Respaldo MongoDB : ${MONGO_BACKUP_FILE}"
echo "=================================================================="

# ------------------------------------------------------------------------------
# 1. Comandos exactos para disparo manual de los CronJobs de respaldo (opcional)
# ------------------------------------------------------------------------------
# kubectl -n "${NAMESPACE}" create job --from=cronjob/mysql-backup mysql-backup-manual-$(date +%s)
# kubectl -n "${NAMESPACE}" create job --from=cronjob/mongodb-backup mongodb-backup-manual-$(date +%s)

# ------------------------------------------------------------------------------
# 2. Restauración verificada de MySQL 8.0 en una base de datos aislada
#    (nexus_db_restore_test) o contenedor efímero aislado
# ------------------------------------------------------------------------------
echo "-> [1/2] Ejecutando prueba de restauración de MySQL desde ${MYSQL_BACKUP_FILE}..."

kubectl -n "${NAMESPACE}" run mysql-restore-verifier \
  --rm -i --restart=Never \
  --image=mysql:8.0 \
  --labels="app=mysql-backup,db-access=true" \
  --overrides='{
    "spec": {
      "serviceAccountName": "nexus-backup-sa",
      "automountServiceAccountToken": false,
      "securityContext": {
        "runAsNonRoot": true,
        "runAsUser": 999,
        "runAsGroup": 999,
        "fsGroup": 999,
        "seccompProfile": { "type": "RuntimeDefault" }
      },
      "containers": [{
        "name": "mysql-restore-verifier",
        "image": "mysql:8.0",
        "command": ["sh", "-c", "set -euo pipefail; echo \"Verificando integridad gzip de '"${MYSQL_BACKUP_FILE}"'...\"; gzip -t \"'"${MYSQL_BACKUP_FILE}"'\"; echo \"Restaurando en esquema aislado nexus_db_restore_test...\"; mysql -h mysql-svc -u root -p\"${MYSQL_ROOT_PASSWORD}\" -e \"DROP DATABASE IF EXISTS nexus_db_restore_test; CREATE DATABASE nexus_db_restore_test;\"; gunzip -c \"'"${MYSQL_BACKUP_FILE}"'\" | mysql -h mysql-svc -u root -p\"${MYSQL_ROOT_PASSWORD}\" nexus_db_restore_test; echo \"Tablas restauradas en nexus_db_restore_test:\"; mysql -h mysql-svc -u root -p\"${MYSQL_ROOT_PASSWORD}\" -e \"SELECT COUNT(*) AS total_products FROM nexus_db_restore_test.products; DROP DATABASE nexus_db_restore_test;\""],
        "envFrom": [{ "secretRef": { "name": "mysql-secret" } }],
        "resources": {
          "requests": { "cpu": "50m", "memory": "64Mi" },
          "limits": { "cpu": "250m", "memory": "256Mi" }
        },
        "securityContext": {
          "runAsNonRoot": true,
          "runAsUser": 999,
          "runAsGroup": 999,
          "allowPrivilegeEscalation": false,
          "readOnlyRootFilesystem": true,
          "capabilities": { "drop": ["ALL"] },
          "seccompProfile": { "type": "RuntimeDefault" }
        },
        "volumeMounts": [
          { "name": "backups", "mountPath": "/backups", "readOnly": true },
          { "name": "tmp", "mountPath": "/tmp" }
        ]
      }],
      "volumes": [
        { "name": "backups", "persistentVolumeClaim": { "claimName": "nexus-backups-pvc" } },
        { "name": "tmp", "emptyDir": {} }
      ]
    }
  }'

# ------------------------------------------------------------------------------
# 3. Restauración verificada de MongoDB 7.0 en una base de datos aislada
#    (nexus_audit_restore_test) usando mongorestore --archive --gzip --nsTo
# ------------------------------------------------------------------------------
echo "-> [2/2] Ejecutando prueba de restauración de MongoDB desde ${MONGO_BACKUP_FILE}..."

kubectl -n "${NAMESPACE}" run mongodb-restore-verifier \
  --rm -i --restart=Never \
  --image=mongo:7.0 \
  --labels="app=mongodb-backup,db-access=true" \
  --overrides='{
    "spec": {
      "serviceAccountName": "nexus-backup-sa",
      "automountServiceAccountToken": false,
      "securityContext": {
        "runAsNonRoot": true,
        "runAsUser": 999,
        "runAsGroup": 999,
        "fsGroup": 999,
        "seccompProfile": { "type": "RuntimeDefault" }
      },
      "containers": [{
        "name": "mongodb-restore-verifier",
        "image": "mongo:7.0",
        "command": ["sh", "-c", "set -euo pipefail; echo \"Restaurando archivo comprimido '"${MONGO_BACKUP_FILE}"' hacia base aislada nexus_audit_restore_test...\"; mongorestore --host mongodb-svc --port 27017 --username \"${MONGO_INITDB_ROOT_USERNAME}\" --password \"${MONGO_INITDB_ROOT_PASSWORD}\" --authenticationDatabase admin --archive=\"'"${MONGO_BACKUP_FILE}"'\" --gzip --nsFrom=\"${MONGODB_DATABASE}.*\" --nsTo=\"nexus_audit_restore_test.*\" --drop; mongosh --host mongodb-svc --port 27017 -u \"${MONGO_INITDB_ROOT_USERNAME}\" -p \"${MONGO_INITDB_ROOT_PASSWORD}\" --authenticationDatabase admin --eval \"db.getSiblingDB('\''nexus_audit_restore_test'\'').getCollectionNames(); db.getSiblingDB('\''nexus_audit_restore_test'\'').dropDatabase();\""],
        "envFrom": [{ "secretRef": { "name": "mongodb-secret" } }],
        "resources": {
          "requests": { "cpu": "50m", "memory": "64Mi" },
          "limits": { "cpu": "250m", "memory": "256Mi" }
        },
        "securityContext": {
          "runAsNonRoot": true,
          "runAsUser": 999,
          "runAsGroup": 999,
          "allowPrivilegeEscalation": false,
          "readOnlyRootFilesystem": true,
          "capabilities": { "drop": ["ALL"] },
          "seccompProfile": { "type": "RuntimeDefault" }
        },
        "volumeMounts": [
          { "name": "backups", "mountPath": "/backups", "readOnly": true },
          { "name": "tmp", "mountPath": "/tmp" }
        ]
      }],
      "volumes": [
        { "name": "backups", "persistentVolumeClaim": { "claimName": "nexus-backups-pvc" } },
        { "name": "tmp", "emptyDir": {} }
      ]
    }
  }'

echo "=================================================================="
echo " Prueba de restauración aislada finalizada con éxito."
echo "=================================================================="

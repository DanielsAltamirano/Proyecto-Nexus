# Nexus ERP — Sistema Farmacéutico de Alta Disponibilidad (Cloud-Native / Kubernetes)

**Nexus ERP** es un sistema integral de planificación de recursos empresariales (ERP) y Punto de Venta (POS) farmacéutico desarrollado en **Laravel 12 (PHP 8.3)**, diseñado bajo una arquitectura **Stateless (Sin estado)** y preparado para despliegues de alta disponibilidad en clústeres de **Kubernetes**.

---

## 1. Arquitectura Multicapa

El sistema desacopla la capa de presentación, el procesamiento de negocio y la persistencia poliglota (Relacional + NoSQL) para garantizar escalabilidad horizontal, tolerancia a fallos y trazabilidad normativa:

1. **Capa Web / Reverse Proxy (Nginx - Puerto `8080`):**
   - Contenedor ligero basado en `nginx:1.27-alpine` ejecutado con usuario sin privilegios (`appuser`, UID `10001`).
   - Sirve los activos estáticos compilados con Vite (`public/build`) en modo solo lectura y enruta las peticiones dinámicas hacia PHP-FPM mediante FastCGI.
2. **Capa de Aplicación Stateless (Laravel PHP-FPM - Puerto `9000`):**
   - Contenedor `php:8.3-fpm-alpine` endurecido (`appuser`, UID `10001`), con código fuente en permisos de solo lectura (`550`/`440`).
   - **Arquitectura Sin Estado:** Las sesiones de usuario (`SESSION_DRIVER=database`) y la caché (`CACHE_STORE=database`) se centralizan en MySQL, permitiendo escalar múltiples réplicas de pods sin pérdida de sesión.
   - **Reglas de Negocio Farmacéuticas:**
     - Bloqueo estricto de ventas cuando el stock relacional (`batches.stock`) es igual a `0`.
     - Validación obligatoria de `cedula_medica` para medicamentos clasificados como **Controlados** (`is_controlled = true`), ejecutando el flujo de estados: `Solicitada` -> `Validación` -> `Surtida`.
     - Control de acceso basado en roles (RBAC): bloqueo de eliminaciones lógicas (`SoftDeletes`) para el rol `Personal de Mostrador` (Cajero), restringiendo dicha operación exclusivamente a `admin` y `superadmin`.
3. **Fuente de Verdad Relacional (MySQL - Puerto `3306`):**
   - Almacena de forma transaccional (`ACID`) el catálogo de medicamentos (`products`), lotes e inventario por sucursal (`batches`), ventas (`sales`), sesiones (`sessions`), caché (`cache`) y la cola relacional de contingencia (`audit_outboxes`).
4. **Bitácora Inmutable NoSQL y Patrón Outbox (MongoDB - Puerto `27017`):**
   - El modelo `App\Models\AuditLog` persiste en la colección `audit_logs` de MongoDB una bitácora estrictamente **inmutable** (bloquea operaciones `update` y `delete` a nivel de modelo) con los datos de auditoría de cada venta y el respaldo de recetas médicas de medicamentos controlados.
   - **Patrón Outbox e Idempotencia (`AuditOutboxService`):** Si la transacción de venta en MySQL se confirma con éxito pero la inserción en MongoDB falla (por caída temporal o partición de red), el sistema captura la excepción sin corromper ni revertir la venta en MySQL y registra el evento pendiente en la tabla relacional `audit_outboxes` con una llave única (`idempotency_key`) para su posterior sincronización idempotente.

---

## 2. Estructura de Carpetas del Repositorio

```text
nexus/
├── app/
│   ├── Console/Commands/
│   │   └── RetryAuditOutboxCommand.php   # Comando Artisan (outbox:retry)
│   ├── Http/
│   │   ├── Controllers/                  # Controladores Web y API REST v1
│   │   └── Requests/
│   │       └── StoreSaleRequest.php      # Validación de stock > 0 y cedula_medica
│   ├── Models/
│   │   ├── AuditLog.php                  # Modelo NoSQL inmutable (MongoDB)
│   │   ├── AuditOutbox.php               # Modelo relacional temporal (Patrón Outbox)
│   │   ├── Batch.php                     # Lotes e inventario por sucursal
│   │   ├── Product.php                   # Catálogo de medicamentos y bloqueo SoftDeletes
│   │   └── Sale.php                      # Ventas y máquina de estados (Solicitada -> Validación -> Surtida)
│   └── Services/
│       └── AuditOutboxService.php        # Orquestador del Patrón Outbox hacia MongoDB
├── config/                               # Configuración Stateless (session, cache, database)
├── database/
│   ├── factories/                        # Factories (UserFactory, ProductFactory, BatchFactory)
│   ├── migrations/                       # Migraciones MySQL (sessions, sales, audit_outboxes)
│   └── seeders/                          # NexusSeeder y DatabaseSeeder (+100 registros sintéticos)
├── docker/
│   ├── nginx/                            # Configuración Nginx no-root (puerto 8080, /tmp)
│   └── php/                              # Pool PHP-FPM no-root (puerto 9000)
├── docs/                                 # Documentación técnica y diagramas de arquitectura
├── k8s/                                  # Manifiestos de Kubernetes (Kustomize)
│   ├── base/                             # Manifiestos base (Deployments, Services, ConfigMaps)
│   ├── overlays/
│   │   ├── dev/                          # Parches y recursos para entorno de desarrollo
│   │   └── prod/                         # Parches de alta disponibilidad para producción
│   ├── observability/                    # Monitoreo, métricas y recolectores de logs
│   ├── network/                          # Ingress, NetworkPolicies y políticas de tráfico
│   └── backups/                          # CronJobs de respaldo de bases de datos
├── scripts/
│   ├── build-images.sh                   # Script Bash de construcción, push y digest SHA256
│   └── build-images.ps1                  # Script PowerShell de construcción, push y digest SHA256
├── Dockerfile.php                        # Imagen segura PHP-FPM 8.3 (appuser, puerto 9000)
├── Dockerfile.nginx                      # Imagen segura Nginx 1.27 (appuser, puerto 8080)
└── .env.example                          # Plantilla de variables de entorno Stateless
```

---

## 3. Construcción de Imágenes de Contenedor

Ambos contenedores se ejecutan con el usuario no privilegiado `appuser` (`UID 10001`), exponen puertos no privilegiados (`9000` y `8080`) y mantienen permisos de solo lectura en las carpetas de código.

### Opción A: Usando los scripts automatizados (Recomendado)

**En Linux / macOS / Git Bash:**
```bash
chmod +x scripts/build-images.sh
./scripts/build-images.sh <tu-usuario-dockerhub-o-ghcr>
```

**En Windows (PowerShell):**
```powershell
.\scripts\build-images.ps1 -RegistryUser "<tu-usuario-dockerhub-o-ghcr>"
```

> Los scripts etiquetan las imágenes con la versión inmutable `1.0.0`, realizan `docker push` y muestran el **digest SHA256** exacto de cada imagen al finalizar.

### Opción B: Construcción manual con Docker CLI

```bash
# 1. Construir imagen PHP-FPM (Puerto 9000)
docker build -f Dockerfile.php -t <tu-usuario>/nexus-php:1.0.0 .

# 2. Construir imagen Nginx con assets compilados (Puerto 8080)
docker build -f Dockerfile.nginx -t <tu-usuario>/nexus-nginx:1.0.0 .
```

---

## 4. Migraciones, Datos Semilla (Seeders) y Reintento Outbox

### Ejecutar Migraciones Relacionales (MySQL)
Crea todas las tablas del ERP, incluyendo `sessions`, `cache`, `sales` (con `cedula_medica` y `status`) y `audit_outboxes`:
```bash
php artisan migrate --force
```

### Poblar Base de Datos con Datos Sintéticos (Seeders)
Genera los roles del sistema, usuarios con contraseñas hasheadas (`Hash::make`), 100 medicamentos sintéticos (70 Normales y 30 Controlados) y 100 lotes de inventario inicial:
```bash
php artisan db:seed --force
```

### Sincronizar Bitácoras Pendientes hacia MongoDB (Patrón Outbox)
Reintenta de forma idempotente el envío de los registros almacenados temporalmente en la tabla relacional `audit_outboxes` hacia la colección `audit_logs` de MongoDB:
```bash
php artisan outbox:retry --limit=100
```

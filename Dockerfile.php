# ==============================================================================
# Dockerfile.php - Contenedor Seguro PHP-FPM (Usuario No-Root: appuser, Puerto 9000)
# Proyecto: Nexus ERP (Alta Disponibilidad en Kubernetes)
# ==============================================================================
FROM php:8.3-fpm-alpine

# 1. Instalar dependencias del sistema y extensiones de PHP (MySQL + MongoDB)
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    openssl-dev \
    curl \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del .build-deps \
    && rm -rf /tmp/pear

# Instalar Composer desde la imagen oficial
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# 2. Crear grupo y usuario no-root (appuser - UID/GID 10001)
RUN addgroup -g 10001 -S appgroup \
    && adduser -u 10001 -S appuser -G appgroup

WORKDIR /var/www/html

# 3. Copiar dependencias e instalar paquetes de producción
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# 4. Copiar el código de la aplicación y configuración de PHP-FPM
COPY . /var/www/html
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-nexus.conf

# Eliminar configuración por defecto que intenta cambiar user/group como root
RUN rm -f /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf.default

# 5. Endurecimiento del sistema de archivos (Solo lectura en código fuente, escritura en storage y bootstrap/cache)
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache \
    && chown -R appuser:appgroup /var/www/html \
    # Directorios de código fuente en modo solo lectura (550 directorios / 440 archivos)
    && find app config database lang public resources routes tests vendor -type d -exec chmod 550 {} + \
    && find app config database lang public resources routes tests vendor -type f -exec chmod 440 {} + \
    && chmod 440 artisan composer.json composer.lock \
    # Únicamente storage y bootstrap/cache tienen permisos de escritura para appuser
    && chmod -R 770 storage bootstrap/cache

# 6. Ejecutar como usuario no-root y exponer puerto no privilegiado 9000
USER appuser

EXPOSE 9000

CMD ["php-fpm", "-F"]


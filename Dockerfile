# ============================================================
# DOCKERFILE - Backend Laravel (Nginx + PHP-FPM)
# ============================================================
# Este archivo le dice a Docker cómo construir la imagen
# del backend de Merulink.
# Incluye Nginx (servidor web) + PHP-FPM (procesa PHP)
# en UN solo contenedor para simplificar.
# ============================================================

FROM php:8.3-fpm

# ── 1. Instalar dependencias del sistema ──────────────────
# Son paquetes de Linux necesarios para que Laravel funcione
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    unzip \
    git \
    curl \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# ── 2. Instalar extensiones de PHP ────────────────────────
# Son los "plugins" que PHP necesita para conectar con PostgreSQL, etc.
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip \
    gd \
    exif \
    pcntl \
    bcmath \
    soap

# ── 3. Instalar Composer (gestor de paquetes PHP) ────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── 4. Configurar el directorio de trabajo ───────────────
# Todas las operaciones siguientes se harán dentro de esta carpeta
WORKDIR /var/www/html

# ── 5. Copiar archivos del proyecto ──────────────────────
# Primero copiamos solo composer.json y composer.lock
# Esto es un truco: si estos archivos no cambian, Docker usa caché
# y no reinstala dependencias cada vez
COPY composer.json composer.lock* ./

# Instalar dependencias de PHP SIN las de desarrollo (más rápido y ligero)
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

# Ahora sí copiamos todo el código fuente
COPY . .

# ── 6. Dar permisos correctos a las carpetas ─────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# ── 7. Configurar Nginx ──────────────────────────────────
# Quitar configuración por defecto de Nginx
RUN rm -f /etc/nginx/sites-enabled/default
# Copiar nuestra configuración personalizada
COPY docker/nginx-backend.conf /etc/nginx/sites-enabled/default

# ── 8. Configurar Supervisor (mantiene Nginx + PHP-FPM vivos) ─
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── 9. Script de inicio (migraciones, caché, etc.) ───────
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# ── 10. Puerto donde Nginx escucha ───────────────────────
EXPOSE 80

# ── 11. Comando de arranque ──────────────────────────────
CMD ["/usr/local/bin/start.sh"]

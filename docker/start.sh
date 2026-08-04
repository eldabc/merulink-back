#!/bin/bash
# ============================================================
# Script de inicio del Backend
# ============================================================
# Se ejecuta cada vez que el contenedor arranca.
# Prepara Laravel (migraciones, caché) y luego inicia los
# servicios (Nginx + PHP-FPM) con Supervisor.
# ============================================================

echo "🚀 Iniciando Merulink Backend..."

# Esperar a que la base de datos esté lista
# (El contenedor de PostgreSQL puede tardar unos segundos en arrancar)
echo "⏳ Esperando a que PostgreSQL esté listo..."
until php -r "try { new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'conectado'; } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
    echo "   PostgreSQL no está listo aún... reintentando en 3s"
    sleep 3
done
echo "✅ PostgreSQL está listo."

# ── Preparar Laravel ──────────────────────────────────────
cd /var/www/html

# Limpiar cachés ANTES de migrar para no usar configs obsoletas
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generar clave de la aplicación si no existe
php artisan key:generate --force --no-interaction

# Ejecutar migraciones pendientes
echo "📊 Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# Ejecutar seeders
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force --no-interaction

# Regenerar caché
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Asegurar que logs, storage y caché de bootstrap pertenezcan a www-data
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Backend listo. Iniciando servicios..."

# ── Iniciar Supervisor (Nginx + PHP-FPM) ──────────────────
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf

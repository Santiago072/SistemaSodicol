#!/bin/sh
# ─────────────────────────────────────────────────────────────
#  Entrypoint SODICOL — inicialización antes de arrancar Apache
# ─────────────────────────────────────────────────────────────
set -e

echo "▶  Iniciando entrypoint SODICOL..."

# ── 1. Inyectar variables de entorno en config/.env ──────────
#  El proyecto lee config/.env (NO variables de entorno directas).
#  Generamos/sobreescribimos ese archivo con los valores actuales.
ENV_FILE="/var/www/html/config/.env"

cat > "$ENV_FILE" <<EOF
# Generado automáticamente por Docker entrypoint
# No editar manualmente en producción

DB_HOST=${DB_HOST:-mysql}
DB_USER=${DB_USER:-sodicol}
DB_PASS=${DB_PASS}
DB_NAME=${DB_NAME:-sistema_sodicol}

APP_BASE=${APP_BASE:-/}

SESSION_LIFETIME=${SESSION_LIFETIME:-3600}
COOKIE_SECURE=${COOKIE_SECURE:-0}

UPLOAD_MAX_SIZE=${UPLOAD_MAX_SIZE:-5242880}
ALLOWED_EXTENSIONS=${ALLOWED_EXTENSIONS:-jpg,jpeg,png,gif,webp}
EOF

chown www-data:www-data "$ENV_FILE"
chmod 640 "$ENV_FILE"

echo "   config/.env generado correctamente"

# ── 2. Asegurar permisos de directorios en tiempo de ejecución ─
chmod 755 /var/www/html/uploads
chmod 755 /var/www/html/logs
chmod 777 /var/lib/php/sessions
chown -R www-data:www-data /var/www/html/uploads \
                            /var/www/html/logs \
                            /var/lib/php/sessions

echo "   Permisos de directorios configurados"

# ── 3. Crear archivo de log si no existe ─────────────────────
touch /var/www/html/logs/php_errors.log
chown www-data:www-data /var/www/html/logs/php_errors.log
chmod 664 /var/www/html/logs/php_errors.log

echo "   Archivos de log inicializados"
echo "✔  Entrypoint completado. Iniciando Apache..."

exec "$@"

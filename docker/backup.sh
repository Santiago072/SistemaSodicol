#!/bin/bash

# ─────────────────────────────────────────────────────────────
#  Script de Copias de Seguridad para SistemaSodicol
#  Este script exporta la base de datos MySQL desde el contenedor
#  y la guarda en la carpeta ../database/backups/
# ─────────────────────────────────────────────────────────────

# Ir al directorio donde está el script (para usar rutas relativas correctas)
cd "$(dirname "$0")"

# Archivo de entorno a leer
ENV_FILE=".env"

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Error: No se encontró el archivo $ENV_FILE en la carpeta docker/"
    echo "Asegúrate de copiar .env.example a .env y configurarlo."
    exit 1
fi

# Cargar variables de entorno (ignorando comentarios)
export $(grep -v '^#' $ENV_FILE | xargs)

# Variables
DB_CONTAINER="sodicol_mysql"
BACKUP_DIR="../database/backups"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/backup_${DB_NAME}_${DATE}.sql"

# Crear directorio de backups si no existe
mkdir -p "$BACKUP_DIR"

echo "⏳ Iniciando copia de seguridad de la base de datos '$DB_NAME'..."

# Ejecutar mysqldump dentro del contenedor
docker exec "$DB_CONTAINER" /usr/bin/mysqldump -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "✅ Copia de seguridad creada con éxito:"
    echo "📁 Archivo: $BACKUP_FILE"
    
    # Mantener solo los últimos 10 backups para ahorrar espacio
    ls -tp "$BACKUP_DIR"/*.sql | grep -v '/$' | tail -n +11 | xargs -I {} rm -- {} 2>/dev/null
    
    echo "♻️  Se conservan solo los últimos 10 backups para ahorrar espacio en disco."
else
    echo "❌ Error al crear la copia de seguridad. Verifica que el contenedor '$DB_CONTAINER' esté en ejecución."
    # Eliminar el archivo vacío si falló
    rm -f "$BACKUP_FILE"
    exit 1
fi

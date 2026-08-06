#!/bin/bash
# Script de despliegue automático - Sistema Sodicol

echo "Iniciando despliegue automático..."

# 1. Ajustar permisos
echo "[1/5] Ajustando permisos..."
sudo chown -R $USER:$USER .

# 2. Obtener cambios de GitHub
echo "[2/5] Obteniendo cambios de GitHub..."
git fetch origin

# 3. Sincronizar con main
echo "[3/5] Sincronizando con la rama main..."
git reset --hard origin/main

# 4. Asegurar config/.env desde .env.example
echo "[4/5] Generando config/.env desde .env.example..."
cp -f .env.example config/.env

# 5. Detener contenedores, limpiar volumenes antiguos y reconstruir
echo "[5/5] Reiniciando volumenes y levantando contenedores Docker..."
docker compose down -v
docker compose up -d --build

echo ""
echo "✅ Despliegue completado exitosamente."

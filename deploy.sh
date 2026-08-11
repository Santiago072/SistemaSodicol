#!/bin/bash
# ============================================================
# deploy.sh — Script oficial de actualización Sistema Sodicol
# Usar en el VPS: chmod +x deploy.sh && ./deploy.sh
# ============================================================

set -e  # Detiene el script si cualquier comando falla

echo ""
echo "=========================================="
echo " 🚀 Desplegando Sistema Sodicol en el VPS..."
echo "=========================================="
echo ""

# Paso 1: Ajustar permisos
echo "🔒 Ajustando permisos..."
sudo chown -R $USER:$USER .

# Paso 2: Obtener cambios de GitHub
echo "⬇️  Obteniendo código desde GitHub (rama main)..."
git fetch origin
git reset --hard origin/main

# Paso 3: Generar config/.env desde .env.example
echo "📋 Generando config/.env desde .env.example..."
cp -f .env.example config/.env

# Paso 4: Copiar config de Nginx y habilitar el sitio
echo "🔧 Actualizando configuración de Nginx..."
sudo cp nginx/sodicol.conf /etc/nginx/sites-available/sodicol.conf
sudo ln -sf /etc/nginx/sites-available/sodicol.conf /etc/nginx/sites-enabled/sodicol.conf
sudo nginx -t && sudo systemctl reload nginx

# Paso 5: Reconstruir y levantar contenedores
echo "🐳 Reiniciando contenedores Docker..."
sudo docker compose down -v
sudo docker compose up -d --build

# Paso 6: Limpiar imágenes obsoletas
echo "🧹 Limpiando imágenes antiguas..."
sudo docker image prune -f

# Paso 7: Mostrar estado
echo ""
echo "📊 Estado de los contenedores:"
sudo docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
echo "=========================================="
echo " ✅ ¡Despliegue completado con éxito!"
echo "    🌐 App: https://sistemasodicol.slscode.online"
echo "=========================================="
echo ""

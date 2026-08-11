# Manual de Despliegue en VPS — Sistema Sodicol

Este manual describe el procedimiento paso a paso para poner en producción y mantener actualizado el **Sistema de Gestión Empresarial Sodicol** en un Servidor Privado Virtual (VPS) con Linux, Docker Compose y Nginx / Caddy.

---

## 📋 Requisitos Previos

- Servidor VPS con **Ubuntu 22.04 / 24.04 LTS** o Debian 12.
- **Docker** y **Docker Compose** (Plugin v2) instalados.
- **Git** configurado en el servidor.
- Un subdominio o dominio apuntando a la IP pública de tu VPS (ej. `sodicol.slscode.online`).

---

## 🚀 Paso 1: Clonar el Repositorio

Conéctate por SSH a tu servidor VPS e ingresa al directorio de proyectos:

```bash
cd /var/www  # o el directorio donde gestionas tus aplicaciones
git clone https://github.com/Santiago072/SistemaSodicol.git
cd SistemaSodicol
```

---

## ⚙️ Paso 2: Configurar las Variables de Entorno

Copia la plantilla `.env.example` para crear tu archivo seguro `.env` (este archivo no se sube a Git):

```bash
cp .env.example .env
nano .env
```

Configura tus credenciales seguras:
```ini
DB_HOST=sodicol_db
DB_USER=sodicol
DB_PASS=TuPasswordSeguro123!
DB_NAME=sistema_sodicol
SESSION_LIFETIME=3600
COOKIE_SECURE=1
UPLOAD_MAX_SIZE=5242880
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,webp
```

---

## 🐳 Paso 3: Despliegue Automatizado con `deploy.sh`

El proyecto cuenta con un script de automatización que compila y levanta los servicios:

```bash
chmod +x deploy.sh
./deploy.sh
```

El script ejecuta automáticamente:
1. `git pull origin main` para descargar los últimos cambios.
2. Comprobación y creación de directorios requeridos (`uploads/`, `logs/`).
3. Inicialización de contenedores con `docker compose up -d --build`.

---

## 🌐 Paso 4: Configurar Proxy Inverso (SSL / HTTPS)

Si utilizas **Nginx Proxy Manager** o Nginx nativo:
- **Forward Hostname / IP:** `sodicol_web` (o la IP local del host).
- **Forward Port:** `8891` (puerto configurado en `docker-compose.yml`).
- **Websockets Support:** Activado.
- **SSL Certificate:** Solicitar certificado Let's Encrypt con *Force SSL* y *HTTP/2 Support*.

---

## 🔄 Actualización Continua del Sistema

Cuando realices mejoras en el código y hagas `git push`:

1. Ingresa a la terminal de tu VPS.
2. Ejecuta:
   ```bash
   cd SistemaSodicol
   bash deploy.sh
   ```
3. El sistema aplicará los cambios sin caída del servicio y preservando la base de datos intacta.

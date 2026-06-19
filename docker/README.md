# 🚀 Guía de Despliegue — SODICOL en Docker

> **Dominio:** `slscode.online` | **Stack:** PHP 8.1 + Apache + MySQL 8 + phpMyAdmin

---

## Requisitos previos

| Requisito | Versión mínima |
|-----------|---------------|
| Docker    | 24.x          |
| Docker Compose | v2 (plugin) |
| Ubuntu    | 22.04 / 24.04 |
| Nginx Proxy Manager | 2.x (paso posterior) |

---

## Estructura Docker

```
docker/
├── docker-compose.yml   ← Orquestación de servicios
├── .env                 ← Variables de entorno (⚠ nunca subir a git)
├── apache/
│   ├── Dockerfile       ← Imagen PHP 8.1 + Apache
│   ├── sodicol.conf     ← VirtualHost Apache
│   └── entrypoint.sh   ← Script de inicialización
├── mysql/
│   └── init.sql         ← Schema + datos iniciales
└── php/
    └── custom.ini       ← Configuración PHP para producción
```

---

## Paso 1 — Clonar el repositorio en el VPS

```bash
# En el directorio del usuario (ej: /home/usuario/)
cd ~
git clone https://github.com/Santiago072/SistemaSodicol.git sodicol
cd sodicol
```

---

## Paso 2 — Configurar variables de entorno

```bash
# Editar las credenciales ANTES de arrancar
nano docker/.env
```

**Variables críticas a revisar:**

```env
DB_PASS=<cambia_por_contraseña_segura>
MYSQL_ROOT_PASSWORD=<cambia_por_contraseña_root_segura>
```

---

## Paso 3 — Construir e iniciar el stack

```bash
# ⚠ Ejecutar SIEMPRE desde el directorio docker/
cd docker

docker compose up -d
```

El primer arranque tardará ~3–5 minutos (descarga de imágenes + inicialización de MySQL).

---

## Paso 4 — Verificar que los servicios estén corriendo

```bash
docker compose ps
```

Salida esperada:

```
NAME                  STATUS          PORTS
sodicol_app           Up (healthy)    80/tcp
sodicol_mysql         Up (healthy)    3306/tcp
sodicol_phpmyadmin    Up              80/tcp
```

---

## Paso 5 — Ver logs en tiempo real

```bash
# Todos los servicios
docker compose logs -f

# Solo la aplicación PHP
docker compose logs -f app

# Solo MySQL
docker compose logs -f mysql
```

---

## Paso 6 — Exponer con Nginx Proxy Manager

En el panel de Nginx Proxy Manager, agregar **Proxy Hosts**:

| Dominio           | Forward Hostname   | Forward Port | SSL |
|-------------------|--------------------|--------------|-----|
| `slscode.online`  | `sodicol_app`      | `80`         | ✅ Let's Encrypt |
| `pma.slscode.online` | `sodicol_phpmyadmin` | `80`   | ✅ Let's Encrypt |

> **Red Docker a conectar:** `sodicol_network`

```bash
# Conectar el contenedor de Nginx Proxy Manager a la red SODICOL
docker network connect sodicol_network <nombre_contenedor_npm>
```

---

## Comandos útiles de mantenimiento

```bash
# Detener el stack
docker compose down

# Detener y eliminar volúmenes (⚠ BORRA LA BASE DE DATOS)
docker compose down -v

# Reconstruir la imagen tras cambios en el código
docker compose build --no-cache app
docker compose up -d app

# Acceder al contenedor PHP
docker exec -it sodicol_app bash

# Acceder a MySQL desde CLI
docker exec -it sodicol_mysql mysql -u sodicol -p sistema_sodicol

# Ver tamaño de los volúmenes
docker system df -v | grep sodicol

# Backup de la base de datos
docker exec sodicol_mysql mysqldump \
  -u sodicol -p<contraseña> sistema_sodicol \
  > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
cat backup.sql | docker exec -i sodicol_mysql mysql \
  -u sodicol -p<contraseña> sistema_sodicol
```

---

## Activar HTTPS (después de configurar Nginx Proxy Manager)

Editar `docker/.env`:
```env
COOKIE_SECURE=1
```

Y en `docker/php/custom.ini`, descomentar:
```ini
; session.cookie_secure = 1   →   session.cookie_secure = 1
```

Luego reiniciar:
```bash
docker compose restart app
```

---

## Primer acceso al sistema

| Campo    | Valor                    |
|----------|--------------------------|
| URL      | `http://slscode.online`  |
| Usuario  | `admin@sodicol.com`    |
| Contraseña | `1234567890` (documento) |

> ⚠ **Cambiar la contraseña inmediatamente tras el primer login.**

---

## Credenciales phpMyAdmin

| Campo    | Valor                    |
|----------|--------------------------|
| URL      | `http://pma.slscode.online` |
| Usuario  | `sodicol`                |
| Contraseña | Ver `docker/.env` → `DB_PASS` |

---

## Solución de problemas comunes

### El contenedor `app` no inicia
```bash
docker compose logs app
# Verificar que MySQL esté healthy antes
docker compose logs mysql
```

### Error de conexión a la base de datos
```bash
# Verificar que config/.env fue generado correctamente
docker exec sodicol_app cat config/.env
```

### Permisos de uploads
```bash
docker exec sodicol_app chown -R www-data:www-data /var/www/html/uploads
```

### Reiniciar solo un servicio
```bash
docker compose restart app
docker compose restart mysql
```

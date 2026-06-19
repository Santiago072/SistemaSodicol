# ─────────────────────────────────────────────────────────────
#  Dockerfile — Apache + PHP 8.1
#  Sistema: SODICOL | Dominio: slscode.online
# ─────────────────────────────────────────────────────────────

FROM php:8.1-apache

LABEL maintainer="SODICOL <slscode.online>" \
      version="1.0" \
      description="PHP 8.1 + Apache para el sistema SODICOL"

# ── Variables de build ────────────────────────────────────────
ARG DEBIAN_FRONTEND=noninteractive

# ── Dependencias del sistema ──────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        libcurl4-openssl-dev \
        unzip \
        curl \
        libmagickwand-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── Extensiones PHP requeridas ────────────────────────────────
# - mysqli      → conexión a MySQL (usada en conexion.php)
# - pdo_mysql   → PDO (Database.php model)
# - gd          → generación de imágenes para dompdf
# - zip         → dompdf / archivos comprimidos
# - mbstring    → manipulación de cadenas unicode
# - xml         → XML parsing (dompdf)
# - curl        → peticiones HTTP (posibles integraciones)
# - fileinfo    → validación de tipos MIME (seguridad de uploads)
# - opcache     → caché de bytecode para mejor rendimiento
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        mbstring \
        xml \
        curl \
        fileinfo \
        opcache

# ── Módulos Apache ────────────────────────────────────────────
RUN a2enmod rewrite headers expires

# ── Configuración PHP personalizada ──────────────────────────
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# ── Configuración VirtualHost Apache ─────────────────────────
COPY docker/apache/sodicol.conf /etc/apache2/sites-available/sodicol.conf
RUN a2dissite 000-default.conf \
    && a2ensite sodicol.conf

# ── Copiar código fuente del proyecto ────────────────────────
COPY . /var/www/html/

# ── Directorio de trabajo ─────────────────────────────────────
WORKDIR /var/www/html

# ── Crear directorios necesarios con permisos correctos ───────
RUN mkdir -p /var/www/html/logs \
             /var/www/html/uploads \
             /var/lib/php/sessions \
    && chown -R www-data:www-data \
        /var/www/html/logs \
        /var/www/html/uploads \
        /var/lib/php/sessions \
    && chmod 755 /var/www/html/uploads \
    && chmod 755 /var/www/html/logs \
    && chmod 777 /var/lib/php/sessions

# ── Permisos generales del proyecto ──────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -name "*.php" -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

# ── Entrypoint personalizado ──────────────────────────────────
COPY docker/apache/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]

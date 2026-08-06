# 🤝 Guía para Colaboradores — Sistema Sodicol

Gracias por tu interés en contribuir a **Sistema Sodicol**. Esta guía explica cómo configurar el entorno local, las convenciones que seguimos y el flujo de trabajo de ingeniería de software.

---

## ⚙️ Configuración del Entorno Local

### Prerrequisitos
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) o servidor [XAMPP](https://www.apachefriends.org/) (PHP 8.2+ y MySQL/MariaDB)
- [Composer](https://getcomposer.org/) (administrador de dependencias de PHP)
- [Git](https://git-scm.com/)

### Pasos iniciales

```bash
# 1. Clonar el repositorio
git clone https://github.com/Santiago072/SistemaSodicol.git
cd SistemaSodicol

# 2. Instalar dependencias de Composer (incluyendo PHPUnit para desarrollo)
composer install

# 3. Configurar variables de entorno
cp .env.example config/.env
```

Edita `config/.env` con los parámetros de tu base de datos local.

---

## 🧪 Pruebas Automatizadas

El proyecto utiliza **PHPUnit 10** para garantizar la estabilidad y prevenir regresiones.

```bash
# Ejecutar todas las pruebas unitarias
composer test

# Ejecutar pruebas con salida detallada por test
composer test-verbose
```

### Reglas para escribir tests
- Las nuevas funciones de seguridad o controladores deben incluir sus correspondientes pruebas unitarias en `tests/Unit/`.
- No utilices conexiones a bases de datos reales dentro de las pruebas unitarias; usa Mocks de `mysqli` o clases de prueba en memoria.
- Todas las pruebas deben pasar exitosamente (0 errores, 0 fallos) antes de enviar un Pull Request.

---

## ✍️ Convención de Commits (en español)

Usamos el formato **Conventional Commits** en español:

```
<tipo>: <descripción corta en imperativo y minúsculas>

<cuerpo opcional — explica el QUÉ y el POR QUÉ>
```

### Tipos permitidos

| Tipo | Cuándo usarlo |
|---|---|
| `feat` | Nueva funcionalidad para el sistema |
| `fix` | Corrección de un bug o error |
| `test` | Adición o modificación de pruebas |
| `ci` | Cambios en workflows de GitHub Actions |
| `docs` | Modificación de documentación |
| `limpieza` | Refactorización de código sin cambio de comportamiento |
| `seguridad` | Ajustes o parches de seguridad |

### Ejemplos

```bash
# ✅ Correctos
git commit -m "test: agregar pruebas unitarias para verificacion de token CSRF"
git commit -m "fix: corregir calculo de IVA en desgloses de cotizacion PDF"
git commit -m "docs: agregar guia de colaboracion y actualizar CHANGELOG"

# ❌ Incorrectos
git commit -m "cambios varios"
git commit -m "fix bug"
git commit -m "WIP"
```

---

## 🔐 Reglas de Seguridad

> [!CAUTION]
> **Nunca** incluyas credenciales, contraseñas ni el archivo `config/.env` en tus commits.

1. Toda entrada de usuario procesada por controladores debe ser sanitizada con `sanitizar_entrada()`.
2. Toda salida hacia vistas HTML debe ser escapada con `escapar_salida()`.
3. Todas las consultas SQL a la base de datos deben usar `mysqli_prepare` (prepared statements) sin excepción.
4. Las operaciones mutables (`POST`) deben requerir un token CSRF válido verificado con `verificar_token_csrf()`.

---

## 🌿 Flujo de Trabajo con Git

1. Crea una rama descriptiva a partir de `main`:
   ```bash
   git checkout -b feature/nueva-funcionalidad
   ```
2. Realiza cambios pequeños y enfocados con commits claros.
3. Asegúrate de que las pruebas pasen (`composer test`).
4. Abre un Pull Request describiendo los cambios introducidos.

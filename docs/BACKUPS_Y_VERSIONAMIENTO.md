# Gestión de Datos y Versionamiento

Este documento detalla las prácticas establecidas a partir de la versión `v1.0.0` para garantizar la integridad de los datos de producción y el registro histórico del desarrollo de la plataforma SistemaSodicol.

---

## 1. Persistencia de Datos (Volúmenes de Docker)

En nuestro entorno de producción (VPS), la base de datos MySQL se ejecuta dentro de un contenedor Docker aislado. Si el contenedor se detiene, se reinicia o se elimina, **los datos no se pierden**. 

Esto es gracias a que en el archivo `docker-compose.yml` declaramos el volumen `sodicol_mysql_data` asociado a la carpeta interna de MySQL (`/var/lib/mysql`). Esto significa que la base de datos en realidad vive físicamente en el disco duro de tu VPS y no dentro del contenedor efímero.

**¿Qué pasa si destruyo el volumen?**
Solo se perderá la información si se ejecuta explícitamente el comando:
```bash
docker compose down -v
```
*(Ese comando con `-v` o `--volumes` es el único que destruye la información y obliga a la base de datos a arrancar de cero leyendo el archivo `init.sql`)*.

---

## 2. Copias de Seguridad Automáticas (Backups)

Para mayor seguridad ante fallas del servidor, errores humanos o necesidad de migrar la plataforma, hemos creado un script de backups fácil de usar.

### Cómo ejecutar un Backup

1. Accede por terminal a tu VPS.
2. Navega a la carpeta del proyecto y luego a la carpeta docker:
   ```bash
   cd ~/projects/SistemaSodicol/docker
   ```
3. Otorga permisos de ejecución al script (solo la primera vez):
   ```bash
   chmod +x backup.sh
   ```
4. Ejecuta el script:
   ```bash
   ./backup.sh
   ```

### ¿Qué hace el script?
1. Se conecta al contenedor `sodicol_mysql`.
2. Utiliza la herramienta `mysqldump` con las credenciales extraídas de tu `.env` de forma segura.
3. Genera un archivo `.sql` en la carpeta `/database/backups/` con la fecha y hora actual (ej. `backup_sistema_sodicol_20260620_150000.sql`).
4. **Protección de espacio**: Borra automáticamente los backups muy viejos, conservando únicamente los 10 más recientes para no llenar el disco duro de tu servidor.

### Cómo restaurar un Backup
Si algún día ocurre un desastre y necesitas restaurar tu base de datos desde un archivo `.sql` generado, puedes hacerlo de la siguiente forma (estando en la carpeta `docker`):

```bash
docker exec -i sodicol_mysql mysql -u sodicol -p"TU_CONTRASEÑA" sistema_sodicol < ../database/backups/TU_ARCHIVO_DE_BACKUP.sql
```
*(Sustituyendo la contraseña y el nombre del archivo correspondientes)*.

---

## 3. Versionamiento Semántico (SemVer)

A partir del despliegue en el VPS mediante Docker, la plataforma ha alcanzado el hito de la versión **v1.0.0**.

Toda actualización futura del código debe quedar registrada en el archivo `CHANGELOG.md` en la raíz del proyecto. Seguiremos la nomenclatura estándar `Mayor.Menor.Parche` (ej: `v1.2.3`):

- **Mayor (1.x.x):** Cambios muy grandes que modifican radicalmente cómo funciona el sistema o rompen compatibilidad con versiones previas de la base de datos.
- **Menor (x.1.x):** Nuevas funcionalidades (ej. Añadir un módulo de reportes en PDF).
- **Parche (x.x.1):** Solución de errores (bugs) menores o ajustes visuales.

### ¿Cómo actualizar el Changelog?
Cada vez que hagas o se hagan cambios importantes, abre `CHANGELOG.md` y añade la nueva versión en la parte superior junto con la fecha. Categoriza los cambios en:
- `### Agregado` (Nuevas funciones)
- `### Modificado` (Cambios en funciones existentes)
- `### Corregido` (Bugs arreglados)
- `### Eliminado` (Funciones que se quitaron)

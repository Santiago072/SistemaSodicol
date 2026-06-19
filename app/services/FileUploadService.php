<?php
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * FileUploadService — Responsabilidad Única: manejar la subida y reemplazo de archivos.
 *
 * Principios aplicados:
 *   - SRP: toda la lógica de archivos vive aquí. Los controllers no saben
 *     cómo se mueven o validan los archivos, solo llaman a este servicio.
 *   - DRY: elimina el código duplicado de procesarFoto() y procesarFotoEdicion()
 *     que existía en CotizacionController.
 *   - OCP: para cambiar la lógica de subida (p.ej. subir a S3) solo se modifica
 *     esta clase, sin tocar ningún controller.
 */
class FileUploadService
{
    private string $uploadDir;

    /**
     * @param string $uploadDir Ruta absoluta al directorio de subidas.
     */
    public function __construct(string $uploadDir)
    {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
    }

    /**
     * Sube un nuevo archivo y devuelve su nombre en disco.
     * Si no hay archivo en $_FILES o falla la validación, devuelve el nombre actual.
     *
     * @param array  $fileInput  El elemento de $_FILES correspondiente.
     * @param string $nombreActual Nombre del archivo actual (fallback si no se sube nada).
     * @return string Nombre del archivo resultante (sin ruta).
     */
    public function subir(array $fileInput, string $nombreActual = ''): string
    {
        if (!isset($fileInput['error']) || $fileInput['error'] !== UPLOAD_ERR_OK) {
            return basename($nombreActual);
        }

        $validacion = validar_imagen($fileInput);
        if (!$validacion['valido']) {
            return basename($nombreActual);
        }

        $ext    = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
        $nombre = generar_nombre_archivo($ext);

        $this->asegurarDirectorio();

        if (move_uploaded_file($fileInput['tmp_name'], $this->uploadDir . $nombre)) {
            return $nombre;
        }

        return basename($nombreActual);
    }

    /**
     * Reemplaza un archivo existente con uno nuevo.
     * Si la subida es exitosa, elimina el archivo anterior del disco.
     * Si no se sube ningún archivo, devuelve el nombre actual sin cambios.
     *
     * @param array  $fileInput    El elemento de $_FILES correspondiente.
     * @param string $nombreActual Nombre del archivo actual a reemplazar.
     * @return string Nombre del archivo resultante (sin ruta).
     */
    public function reemplazar(array $fileInput, string $nombreActual = ''): string
    {
        if (!isset($fileInput['error']) || $fileInput['error'] !== UPLOAD_ERR_OK) {
            return basename($nombreActual);
        }

        $validacion = validar_imagen($fileInput);
        if (!$validacion['valido']) {
            return basename($nombreActual);
        }

        $ext    = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
        $nombre = generar_nombre_archivo($ext);

        $this->asegurarDirectorio();

        if (move_uploaded_file($fileInput['tmp_name'], $this->uploadDir . $nombre)) {
            // Eliminar archivo anterior solo si la subida fue exitosa
            $this->eliminarSiExiste($nombreActual);
            return $nombre;
        }

        return basename($nombreActual);
    }

    /**
     * Elimina un archivo del directorio de subidas si existe.
     *
     * @param string $nombre Nombre del archivo (con o sin ruta).
     */
    public function eliminarSiExiste(string $nombre): void
    {
        if (empty($nombre)) {
            return;
        }
        $ruta = $this->uploadDir . basename($nombre);
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }

    /**
     * Crea el directorio de subidas si no existe.
     */
    private function asegurarDirectorio(): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
}

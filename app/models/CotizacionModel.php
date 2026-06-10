<?php
/**
 * CotizacionModel — acceso a datos de cotizaciones y sus ítems.
 */
class CotizacionModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // ──────────────────── CABECERA ────────────────────

    public function buscarPorId(int $id): ?array {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM cotizaciones WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function buscarPorNumero(string $numero): ?array {
        $stmt = mysqli_prepare($this->db,
            "SELECT * FROM cotizaciones WHERE numero_cotizacion = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $numero);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    /** Buscar borrador existente (con ítems, sin número) del usuario */
    public function buscarBorradorConItems(string $usuarioNombre): ?int {
        $stmt = mysqli_prepare($this->db,
            "SELECT c.id FROM cotizaciones c
             INNER JOIN cotizacion_items i ON c.id = i.cotizacion_id
             WHERE c.usuario_nombre = ?
             AND (c.numero_cotizacion IS NULL OR c.numero_cotizacion = '')
             ORDER BY c.id DESC LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $usuarioNombre);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ? (int)$row['id'] : null;
    }

    /** Buscar cabecera vacía sin número del usuario */
    public function buscarCabeceraVacia(string $usuarioNombre): ?int {
        $stmt = mysqli_prepare($this->db,
            "SELECT id FROM cotizaciones
             WHERE usuario_nombre = ?
             AND (numero_cotizacion IS NULL OR numero_cotizacion = '')
             ORDER BY id DESC LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $usuarioNombre);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ? (int)$row['id'] : null;
    }

    public function crearCabecera(string $usuarioNombre): int {
        $stmt = mysqli_prepare($this->db,
            "INSERT INTO cotizaciones (usuario_nombre) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $usuarioNombre);
        mysqli_stmt_execute($stmt);
        $id = (int)mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);
        return $id;
    }

    /**
     * Actualizar cabecera con datos del cliente y asignar número.
     * Usa transacción para evitar colisiones de número concurrentes.
     */
    public function finalizarCotizacion(int $id, string $fecha, string $profesion,
                                         string $nombreCliente, string $especialidad,
                                         string $entidad, string $ciudad): string {
        mysqli_begin_transaction($this->db);
        try {
            // Número único: bloqueo a nivel de tabla para evitar concurrencia
            mysqli_query($this->db, "LOCK TABLES cotizaciones WRITE");

            $prefijo_mes  = date('Ym');
            $prefijo_like = $prefijo_mes . '%';
            $stmt_cnt = mysqli_prepare($this->db,
                "SELECT COUNT(*) AS total FROM cotizaciones WHERE numero_cotizacion LIKE ?");
            mysqli_stmt_bind_param($stmt_cnt, "s", $prefijo_like);
            mysqli_stmt_execute($stmt_cnt);
            $res_cnt = mysqli_stmt_get_result($stmt_cnt);
            $cnt     = (int)mysqli_fetch_assoc($res_cnt)['total'];
            mysqli_stmt_close($stmt_cnt);

            $consecutivo       = $cnt + 1;
            $numero_cotizacion = date('Ymd') . str_pad($consecutivo, 2, '0', STR_PAD_LEFT);

            $stmt_upd = mysqli_prepare($this->db,
                "UPDATE cotizaciones
                 SET fecha_creacion=?,profesion=?,nombre_cliente=?,
                     especialidad=?,entidad=?,ciudad=?,numero_cotizacion=?
                 WHERE id=?");
            mysqli_stmt_bind_param($stmt_upd, "sssssssi",
                $fecha, $profesion, $nombreCliente,
                $especialidad, $entidad, $ciudad, $numero_cotizacion, $id);
            mysqli_stmt_execute($stmt_upd);
            mysqli_stmt_close($stmt_upd);

            mysqli_query($this->db, "UNLOCK TABLES");
            mysqli_commit($this->db);
            return $numero_cotizacion;
        } catch (Exception $e) {
            mysqli_query($this->db, "UNLOCK TABLES");
            mysqli_rollback($this->db);
            throw $e;
        }
    }

    /** Buscar cotizaciones con filtros dinámicos y paginación */
    public function buscarConFiltros(array $filtros, int $offset, int $limite): array {
        [$where, $params, $types] = $this->construirWhere($filtros);
        $sql = "SELECT * FROM cotizaciones" . ($where ? " WHERE $where" : "") .
               " ORDER BY id DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limite;
        $params[] = $offset;

        $stmt = mysqli_prepare($this->db, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function contarConFiltros(array $filtros): int {
        [$where, $params, $types] = $this->construirWhere($filtros);
        $sql = "SELECT COUNT(*) AS total FROM cotizaciones" . ($where ? " WHERE $where" : "");
        $stmt = mysqli_prepare($this->db, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    private function construirWhere(array $filtros): array {
        $condiciones = [];
        $params = [];
        $types  = '';

        if (!empty($filtros['fecha'])) {
            $condiciones[] = "fecha_creacion = ?";
            $params[] = $filtros['fecha'];
            $types   .= 's';
        }
        if (!empty($filtros['nombre_cliente'])) {
            $condiciones[] = "nombre_cliente LIKE ?";
            $params[] = "%" . $filtros['nombre_cliente'] . "%";
            $types   .= 's';
        }
        if (!empty($filtros['numero_cotizacion'])) {
            $condiciones[] = "numero_cotizacion LIKE ?";
            $params[] = "%" . $filtros['numero_cotizacion'] . "%";
            $types   .= 's';
        }

        $where = count($condiciones) > 0 ? implode(' AND ', $condiciones) : '';
        return [$where, $params, $types];
    }

    // ──────────────────── ÍTEMS ────────────────────

    public function obtenerItems(int $cotizacionId): array {
        $stmt = mysqli_prepare($this->db,
            "SELECT * FROM cotizacion_items WHERE cotizacion_id = ? ORDER BY id ASC");
        mysqli_stmt_bind_param($stmt, "i", $cotizacionId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function contarItems(int $cotizacionId): int {
        $stmt = mysqli_prepare($this->db,
            "SELECT COUNT(*) AS total FROM cotizacion_items WHERE cotizacion_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $cotizacionId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    public function buscarItemPorId(int $itemId, int $cotizacionId): ?array {
        $stmt = mysqli_prepare($this->db,
            "SELECT * FROM cotizacion_items WHERE id = ? AND cotizacion_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $itemId, $cotizacionId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function insertarItem(int $cotizacionId, string $titulo, string $foto,
                                  string $descripcion, int $cantidad,
                                  string $iva, float $precio): bool {
        $stmt = mysqli_prepare($this->db,
            "INSERT INTO cotizacion_items (cotizacion_id,titulo,foto,descripcion,cantidad,iva,precio)
             VALUES (?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "isssisd",
            $cotizacionId, $titulo, $foto, $descripcion, $cantidad, $iva, $precio);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function actualizarItem(int $itemId, int $cotizacionId, string $titulo,
                                    string $foto, string $descripcion, int $cantidad,
                                    string $iva, float $precio): bool {
        $stmt = mysqli_prepare($this->db,
            "UPDATE cotizacion_items
             SET titulo=?,foto=?,descripcion=?,cantidad=?,iva=?,precio=?
             WHERE id=? AND cotizacion_id=?");
        mysqli_stmt_bind_param($stmt, "sssissii",
            $titulo, $foto, $descripcion, $cantidad, $iva, $precio, $itemId, $cotizacionId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function eliminarItem(int $itemId): bool {
        $stmt = mysqli_prepare($this->db,
            "DELETE FROM cotizacion_items WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $itemId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}

<?php
/**
 * CotizacionModel — acceso a datos de cotizaciones y sus ítems.
 *
 * Principios aplicados:
 *   - SRP: toda la lógica de acceso a datos de cotizaciones vive aquí.
 *   - Type hints completos en todos los métodos.
 *
 * Nota: CotizacionModel no implementa RepositoryInterface directamente
 * porque su entidad principal tiene dos tablas (cotizaciones + cotizacion_items)
 * con operaciones muy distintas. ISP: no forzar métodos que no aplican.
 */
class CotizacionModel
{
    private \mysqli $db;

    public function __construct(\mysqli $conexion)
    {
        $this->db = $conexion;
    }

    // ── Contadores ────────────────────────────────────────────────────────────

    /** Total de cotizaciones finalizadas de un usuario (para dashboard) */
    public function contarDelUsuario(string $usuarioNombre): int
    {
        $stmt = mysqli_prepare($this->db,
            "SELECT COUNT(*) AS total FROM cotizaciones
             WHERE numero_cotizacion IS NOT NULL AND numero_cotizacion != ''
               AND nombre_cliente IS NOT NULL AND nombre_cliente != ''
               AND usuario_nombre = ?");
        mysqli_stmt_bind_param($stmt, 's', $usuarioNombre);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    // ── Cabecera ──────────────────────────────────────────────────────────────

    public function buscarPorId(int $id): ?array
    {
        $stmt = mysqli_prepare($this->db, 'SELECT * FROM cotizaciones WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function buscarPorNumero(string $numero): ?array
    {
        $stmt = mysqli_prepare($this->db,
            'SELECT * FROM cotizaciones WHERE numero_cotizacion = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $numero);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    /** Borrador con ítems, sin número, del usuario */
    public function buscarBorradorConItems(string $usuarioNombre): ?int
    {
        $stmt = mysqli_prepare($this->db,
            "SELECT c.id FROM cotizaciones c
             INNER JOIN cotizacion_items i ON c.id = i.cotizacion_id
             WHERE c.usuario_nombre = ?
               AND (c.numero_cotizacion IS NULL OR c.numero_cotizacion = '')
             ORDER BY c.id DESC LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $usuarioNombre);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ? (int)$row['id'] : null;
    }

    /** Cabecera vacía sin número del usuario */
    public function buscarCabeceraVacia(string $usuarioNombre): ?int
    {
        $stmt = mysqli_prepare($this->db,
            "SELECT id FROM cotizaciones
             WHERE usuario_nombre = ?
               AND (numero_cotizacion IS NULL OR numero_cotizacion = '')
             ORDER BY id DESC LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $usuarioNombre);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ? (int)$row['id'] : null;
    }

    public function crearCabecera(string $usuarioNombre): int
    {
        $stmt = mysqli_prepare($this->db,
            'INSERT INTO cotizaciones (usuario_nombre) VALUES (?)');
        mysqli_stmt_bind_param($stmt, 's', $usuarioNombre);
        mysqli_stmt_execute($stmt);
        $id = (int)mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);
        return $id;
    }

    /**
     * Finaliza la cotización asignando número único.
     * Usa transacción + LOCK para evitar colisiones en entornos concurrentes.
     */
    public function finalizarCotizacion(int $id, string $fecha, string $profesion,
                                        string $nombreCliente, string $especialidad,
                                        string $entidad, string $ciudad): string
    {
        mysqli_begin_transaction($this->db);
        try {
            $prefijoLike = date('Ym') . '%';
            
            // SELECT FOR UPDATE para bloquear las filas de cotizaciones y evitar condiciones de carrera, sin requerir privilegio LOCK TABLES
            $stmtCnt = mysqli_prepare($this->db,
                'SELECT COUNT(*) AS total FROM cotizaciones WHERE numero_cotizacion LIKE ? FOR UPDATE');
            mysqli_stmt_bind_param($stmtCnt, 's', $prefijoLike);
            mysqli_stmt_execute($stmtCnt);
            $resCnt = mysqli_stmt_get_result($stmtCnt);
            $cnt = (int)mysqli_fetch_assoc($resCnt)['total'];
            mysqli_stmt_close($stmtCnt);

            $numeroCotizacion = date('Ymd') . str_pad($cnt + 1, 2, '0', STR_PAD_LEFT);

            $stmtUpd = mysqli_prepare($this->db,
                'UPDATE cotizaciones
                 SET fecha_creacion=?,profesion=?,nombre_cliente=?,
                     especialidad=?,entidad=?,ciudad=?,numero_cotizacion=?
                 WHERE id=?');
            mysqli_stmt_bind_param($stmtUpd, 'sssssssi',
                $fecha, $profesion, $nombreCliente,
                $especialidad, $entidad, $ciudad, $numeroCotizacion, $id);
            mysqli_stmt_execute($stmtUpd);
            mysqli_stmt_close($stmtUpd);

            mysqli_commit($this->db);
            return $numeroCotizacion;
        } catch (\Exception $e) {
            mysqli_rollback($this->db);
            throw $e;
        }
    }

    // ── Búsqueda con filtros ──────────────────────────────────────────────────

    public function buscarConFiltros(array $filtros, int $offset, int $limite): array
    {
        [$where, $params, $types] = $this->construirWhere($filtros);
        $sql    = 'SELECT * FROM cotizaciones' . ($where ? " WHERE $where" : '') .
                  ' ORDER BY id DESC LIMIT ? OFFSET ?';
        $types .= 'ii';
        $params[] = $limite;
        $params[] = $offset;

        $stmt = mysqli_prepare($this->db, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows   = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function contarConFiltros(array $filtros): int
    {
        [$where, $params, $types] = $this->construirWhere($filtros);
        $sql  = 'SELECT COUNT(*) AS total FROM cotizaciones' . ($where ? " WHERE $where" : '');
        $stmt = mysqli_prepare($this->db, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    private function construirWhere(array $filtros): array
    {
        $condiciones = [];
        $params      = [];
        $types       = '';

        if (!empty($filtros['fecha'])) {
            $condiciones[] = 'fecha_creacion = ?';
            $params[]      = $filtros['fecha'];
            $types        .= 's';
        }
        if (!empty($filtros['nombre_cliente'])) {
            $condiciones[] = 'nombre_cliente LIKE ?';
            $params[]      = '%' . $filtros['nombre_cliente'] . '%';
            $types        .= 's';
        }
        if (!empty($filtros['numero_cotizacion'])) {
            $condiciones[] = 'numero_cotizacion LIKE ?';
            $params[]      = '%' . $filtros['numero_cotizacion'] . '%';
            $types        .= 's';
        }

        return [
            $condiciones ? implode(' AND ', $condiciones) : '',
            $params,
            $types,
        ];
    }

    // ── Ítems ─────────────────────────────────────────────────────────────────

    public function obtenerItems(int $cotizacionId): array
    {
        $stmt = mysqli_prepare($this->db,
            'SELECT * FROM cotizacion_items WHERE cotizacion_id = ? ORDER BY id ASC');
        mysqli_stmt_bind_param($stmt, 'i', $cotizacionId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows   = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function contarItems(int $cotizacionId): int
    {
        $stmt = mysqli_prepare($this->db,
            'SELECT COUNT(*) AS total FROM cotizacion_items WHERE cotizacion_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $cotizacionId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    public function buscarItemPorId(int $itemId, int $cotizacionId): ?array
    {
        $stmt = mysqli_prepare($this->db,
            'SELECT * FROM cotizacion_items WHERE id = ? AND cotizacion_id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $itemId, $cotizacionId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function insertarItem(int $cotizacionId, string $titulo, string $foto,
                                 string $descripcion, int $cantidad,
                                 string $iva, float $precio): bool
    {
        $stmt = mysqli_prepare($this->db,
            'INSERT INTO cotizacion_items (cotizacion_id,titulo,foto,descripcion,cantidad,iva,precio)
             VALUES (?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'isssisd',
            $cotizacionId, $titulo, $foto, $descripcion, $cantidad, $iva, $precio);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function actualizarItem(int $itemId, int $cotizacionId, string $titulo,
                                   string $foto, string $descripcion, int $cantidad,
                                   string $iva, float $precio): bool
    {
        $stmt = mysqli_prepare($this->db,
            'UPDATE cotizacion_items
             SET titulo=?,foto=?,descripcion=?,cantidad=?,iva=?,precio=?
             WHERE id=? AND cotizacion_id=?');
        mysqli_stmt_bind_param($stmt, 'sssissii',
            $titulo, $foto, $descripcion, $cantidad, $iva, $precio, $itemId, $cotizacionId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function eliminarItem(int $itemId): bool
    {
        $stmt = mysqli_prepare($this->db, 'DELETE FROM cotizacion_items WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $itemId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}

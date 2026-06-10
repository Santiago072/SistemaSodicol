<?php
/**
 * TareaModel — acceso a datos de la tabla tareas.
 */
class TareaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /** Listar todas las tareas con nombre de usuario, con paginación */
    public function listarTodas(int $offset, int $limite): array {
        $stmt = mysqli_prepare($this->db,
            "SELECT t.*, u.nombre FROM tareas t
             JOIN usuarios u ON t.usuario_id = u.id
             ORDER BY t.id DESC LIMIT ? OFFSET ?");
        mysqli_stmt_bind_param($stmt, "ii", $limite, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function contarTodas(): int {
        $result = mysqli_query($this->db, "SELECT COUNT(*) AS total FROM tareas");
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }

    /** Tareas pendientes de un usuario específico */
    public function listarPendientesDeUsuario(int $usuarioId): array {
        $stmt = mysqli_prepare($this->db,
            "SELECT * FROM tareas WHERE usuario_id = ? AND estado = 'pendiente'");
        mysqli_stmt_bind_param($stmt, "i", $usuarioId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function buscarPorId(int $id): ?array {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM tareas WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function crear(int $usuarioId, string $descripcion, string $estado): bool {
        $stmt = mysqli_prepare($this->db,
            "INSERT INTO tareas (usuario_id, descripcion_tarea, estado) VALUES (?,?,?)");
        mysqli_stmt_bind_param($stmt, "iss", $usuarioId, $descripcion, $estado);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function actualizar(int $id, int $usuarioId, string $descripcion, string $estado): bool {
        $stmt = mysqli_prepare($this->db,
            "UPDATE tareas SET usuario_id=?,descripcion_tarea=?,estado=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "issi", $usuarioId, $descripcion, $estado, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function eliminar(int $id): bool {
        $stmt = mysqli_prepare($this->db, "DELETE FROM tareas WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function completar(int $id, int $usuarioId): bool {
        $stmt = mysqli_prepare($this->db,
            "UPDATE tareas SET estado = 'completo' WHERE id = ? AND usuario_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $usuarioId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}

<?php
require_once dirname(__DIR__, 2) . '/app/contracts/RepositoryInterface.php';

/**
 * UsuarioModel — acceso a datos de la tabla usuarios.
 *
 * Implementa RepositoryInterface (ISP): contrato formal de repositorio.
 * Toda consulta SQL de usuarios vive aquí (SRP).
 */
class UsuarioModel implements RepositoryInterface
{
    private \mysqli $db;

    public function __construct(\mysqli $conexion)
    {
        $this->db = $conexion;
    }

    /** Obtener usuarios con paginación y búsqueda opcional */
    public function listar(int $offset, int $limite, string $busqueda = ''): array
    {
        if ($busqueda !== '') {
            $param = "%$busqueda%";
            $stmt  = mysqli_prepare($this->db,
                'SELECT * FROM usuarios WHERE nombre LIKE ? ORDER BY nombre LIMIT ? OFFSET ?');
            mysqli_stmt_bind_param($stmt, 'sii', $param, $limite, $offset);
        } else {
            $stmt = mysqli_prepare($this->db,
                'SELECT * FROM usuarios ORDER BY nombre LIMIT ? OFFSET ?');
            mysqli_stmt_bind_param($stmt, 'ii', $limite, $offset);
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

    /** Total de usuarios para paginación */
    public function contar(string $busqueda = ''): int
    {
        if ($busqueda !== '') {
            $param = "%$busqueda%";
            $stmt  = mysqli_prepare($this->db,
                'SELECT COUNT(*) AS total FROM usuarios WHERE nombre LIKE ?');
            mysqli_stmt_bind_param($stmt, 's', $param);
        } else {
            $stmt = mysqli_prepare($this->db, 'SELECT COUNT(*) AS total FROM usuarios');
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = mysqli_prepare($this->db, 'SELECT * FROM usuarios WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    /** Busca solo por correo y estado activo — para autenticación */
    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = mysqli_prepare($this->db,
            "SELECT id, nombre, correo, password, rol
             FROM usuarios WHERE correo = ? AND estado = 'activo'");
        mysqli_stmt_bind_param($stmt, 's', $correo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function existeDocumentoOCorreo(string $documento, string $correo, int $excluirId = 0): bool
    {
        $stmt = mysqli_prepare($this->db,
            'SELECT id FROM usuarios WHERE (documento = ? OR correo = ?) AND id != ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'ssi', $documento, $correo, $excluirId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existe = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $existe;
    }

    public function crear(string $doc, string $nombre, string $correo,
                          string $password, string $telefono, string $rol): bool
    {
        $stmt = mysqli_prepare($this->db,
            'INSERT INTO usuarios (documento, nombre, correo, password, telefono, rol)
             VALUES (?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'ssssss', $doc, $nombre, $correo, $password, $telefono, $rol);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function actualizar(int $id, string $doc, string $nombre, string $correo,
                               string $telefono, string $rol, string $estado,
                               ?string $passwordHash = null): bool
    {
        if ($passwordHash !== null) {
            $stmt = mysqli_prepare($this->db,
                'UPDATE usuarios SET documento=?,nombre=?,correo=?,password=?,telefono=?,rol=?,estado=? WHERE id=?');
            mysqli_stmt_bind_param($stmt, 'sssssssi',
                $doc, $nombre, $correo, $passwordHash, $telefono, $rol, $estado, $id);
        } else {
            $stmt = mysqli_prepare($this->db,
                'UPDATE usuarios SET documento=?,nombre=?,correo=?,telefono=?,rol=?,estado=? WHERE id=?');
            mysqli_stmt_bind_param($stmt, 'ssssssi',
                $doc, $nombre, $correo, $telefono, $rol, $estado, $id);
        }
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function eliminar(int $id): bool
    {
        $stmt = mysqli_prepare($this->db, 'DELETE FROM usuarios WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function contarAdmins(): int
    {
        $result = mysqli_query($this->db,
            "SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'admin'");
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }

    /** Usuarios activos para selects/dropdowns */
    public function listarActivos(): array
    {
        $result = mysqli_query($this->db,
            "SELECT id, nombre FROM usuarios WHERE estado = 'activo' ORDER BY nombre ASC");
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}

<?php
require_once dirname(__DIR__, 2) . '/app/contracts/RepositoryInterface.php';

/**
 * ProductoModel — acceso a datos de la tabla productos.
 *
 * Implementa RepositoryInterface (ISP): contrato formal de repositorio.
 * Toda consulta SQL de productos vive aquí (SRP).
 */
class ProductoModel implements RepositoryInterface
{
    private \mysqli $db;

    public function __construct(\mysqli $conexion)
    {
        $this->db = $conexion;
    }

    /** Listar productos con paginación y búsqueda opcional */
    public function listar(int $offset, int $limite, string $busqueda = ''): array
    {
        if ($busqueda !== '') {
            $param = "%$busqueda%";
            $stmt  = mysqli_prepare($this->db,
                'SELECT * FROM productos WHERE titulo LIKE ? ORDER BY titulo ASC LIMIT ? OFFSET ?');
            mysqli_stmt_bind_param($stmt, 'sii', $param, $limite, $offset);
        } else {
            $stmt = mysqli_prepare($this->db,
                'SELECT * FROM productos ORDER BY titulo ASC LIMIT ? OFFSET ?');
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

    /** Listar todos los productos (para selects en cotización) */
    public function listarTodos(string $busqueda = ''): array
    {
        if ($busqueda !== '') {
            $param = "%$busqueda%";
            $stmt  = mysqli_prepare($this->db,
                'SELECT * FROM productos WHERE titulo LIKE ? ORDER BY titulo ASC');
            mysqli_stmt_bind_param($stmt, 's', $param);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
        } else {
            $result = mysqli_query($this->db, 'SELECT * FROM productos ORDER BY titulo ASC');
        }
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function contar(string $busqueda = ''): int
    {
        if ($busqueda !== '') {
            $param = "%$busqueda%";
            $stmt  = mysqli_prepare($this->db,
                'SELECT COUNT(*) AS total FROM productos WHERE titulo LIKE ?');
            mysqli_stmt_bind_param($stmt, 's', $param);
        } else {
            $stmt = mysqli_prepare($this->db, 'SELECT COUNT(*) AS total FROM productos');
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = mysqli_prepare($this->db, 'SELECT * FROM productos WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function actualizar(int $id, string $titulo, string $foto,
                               string $descripcion, int $cantidad,
                               string $iva, float $precio): bool
    {
        $stmt = mysqli_prepare($this->db,
            'UPDATE productos SET titulo=?,foto=?,descripcion=?,cantidad=?,iva=?,precio=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'sssisdi', $titulo, $foto, $descripcion, $cantidad, $iva, $precio, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function eliminar(int $id): bool
    {
        $stmt = mysqli_prepare($this->db, 'DELETE FROM productos WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Verifica si una foto está referenciada en cotizaciones generadas */
    public function fotoEnUso(string $foto): bool
    {
        $param = '%' . basename($foto) . '%';
        $stmt  = mysqli_prepare($this->db,
            'SELECT COUNT(*) AS total FROM cotizacion_items WHERE foto LIKE ?');
        mysqli_stmt_bind_param($stmt, 's', $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0) > 0;
    }

    public function existePorTitulo(string $titulo, int $excluirId = 0): bool
    {
        $stmt = mysqli_prepare($this->db,
            'SELECT id FROM productos WHERE titulo = ? AND id != ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'si', $titulo, $excluirId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existe = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $existe;
    }

    public function crear(string $titulo, string $foto, string $descripcion,
                          int $cantidad, string $iva, float $precio): bool
    {
        $stmt = mysqli_prepare($this->db,
            'INSERT INTO productos (titulo, foto, descripcion, cantidad, iva, precio) VALUES (?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'sssisd', $titulo, $foto, $descripcion, $cantidad, $iva, $precio);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}

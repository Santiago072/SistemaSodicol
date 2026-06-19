<?php
/**
 * RepositoryInterface — Contrato formal para todos los modelos de datos.
 *
 * Principios aplicados:
 *   - ISP (Interface Segregation): define solo los métodos comunes mínimos.
 *     Cada modelo puede declarar métodos adicionales propios sin forzar
 *     a los demás a implementarlos.
 *   - OCP: el sistema puede añadir nuevos repositorios sin modificar los existentes.
 */
interface RepositoryInterface
{
    /**
     * Busca un registro por su ID primario.
     *
     * @param int $id
     * @return array|null El registro como array asociativo, o null si no existe.
     */
    public function buscarPorId(int $id): ?array;

    /**
     * Elimina un registro por su ID primario.
     *
     * @param int $id
     * @return bool True si se eliminó al menos una fila.
     */
    public function eliminar(int $id): bool;
}

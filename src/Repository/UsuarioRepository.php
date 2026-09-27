<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use BovinNet\Config\Conexion;
use PDO;

/**
 * Repositorio de acceso a datos para Usuario, usado unicamente por
 * el servicio de autenticacion (POST /api/auth/login).
 */
class UsuarioRepository
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtener();
    }

    /**
     * Busca un usuario activo por cedula. Retorna el arreglo con sus
     * datos (incluido el hash de la contrasena) o null si no existe.
     */
    public function buscarPorCedula(string $cedula): ?array
    {
        $sentencia = $this->conexion->prepare(
            'SELECT id_usuario, nombres, apellidos, cedula, contrasena_hash, estado, id_rol
             FROM usuario WHERE cedula = :cedula AND estado = "activo"'
        );
        $sentencia->execute(['cedula' => $cedula]);
        $fila = $sentencia->fetch(PDO::FETCH_ASSOC);

        return $fila ?: null;
    }
}

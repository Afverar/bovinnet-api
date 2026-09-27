<?php

declare(strict_types=1);

namespace BovinNet\Config;

use PDO;
use PDOException;

/**
 * Fabrica de conexiones PDO hacia la base de datos "bovinnet"
 * (MySQL), definida en las evidencias GA6-220501096-AA2-EV02/EV03.
 *
 * Centraliza los parametros de conexion para que ningun controlador
 * ni repositorio tenga que repetir la cadena de conexion.
 */
final class Conexion
{
    private const HOST = '127.0.0.1';
    private const PUERTO = '3306';
    private const BASE_DATOS = 'bovinnet';
    private const USUARIO = 'root';
    private const CONTRASENA = '';

    private static ?PDO $instancia = null;

    private function __construct()
    {
        // Clase de utilidad: no se instancia directamente.
    }

    public static function obtener(): PDO
    {
        if (self::$instancia === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                self::HOST,
                self::PUERTO,
                self::BASE_DATOS
            );

            try {
                self::$instancia = new PDO(
                    $dsn,
                    self::USUARIO,
                    self::CONTRASENA,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $error) {
                throw new PDOException('No fue posible conectar a la base de datos: ' . $error->getMessage());
            }
        }

        return self::$instancia;
    }
}

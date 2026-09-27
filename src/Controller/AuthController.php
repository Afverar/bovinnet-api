<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Repository\UsuarioRepository;

/**
 * Controlador REST del servicio de autenticacion (/api/auth/login).
 */
class AuthController
{
    private UsuarioRepository $repositorio;

    public function __construct()
    {
        $this->repositorio = new UsuarioRepository();
    }

    /** POST /api/auth/login */
    public function login(): void
    {
        $cuerpo = file_get_contents('php://input');
        $datos = json_decode($cuerpo, true) ?? [];

        if (empty($datos['cedula']) || empty($datos['contrasena'])) {
            $this->responderJson(400, ['mensaje' => 'Debe enviar cedula y contrasena.']);
            return;
        }

        $usuario = $this->repositorio->buscarPorCedula((string) $datos['cedula']);

        if ($usuario === null || !password_verify((string) $datos['contrasena'], $usuario['contrasena_hash'])) {
            $this->responderJson(401, ['mensaje' => 'Credenciales invalidas.']);
            return;
        }

        // Token de sesion simplificado para efectos academicos; en un
        // entorno de produccion se emitiria un JWT firmado.
        $token = bin2hex(random_bytes(16));

        $this->responderJson(200, [
            'token' => $token,
            'usuario' => [
                'idUsuario' => (int) $usuario['id_usuario'],
                'nombres' => $usuario['nombres'],
                'apellidos' => $usuario['apellidos'],
                'idRol' => (int) $usuario['id_rol'],
            ],
        ]);
    }

    private function responderJson(int $codigoHttp, array $datos): void
    {
        http_response_code($codigoHttp);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    }
}

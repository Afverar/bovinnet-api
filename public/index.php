<?php

declare(strict_types=1);

/**
 * Autoloader PSR-4 minimo, sin depender de Composer, para que el
 * proyecto se pueda ejecutar de forma independiente con
 * "php -S localhost:8000" desde esta misma carpeta (public/).
 * Composer (composer.json) sigue siendo valido si se ejecuta
 * "composer install" y se desea usar vendor/autoload.php en su lugar.
 */
spl_autoload_register(function (string $clase): void {
    $prefijo = 'BovinNet\\';
    if (!str_starts_with($clase, $prefijo)) {
        return;
    }
    $rutaRelativa = str_replace('\\', '/', substr($clase, strlen($prefijo)));
    $archivo = __DIR__ . '/../src/' . $rutaRelativa . '.php';
    if (is_file($archivo)) {
        require $archivo;
    }
});

use BovinNet\Controller\AnimalController;
use BovinNet\Controller\AuthController;

/**
 * Punto de entrada unico de la API REST de BovinNet.
 *
 * Enrutador minimo (sin framework) que interpreta la URL y el metodo
 * HTTP de cada peticion y la despacha al controlador correspondiente.
 * Los servicios expuestos estan documentados en API_DOCUMENTATION.md.
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

$metodo = $_SERVER['REQUEST_METHOD'];
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ruta = preg_replace('#^.*/public#', '', $ruta ?? '');
$segmentos = array_values(array_filter(explode('/', $ruta)));

try {
    // POST /api/auth/login
    if ($metodo === 'POST' && ($segmentos[0] ?? '') === 'api' && ($segmentos[1] ?? '') === 'auth' && ($segmentos[2] ?? '') === 'login') {
        (new AuthController())->login();
        exit;
    }

    // /api/animales  y  /api/animales/{id}
    if (($segmentos[0] ?? '') === 'api' && ($segmentos[1] ?? '') === 'animales') {
        $controlador = new AnimalController();
        $idAnimal = isset($segmentos[2]) ? (int) $segmentos[2] : null;

        match (true) {
            $metodo === 'GET' && $idAnimal === null => $controlador->listar(),
            $metodo === 'GET' && $idAnimal !== null => $controlador->obtener($idAnimal),
            $metodo === 'POST' && $idAnimal === null => $controlador->crear(),
            $metodo === 'PUT' && $idAnimal !== null => $controlador->actualizar($idAnimal),
            $metodo === 'DELETE' && $idAnimal !== null => $controlador->eliminar($idAnimal),
            default => responderNoEncontrado(),
        };
        exit;
    }

    responderNoEncontrado();

} catch (\Throwable $error) {
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['mensaje' => 'Error interno del servidor.', 'detalle' => $error->getMessage()]);
}

function responderNoEncontrado(): void
{
    http_response_code(404);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['mensaje' => 'Servicio no encontrado.']);
}

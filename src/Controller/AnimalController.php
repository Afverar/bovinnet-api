<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Model\Animal;
use BovinNet\Repository\AnimalRepository;

/**
 * Controlador REST del recurso /api/animales.
 *
 * Traduce cada peticion HTTP en una operacion del AnimalRepository y
 * responde siempre en formato JSON con el codigo de estado HTTP
 * correspondiente, documentado en API_DOCUMENTATION.md.
 */
class AnimalController
{
    private AnimalRepository $repositorio;

    public function __construct()
    {
        $this->repositorio = new AnimalRepository();
    }

    /** GET /api/animales */
    public function listar(): void
    {
        $animales = $this->repositorio->listarTodos();

        $this->responderJson(200, array_map(
            fn (Animal $animal): array => $animal->aArreglo(),
            $animales
        ));
    }

    /** GET /api/animales/{id} */
    public function obtener(int $idAnimal): void
    {
        $animal = $this->repositorio->obtenerPorId($idAnimal);

        if ($animal === null) {
            $this->responderJson(404, ['mensaje' => 'No existe un animal con ese id.']);
            return;
        }

        $this->responderJson(200, $animal->aArreglo());
    }

    /** POST /api/animales */
    public function crear(): void
    {
        $datos = $this->leerCuerpoJson();
        $error = $this->validarDatos($datos);

        if ($error !== null) {
            $this->responderJson(400, ['mensaje' => $error]);
            return;
        }

        $animal = new Animal(
            null,
            $datos['arete'],
            $datos['nombre'] ?? null,
            $datos['sexo'],
            $datos['fechaNacimiento'] ?? null,
            (int) $datos['idLote']
        );

        $animalCreado = $this->repositorio->crear($animal);

        $this->responderJson(201, $animalCreado->aArreglo());
    }

    /** PUT /api/animales/{id} */
    public function actualizar(int $idAnimal): void
    {
        if ($this->repositorio->obtenerPorId($idAnimal) === null) {
            $this->responderJson(404, ['mensaje' => 'No existe un animal con ese id.']);
            return;
        }

        $datos = $this->leerCuerpoJson();
        $error = $this->validarDatos($datos);

        if ($error !== null) {
            $this->responderJson(400, ['mensaje' => $error]);
            return;
        }

        $animal = new Animal(
            $idAnimal,
            $datos['arete'],
            $datos['nombre'] ?? null,
            $datos['sexo'],
            $datos['fechaNacimiento'] ?? null,
            (int) $datos['idLote']
        );

        $this->repositorio->actualizar($idAnimal, $animal);

        $this->responderJson(200, $animal->aArreglo());
    }

    /** DELETE /api/animales/{id} */
    public function eliminar(int $idAnimal): void
    {
        $eliminado = $this->repositorio->eliminar($idAnimal);

        if (!$eliminado) {
            $this->responderJson(404, ['mensaje' => 'No existe un animal con ese id.']);
            return;
        }

        $this->responderJson(200, ['mensaje' => 'Animal eliminado correctamente.']);
    }

    private function validarDatos(array $datos): ?string
    {
        if (empty($datos['arete']) || empty($datos['sexo']) || empty($datos['idLote'])) {
            return 'Los campos arete, sexo e idLote son obligatorios.';
        }

        if (!in_array($datos['sexo'], ['Hembra', 'Macho'], true)) {
            return 'El campo sexo debe ser "Hembra" o "Macho".';
        }

        return null;
    }

    private function leerCuerpoJson(): array
    {
        $cuerpo = file_get_contents('php://input');
        $datos = json_decode($cuerpo, true);

        return is_array($datos) ? $datos : [];
    }

    private function responderJson(int $codigoHttp, array $datos): void
    {
        http_response_code($codigoHttp);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    }
}

<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use BovinNet\Config\Conexion;
use BovinNet\Model\Animal;
use PDO;

/**
 * Repositorio de acceso a datos para Animal. Implementa el CRUD
 * completo (crear, listar, obtener, actualizar, eliminar) que
 * consumen los servicios REST de AnimalController.
 */
class AnimalRepository
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtener();
    }

    public function listarTodos(): array
    {
        $sentencia = $this->conexion->query(
            'SELECT id_animal, arete, nombre, sexo, fecha_nacimiento, id_lote
             FROM animal ORDER BY id_animal'
        );

        return array_map(
            fn (array $fila): Animal => Animal::desdeArreglo($fila),
            $sentencia->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function obtenerPorId(int $idAnimal): ?Animal
    {
        $sentencia = $this->conexion->prepare(
            'SELECT id_animal, arete, nombre, sexo, fecha_nacimiento, id_lote
             FROM animal WHERE id_animal = :idAnimal'
        );
        $sentencia->execute(['idAnimal' => $idAnimal]);
        $fila = $sentencia->fetch(PDO::FETCH_ASSOC);

        return $fila ? Animal::desdeArreglo($fila) : null;
    }

    public function crear(Animal $animal): Animal
    {
        $sentencia = $this->conexion->prepare(
            'INSERT INTO animal (arete, nombre, sexo, fecha_nacimiento, id_lote)
             VALUES (:arete, :nombre, :sexo, :fechaNacimiento, :idLote)'
        );

        $sentencia->execute([
            'arete' => $animal->getArete(),
            'nombre' => $animal->getNombre(),
            'sexo' => $animal->getSexo(),
            'fechaNacimiento' => $animal->getFechaNacimiento(),
            'idLote' => $animal->getIdLote(),
        ]);

        $nuevoId = (int) $this->conexion->lastInsertId();

        return $this->obtenerPorId($nuevoId);
    }

    public function actualizar(int $idAnimal, Animal $animal): bool
    {
        $sentencia = $this->conexion->prepare(
            'UPDATE animal SET arete = :arete, nombre = :nombre, sexo = :sexo,
             fecha_nacimiento = :fechaNacimiento, id_lote = :idLote
             WHERE id_animal = :idAnimal'
        );

        return $sentencia->execute([
            'arete' => $animal->getArete(),
            'nombre' => $animal->getNombre(),
            'sexo' => $animal->getSexo(),
            'fechaNacimiento' => $animal->getFechaNacimiento(),
            'idLote' => $animal->getIdLote(),
            'idAnimal' => $idAnimal,
        ]) && $sentencia->rowCount() > 0;
    }

    public function eliminar(int $idAnimal): bool
    {
        $sentencia = $this->conexion->prepare('DELETE FROM animal WHERE id_animal = :idAnimal');
        $sentencia->execute(['idAnimal' => $idAnimal]);

        return $sentencia->rowCount() > 0;
    }
}

<?php

declare(strict_types=1);

namespace BovinNet\Model;

/**
 * Modelo de datos para la entidad Animal, correspondiente a la tabla
 * "animal" del modelo relacional de BovinNet (GA6-220501096-AA1-EV01).
 */
class Animal
{
    private ?int $idAnimal;
    private string $arete;
    private ?string $nombre;
    private string $sexo;
    private ?string $fechaNacimiento;
    private int $idLote;

    public function __construct(
        ?int $idAnimal,
        string $arete,
        ?string $nombre,
        string $sexo,
        ?string $fechaNacimiento,
        int $idLote
    ) {
        $this->idAnimal = $idAnimal;
        $this->arete = $arete;
        $this->nombre = $nombre;
        $this->sexo = $sexo;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->idLote = $idLote;
    }

    public static function desdeArreglo(array $fila): self
    {
        return new self(
            isset($fila['id_animal']) ? (int) $fila['id_animal'] : null,
            (string) $fila['arete'],
            $fila['nombre'] ?? null,
            (string) $fila['sexo'],
            $fila['fecha_nacimiento'] ?? null,
            (int) $fila['id_lote']
        );
    }

    public function aArreglo(): array
    {
        return [
            'idAnimal' => $this->idAnimal,
            'arete' => $this->arete,
            'nombre' => $this->nombre,
            'sexo' => $this->sexo,
            'fechaNacimiento' => $this->fechaNacimiento,
            'idLote' => $this->idLote,
        ];
    }

    public function getIdAnimal(): ?int
    {
        return $this->idAnimal;
    }

    public function getArete(): string
    {
        return $this->arete;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function getSexo(): string
    {
        return $this->sexo;
    }

    public function getFechaNacimiento(): ?string
    {
        return $this->fechaNacimiento;
    }

    public function getIdLote(): int
    {
        return $this->idLote;
    }
}

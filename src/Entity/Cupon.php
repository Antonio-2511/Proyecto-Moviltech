<?php

namespace App\Entity;

use App\Repository\CuponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CuponRepository::class)]
class Cupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Código único que el usuario introduce (ej: "VERANO10")
    #[ORM\Column(length: 50, unique: true)]
    private string $codigo;

    // Porcentaje de descuento (ej: 15 => 15%)
    #[ORM\Column]
    private float $porcentaje;

    // Indica si el cupón sigue activo
    #[ORM\Column]
    private bool $activo = true;

    // =========================
    // Getters y setters
    // =========================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): static
    {
        $this->codigo = strtoupper(trim($codigo));
        return $this;
    }

    public function getPorcentaje(): float
    {
        return $this->porcentaje;
    }

    public function setPorcentaje(float $porcentaje): static
    {
        $this->porcentaje = $porcentaje;
        return $this;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;
        return $this;
    }

    /**
     * Calcula el total con el descuento aplicado.
     */
    public function aplicarDescuento(float $total): float
    {
        return $total * (1 - $this->porcentaje / 100);
    }
}

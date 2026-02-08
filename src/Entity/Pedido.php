<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $fecha;

    #[ORM\Column(length: 20)]
    private string $estado;

    #[ORM\Column]
    private float $total;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\OneToMany(mappedBy: 'pedido', targetEntity: DetallePedido::class, cascade: ['persist', 'remove'])]
    private Collection $detalles;

    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable();
        $this->estado = 'pendiente';
        $this->total = 0;
        $this->detalles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): \DateTimeImmutable
    {
        return $this->fecha;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(User $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getDetalles(): Collection
    {
        return $this->detalles;
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Pedido
{
    // Identificador único del pedido (clave primaria)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Fecha de creación del pedido
    // Se usa DateTimeImmutable para evitar modificaciones accidentales
    #[ORM\Column]
    private \DateTimeImmutable $fecha;

    // Estado actual del pedido (pendiente, pagado, enviado, etc.)
    #[ORM\Column(length: 20)]
    private string $estado;

    // Importe total del pedido
    // Se calcula durante el checkout sumando los subtotales
    #[ORM\Column]
    private float $total;

    // Relación ManyToOne con User
    // Un usuario puede tener muchos pedidos
    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    // Relación OneToMany con DetallePedido
    // Un pedido contiene múltiples líneas de detalle
    // Cascade persist/remove permite que al guardar o borrar el pedido
    // se guarden o eliminen automáticamente sus detalles
    #[ORM\OneToMany(
        mappedBy: 'pedido',
        targetEntity: DetallePedido::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $detalles;

    public function __construct()
    {
        // Se inicializan valores por defecto
        $this->fecha = new \DateTimeImmutable();
        $this->estado = 'pendiente';
        $this->total = 0;
        $this->detalles = new ArrayCollection();
    }

    // =========================
    // Getters y setters
    // =========================

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

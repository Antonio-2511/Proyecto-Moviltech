<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DetallePedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $cantidad;

    #[ORM\Column]
    private float $precioUnitario;

    #[ORM\ManyToOne(inversedBy: 'detalles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pedido $pedido = null;

    #[ORM\ManyToOne(inversedBy: 'detallesPedido')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;
        return $this;
    }

    public function getPrecioUnitario(): float
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(float $precioUnitario): static
    {
        $this->precioUnitario = $precioUnitario;
        return $this;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function setPedido(Pedido $pedido): static
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(Producto $producto): static
    {
        $this->producto = $producto;
        return $this;
    }
}

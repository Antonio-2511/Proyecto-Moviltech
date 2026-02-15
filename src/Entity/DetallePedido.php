<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DetallePedido
{

    // Identificador único del detalle (clave primaria)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Cantidad de unidades del producto en el pedido
    #[ORM\Column]
    private int $cantidad;

    // Precio del producto en el momento de la compra
    // Se guarda aquí para mantener histórico aunque cambie el precio del producto
    #[ORM\Column]
    private float $precioUnitario;

    // Relación ManyToOne con Pedido
    // Un pedido puede tener muchos detalles
    #[ORM\ManyToOne(inversedBy: 'detalles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pedido $pedido = null;

    // Relación ManyToOne con Producto
    // Un producto puede aparecer en muchos detalles de pedido
    #[ORM\ManyToOne(inversedBy: 'detallesPedido')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    // =========================
    // Getters y setters
    // =========================

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

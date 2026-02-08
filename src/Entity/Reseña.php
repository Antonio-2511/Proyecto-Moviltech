<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Reseña
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $puntuacion;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comentario = null;

    #[ORM\Column]
    private \DateTimeImmutable $fecha;

    #[ORM\ManyToOne(inversedBy: 'reseñas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'reseñas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuntuacion(): int
    {
        return $this->puntuacion;
    }

    public function setPuntuacion(int $puntuacion): static
    {
        $this->puntuacion = $puntuacion;
        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): static
    {
        $this->comentario = $comentario;
        return $this;
    }

    public function getFecha(): \DateTimeImmutable
    {
        return $this->fecha;
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

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Resena
{
    // Identificador único de la reseña (clave primaria)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Puntuación otorgada al producto (por ejemplo de 1 a 5)
    #[ORM\Column]
    private int $puntuacion;

    // Comentario opcional del usuario
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comentario = null;

    // Fecha de creación de la reseña
    // Se establece automáticamente al crear la instancia
    #[ORM\Column]
    private \DateTimeImmutable $fecha;

    // Relación ManyToOne con User
    // Un usuario puede escribir muchas reseñas
    #[ORM\ManyToOne(inversedBy: 'resenas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    // Relación ManyToOne con Producto
    // Un producto puede tener múltiples reseñas
    #[ORM\ManyToOne(inversedBy: 'resenas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    public function __construct()
    {
        // Se asigna automáticamente la fecha actual al crear la reseña
        $this->fecha = new \DateTimeImmutable();
    }

    // =========================
    // Getters y setters
    // =========================

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

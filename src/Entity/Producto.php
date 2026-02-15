<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    // Identificador único del producto (clave primaria)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nombre del producto
    // Se valida que no esté vacío y tenga mínimo 3 caracteres
    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "El nombre no puede estar vacío")]
    #[Assert\Length(min: 3, minMessage: "El nombre debe tener al menos 3 caracteres")]
    private ?string $nombre = null;

    // Descripción detallada del producto
    // Obligatoria y con mínimo 10 caracteres
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La descripción es obligatoria")]
    #[Assert\Length(min: 10, minMessage: "La descripción debe tener al menos 10 caracteres")]
    private ?string $descripcion = null;

    // Precio unitario del producto
    // Debe ser mayor que 0
    #[ORM\Column]
    #[Assert\NotBlank(message: "El precio es obligatorio")]
    #[Assert\Positive(message: "El precio debe ser mayor que 0")]
    private ?float $precio = null;

    // Cantidad disponible en inventario
    // No puede ser negativa
    #[ORM\Column]
    #[Assert\NotBlank(message: "El stock es obligatorio")]
    #[Assert\PositiveOrZero(message: "El stock no puede ser negativo")]
    private ?int $stock = null;

    // Atributos opcionales del producto
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $marca = null;

    // Ruta o nombre del archivo de imagen del producto
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    // Relación ManyToOne con Categoria
    // Muchos productos pertenecen a una categoría
    // Es obligatoria (nullable: false)
    #[ORM\ManyToOne(inversedBy: 'productos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Debe seleccionar una categoría")]
    private ?Categoria $categoria = null;

    // Relación OneToMany con DetallePedido
    // Un producto puede aparecer en muchos detalles de pedido
    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: DetallePedido::class, orphanRemoval: true)]
    private Collection $detallesPedido;

    // Relación OneToMany con Reseña
    // Permite que un producto tenga múltiples reseñas
    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: Resena::class, orphanRemoval: true)]
    private Collection $resenas;

    public function __construct()
    {
        // Inicialización de colecciones para evitar null
        $this->detallesPedido = new ArrayCollection();
        $this->resenas = new ArrayCollection();
    }

    // =========================
    // Getters y setters
    // =========================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): static
    {
        $this->precio = $precio;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(?string $marca): static
    {
        $this->marca = $marca;
        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;
        return $this;
    }

    /**
     * Devuelve los detalles de pedido en los que aparece este producto
     */
    public function getDetallesPedido(): Collection
    {
        return $this->detallesPedido;
    }

    /**
     * Devuelve las reseñas asociadas al producto
     */
    public function getResenas(): Collection
    {
        return $this->resenas;
    }
}

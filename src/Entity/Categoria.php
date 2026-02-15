<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{

    // Identificador único de la categoría (clave primaria)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nombre de la categoría (ej: Smartphones, Tablets...)
    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    // Descripción opcional de la categoría
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    // Relación OneToMany con Producto
    // Una categoría puede tener muchos productos
    #[ORM\OneToMany(mappedBy: 'categoria', targetEntity: Producto::class)]
    private Collection $productos;

    public function __construct()
    {
        // Inicializamos la colección para evitar null
        $this->productos = new ArrayCollection();
    }

    // =========================
    // Getters y setters básicos
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

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    /**
     * Devuelve la colección de productos asociados
     */
    public function getProductos(): Collection
    {
        return $this->productos;
    }

    /**
     * Añade un producto a la categoría
     * Mantiene la coherencia de la relación bidireccional
     */
    public function addProducto(Producto $producto): static
    {
        if (!$this->productos->contains($producto)) {
            $this->productos->add($producto);

            // Sincroniza el lado inverso de la relación
            $producto->setCategoria($this);
        }

        return $this;
    }

    /**
     * Elimina un producto de la categoría
     */
    public function removeProducto(Producto $producto): static
    {
        if ($this->productos->removeElement($producto)) {

            // Si el producto sigue apuntando a esta categoría,
            // se rompe la relación
            if ($producto->getCategoria() === $this) {
                $producto->setCategoria(null);
            }
        }

        return $this;
    }
}

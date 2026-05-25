<?php

namespace App\Services;

use App\Entity\Producto;
use Doctrine\ORM\EntityManagerInterface;

class ProductoService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Elimina un producto si no tiene pedidos asociados.
     * Devuelve un mensaje de error si no es posible, o null si se eliminó correctamente.
     */
    public function eliminar(Producto $producto): ?string
    {
        if (!$producto->getDetallesPedido()->isEmpty()) {
            return 'No se puede eliminar este producto porque está asociado a uno o más pedidos.';
        }

        $this->entityManager->remove($producto);
        $this->entityManager->flush();

        return null;
    }
}

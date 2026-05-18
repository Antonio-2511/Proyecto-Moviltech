<?php

namespace App\Service;

use App\Entity\Producto;
use App\Repository\DetallePedidoRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class ResenaService
{
    public function __construct(
        private DetallePedidoRepository $detallePedidoRepository
    ) {}

    /**
     * Comprueba si un usuario ha comprado un producto concreto.
     * Solo los compradores pueden dejar reseña.
     */
    public function usuarioHaComprado(Producto $producto, UserInterface $usuario): bool
    {
        $detalles = $this->detallePedidoRepository->findBy(['producto' => $producto]);

        foreach ($detalles as $detalle) {
            if ($detalle->getPedido()->getUsuario() === $usuario) {
                return true;
            }
        }

        return false;
    }
}

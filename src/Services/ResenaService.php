<?php

namespace App\Services;

use App\Entity\Producto;
use App\Entity\Resena;
use App\Repository\DetallePedidoRepository;
use App\Repository\ResenaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ResenaService
{
    public function __construct(
        private DetallePedidoRepository $detallePedidoRepository,
        private EntityManagerInterface  $entityManager,
    ) {}

    /**
     * Comprueba si el usuario ha comprado alguna vez el producto.
     */
    public function usuarioHaComprado(UserInterface $usuario, Producto $producto): bool
    {
        $detalles = $this->detallePedidoRepository->findBy(['producto' => $producto]);

        foreach ($detalles as $detalle) {
            if ($detalle->getPedido()->getUsuario() === $usuario) {
                return true;
            }
        }

        return false;
    }

    /**
     * Guarda una nueva reseña para un producto hecha por un usuario.
     * Lanza una excepción si el usuario no ha comprado el producto.
     */
    public function crearResena(
        UserInterface $usuario,
        Producto      $producto,
        int           $puntuacion,
        ?string       $comentario
    ): Resena {
        if (!$this->usuarioHaComprado($usuario, $producto)) {
            throw new \LogicException('El usuario no ha comprado este producto.');
        }

        $resena = new Resena();
        $resena->setUsuario($usuario);
        $resena->setProducto($producto);
        $resena->setPuntuacion($puntuacion);
        $resena->setComentario($comentario);

        $this->entityManager->persist($resena);
        $this->entityManager->flush();

        return $resena;
    }
}

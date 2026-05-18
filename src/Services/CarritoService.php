<?php

namespace App\Service;

use App\Entity\DetallePedido;
use App\Entity\Pedido;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CarritoService
{
    public function __construct(
        private ProductoRepository $productoRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Devuelve los productos del carrito con cantidad y subtotal,
     * y el total general.
     */
    public function obtenerContenido(array $carrito): array
    {
        $productos = [];
        $total = 0;

        foreach ($carrito as $id => $cantidad) {
            $producto = $this->productoRepository->find($id);

            if ($producto) {
                $subtotal = $producto->getPrecio() * $cantidad;

                $productos[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                ];

                $total += $subtotal;
            }
        }

        return ['productos' => $productos, 'total' => $total];
    }

    /**
     * Añade una unidad de un producto al carrito.
     * Devuelve un mensaje de error si no es posible, o null si todo va bien.
     */
    public function añadir(int $id, array &$carrito): ?string
    {
        $producto = $this->productoRepository->find($id);

        if (!$producto) {
            return 'Producto no encontrado.';
        }

        $cantidadActual = $carrito[$id] ?? 0;

        if ($cantidadActual >= $producto->getStock()) {
            return 'No hay más stock disponible.';
        }

        $carrito[$id] = $cantidadActual + 1;

        return null;
    }

    /**
     * Elimina un producto del carrito.
     */
    public function eliminar(int $id, array &$carrito): void
    {
        unset($carrito[$id]);
    }

    /**
     * Aumenta en 1 la cantidad de un producto en el carrito.
     */
    public function aumentar(int $id, array &$carrito): void
    {
        if (isset($carrito[$id])) {
            $carrito[$id]++;
        }
    }

    /**
     * Reduce en 1 la cantidad de un producto. Si llega a 0, lo elimina.
     */
    public function reducir(int $id, array &$carrito): void
    {
        if (!isset($carrito[$id])) {
            return;
        }

        if ($carrito[$id] > 1) {
            $carrito[$id]--;
        } else {
            unset($carrito[$id]);
        }
    }

    /**
     * Procesa el checkout: crea el Pedido con sus DetallePedido,
     * descuenta stock y persiste todo.
     *
     * Devuelve un mensaje de error si algo falla, o null si todo va bien.
     */
    public function checkout(array $carrito, UserInterface $usuario): ?string
    {
        if (empty($carrito)) {
            return 'El carrito está vacío.';
        }

        $pedido = new Pedido();
        $pedido->setUsuario($usuario);

        $total = 0;

        foreach ($carrito as $id => $cantidad) {
            $producto = $this->productoRepository->find($id);

            if (!$producto) {
                continue;
            }

            if ($producto->getStock() < $cantidad) {
                return 'Stock insuficiente para ' . $producto->getNombre();
            }

            $detalle = new DetallePedido();
            $detalle->setProducto($producto);
            $detalle->setCantidad($cantidad);
            $detalle->setPrecioUnitario($producto->getPrecio());
            $detalle->setPedido($pedido);

            $total += $producto->getPrecio() * $cantidad;

            $producto->setStock($producto->getStock() - $cantidad);

            $this->entityManager->persist($detalle);
        }

        $pedido->setTotal($total);

        $this->entityManager->persist($pedido);
        $this->entityManager->flush();

        return null;
    }
}

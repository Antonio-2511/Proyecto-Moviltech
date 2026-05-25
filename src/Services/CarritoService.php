<?php

namespace App\Services;

use App\Entity\DetallePedido;
use App\Entity\Pedido;
use App\Repository\CuponRepository;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CarritoService
{
    public function __construct(
        private ProductoRepository    $productoRepository,
        private EntityManagerInterface $entityManager,
        private CuponRepository       $cuponRepository,
    ) {}

    // =========================================================
    // LÓGICA DE CONTENIDO DEL CARRITO
    //
    // =========================================================

    /**
     * Devuelve los productos del carrito con cantidad y subtotal,
     * y el total general.
     */
    public function obtenerContenido(array $carrito): array
    {
        $productos = [];
        $total     = 0;

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

    // =========================================================
    // LÓGICA DE MANIPULACIÓN DEL CARRITO
    //
    // =========================================================

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
     * Elimina completamente un producto del carrito.
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
     * Reduce en 1 la cantidad de un producto.
     * Si llega a 0, lo elimina del carrito.
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

    // =========================================================
    // VALIDACIÓN DE CUPÓN
    // =========================================================

    /**
     * Valida un código de cupón y devuelve el total con descuento aplicado.
     *
     * Retorna un array con:
     *   - 'totalFinal'  => float (total con descuento, o el mismo si no hay cupón)
     *   - 'descuento'   => float (importe ahorrado)
     *   - 'porcentaje'  => float|null
     *   - 'error'       => string|null (mensaje si el cupón no es válido)
     */
    public function aplicarCupon(?string $codigoCupon, float $total): array
    {
        if (empty($codigoCupon)) {
            return [
                'totalFinal' => $total,
                'descuento'  => 0,
                'porcentaje' => null,
                'error'      => null,
            ];
        }

        $cupon = $this->cuponRepository->findActivoByCodigo($codigoCupon);

        if (!$cupon) {
            return [
                'totalFinal' => $total,
                'descuento'  => 0,
                'porcentaje' => null,
                'error'      => 'El cupón "' . $codigoCupon . '" no es válido o ha caducado.',
            ];
        }

        $totalFinal = $cupon->aplicarDescuento($total);
        $descuento  = $total - $totalFinal;

        return [
            'totalFinal' => round($totalFinal, 2),
            'descuento'  => round($descuento, 2),
            'porcentaje' => $cupon->getPorcentaje(),
            'error'      => null,
        ];
    }

    // =========================================================
    // CHECKOUT
    //
    // =========================================================

    /**
     * Procesa el checkout: crea el Pedido con sus DetallePedido,
     * aplica el cupón si existe, descuenta stock y persiste todo.
     *
     * Devuelve un mensaje de error si algo falla, o null si todo va bien.
     */
    public function checkout(array $carrito, UserInterface $usuario, ?string $codigoCupon = null): ?string
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

            // Descontar stock
            $producto->setStock($producto->getStock() - $cantidad);

            $this->entityManager->persist($detalle);
        }

        // Aplicar cupón si se proporcionó
        $resultadoCupon = $this->aplicarCupon($codigoCupon, $total);

        // Si el cupón es inválido no bloqueamos el pedido; el controlador
        // ya habrá mostrado el aviso. Usamos el total sin descuento como fallback.
        $pedido->setTotal($resultadoCupon['totalFinal']);

        $this->entityManager->persist($pedido);
        $this->entityManager->flush();

        return null;
    }
}

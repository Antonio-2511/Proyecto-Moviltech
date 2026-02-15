<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Entity\DetallePedido;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carrito')]
class CarritoController extends AbstractController
{
    #[Route('/', name: 'carrito_index')]
    public function index(
        Request $request,
        ProductoRepository $productoRepository
    ): Response {

        // Se obtiene el carrito almacenado en sesión (array id => cantidad)
        $carrito = $request->getSession()->get('carrito', []);

        $productos = [];
        $total = 0;

        // Se recorren los productos guardados en sesión
        foreach ($carrito as $id => $cantidad) {

            $producto = $productoRepository->find($id);

            if ($producto) {

                $subtotal = $producto->getPrecio() * $cantidad;

                // Se prepara estructura para la vista
                $productos[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal
                ];

                $total += $subtotal;
            }
        }

        return $this->render('carrito/index.html.twig', [
            'productos' => $productos,
            'total' => $total
        ]);
    }

    #[Route('/add/{id}', name: 'carrito_add')]
    public function add(int $id, Request $request, ProductoRepository $productoRepository): Response
    {
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        $producto = $productoRepository->find($id);

        // Validación: producto debe existir
        if (!$producto) {
            $this->addFlash('danger', 'Producto no encontrado.');
            return $this->redirectToRoute('app_home');
        }

        $cantidadActual = $carrito[$id] ?? 0;

        // Validación: no superar el stock disponible
        if ($cantidadActual >= $producto->getStock()) {
            $this->addFlash('warning', 'No hay más stock disponible.');
            return $this->redirectToRoute('app_home');
        }

        // Se incrementa cantidad en sesión
        $carrito[$id] = $cantidadActual + 1;
        $session->set('carrito', $carrito);

        $this->addFlash('success', 'Producto añadido al carrito.');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/remove/{id}', name: 'carrito_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $carrito = $session->get('carrito', []);

        // Elimina completamente el producto del carrito
        if (isset($carrito[$id])) {
            unset($carrito[$id]);
        }

        $session->set('carrito', $carrito);

        $this->addFlash('info', 'Producto eliminado del carrito.');

        return $this->redirectToRoute('carrito_index');
    }

    #[Route('/clear', name: 'carrito_clear')]
    public function clear(SessionInterface $session): Response
    {
        // Vacía completamente el carrito
        $session->remove('carrito');

        $this->addFlash('info', 'Carrito vaciado.');

        return $this->redirectToRoute('carrito_index');
    }

    #[Route('/checkout', name: 'carrito_checkout')]
    public function checkout(
        SessionInterface $session,
        ProductoRepository $productoRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $carrito = $session->get('carrito', []);

        // Validación: carrito vacío
        if (empty($carrito)) {
            $this->addFlash('warning', 'El carrito está vacío.');
            return $this->redirectToRoute('carrito_index');
        }

        // Validación: usuario autenticado
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Debes iniciar sesión para finalizar la compra.');
            return $this->redirectToRoute('app_login');
        }

        // Se crea el pedido
        $pedido = new Pedido();
        $pedido->setUsuario($this->getUser());

        $total = 0;

        foreach ($carrito as $id => $cantidad) {

            $producto = $productoRepository->find($id);

            if (!$producto) {
                continue;
            }

            // Validación crítica: comprobar stock antes de confirmar
            if ($producto->getStock() < $cantidad) {
                $this->addFlash('danger', 'Stock insuficiente para ' . $producto->getNombre());
                return $this->redirectToRoute('carrito_index');
            }

            // Crear detalle de pedido
            $detalle = new DetallePedido();
            $detalle->setProducto($producto);
            $detalle->setCantidad($cantidad);
            $detalle->setPrecioUnitario($producto->getPrecio());
            $detalle->setPedido($pedido);

            $subtotal = $producto->getPrecio() * $cantidad;
            $total += $subtotal;

            // Descontar stock
            $producto->setStock($producto->getStock() - $cantidad);

            $entityManager->persist($detalle);
        }

        $pedido->setTotal($total);

        $entityManager->persist($pedido);
        $entityManager->flush(); // Se guardan pedido, detalles y stock actualizado

        $session->remove('carrito');

        $this->addFlash('success', 'Pedido realizado correctamente.');

        return $this->redirectToRoute('pedido_index');
    }

    #[Route('/increase/{id}', name: 'carrito_increase')]
    public function increase(int $id, SessionInterface $session): Response
    {
        $carrito = $session->get('carrito', []);

        if (isset($carrito[$id])) {
            $carrito[$id]++;
        }

        $session->set('carrito', $carrito);

        return $this->redirectToRoute('carrito_index');
    }

    #[Route('/decrease/{id}', name: 'carrito_decrease')]
    public function decrease(int $id, SessionInterface $session): Response
    {
        $carrito = $session->get('carrito', []);

        if (isset($carrito[$id])) {

            // Si cantidad > 1 se reduce, si no se elimina
            if ($carrito[$id] > 1) {
                $carrito[$id]--;
            } else {
                unset($carrito[$id]);
            }
        }

        $session->set('carrito', $carrito);

        return $this->redirectToRoute('carrito_index');
    }
}

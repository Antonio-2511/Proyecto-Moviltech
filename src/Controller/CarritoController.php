<?php

namespace App\Controller;

use App\Services\CarritoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carrito')]
class CarritoController extends AbstractController
{
    public function __construct(
        private CarritoService $carritoService
    ) {}

    // =========================================================
    // VER CARRITO
    // Ahora delega el cálculo al servicio
    // =========================================================

    #[Route('/', name: 'carrito_index')]
    public function index(Request $request): Response
    {
        $carrito    = $request->getSession()->get('carrito', []);
        $contenido  = $this->carritoService->obtenerContenido($carrito);

        return $this->render('carrito/index.html.twig', [
            'productos' => $contenido['productos'],
            'total'     => $contenido['total'],
        ]);
    }

    // =========================================================
    // AÑADIR AL CARRITO
    // =========================================================

    #[Route('/add/{id}', name: 'carrito_add')]
    public function add(int $id, Request $request): Response
    {
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        $error = $this->carritoService->añadir($id, $carrito);

        if ($error) {
            $this->addFlash('warning', $error);
        } else {
            $session->set('carrito', $carrito);
            $this->addFlash('success', 'Producto añadido al carrito.');
        }

        return $this->redirectToRoute('app_home');
    }

    // =========================================================
    // ELIMINAR DEL CARRITO
    // =========================================================

    #[Route('/remove/{id}', name: 'carrito_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $carrito = $session->get('carrito', []);
        $this->carritoService->eliminar($id, $carrito);
        $session->set('carrito', $carrito);

        $this->addFlash('info', 'Producto eliminado del carrito.');

        return $this->redirectToRoute('carrito_index');
    }

    // =========================================================
    // VACIAR CARRITO
    // =========================================================

    #[Route('/clear', name: 'carrito_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('carrito');

        $this->addFlash('info', 'Carrito vaciado.');

        return $this->redirectToRoute('carrito_index');
    }

    // =========================================================
    // AUMENTAR / REDUCIR CANTIDAD
    // =========================================================

    #[Route('/increase/{id}', name: 'carrito_increase')]
    public function increase(int $id, SessionInterface $session): Response
    {
        $carrito = $session->get('carrito', []);
        $this->carritoService->aumentar($id, $carrito);
        $session->set('carrito', $carrito);

        return $this->redirectToRoute('carrito_index');
    }

    #[Route('/decrease/{id}', name: 'carrito_decrease')]
    public function decrease(int $id, SessionInterface $session): Response
    {
        $carrito = $session->get('carrito', []);
        $this->carritoService->reducir($id, $carrito);
        $session->set('carrito', $carrito);

        return $this->redirectToRoute('carrito_index');
    }

    // =========================================================
    // CHECKOUT CON CUPÓN
    // =========================================================

    #[Route('/checkout', name: 'carrito_checkout', methods: ['GET', 'POST'])]
    public function checkout(
        Request          $request,
        SessionInterface $session
    ): Response {
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('warning', 'El carrito está vacío.');
            return $this->redirectToRoute('carrito_index');
        }

        if (!$this->getUser()) {
            $this->addFlash('warning', 'Debes iniciar sesión para finalizar la compra.');
            return $this->redirectToRoute('app_login');
        }

        // Recoger el cupón del formulario (campo opcional)
        $codigoCupon = trim((string) $request->request->get('cupon', ''));
        $codigoCupon = $codigoCupon !== '' ? $codigoCupon : null;

        // Validar cupón de forma previa para mostrar aviso sin procesar el pedido
        if ($codigoCupon !== null) {
            $contenido       = $this->carritoService->obtenerContenido($carrito);
            $resultadoCupon  = $this->carritoService->aplicarCupon($codigoCupon, $contenido['total']);

            if ($resultadoCupon['error']) {
                $this->addFlash('danger', $resultadoCupon['error']);
                return $this->redirectToRoute('carrito_index');
            }

            // Informar del descuento aplicado
            $this->addFlash(
                'info',
                sprintf(
                    'Cupón "%s" aplicado: %.0f%% de descuento (–%.2f €).',
                    strtoupper($codigoCupon),
                    $resultadoCupon['porcentaje'],
                    $resultadoCupon['descuento']
                )
            );
        }

        // Delegar el proceso de compra completo al servicio
        $error = $this->carritoService->checkout($carrito, $this->getUser(), $codigoCupon);

        if ($error) {
            $this->addFlash('danger', $error);
            return $this->redirectToRoute('carrito_index');
        }

        $session->remove('carrito');

        $this->addFlash('success', 'Pedido realizado correctamente.');

        return $this->redirectToRoute('pedido_index');
    }
}

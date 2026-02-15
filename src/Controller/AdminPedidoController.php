<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pedido;
use App\Repository\PedidoRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/pedido')]
#[IsGranted('ROLE_ADMIN')] // Solo usuarios con rol ADMIN pueden acceder a este controlador
class AdminPedidoController extends AbstractController
{
    #[Route('/', name: 'admin_pedido_index')]
    public function index(PedidoRepository $pedidoRepository): Response
    {
        // Obtiene todos los pedidos ordenados por fecha descendente (más recientes primero)
        $pedidos = $pedidoRepository->findBy([], ['fecha' => 'DESC']);

        return $this->render('admin_pedido/index.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/{id}/estado', name: 'admin_pedido_estado', methods: ['POST'])]
    public function cambiarEstado(
        Pedido $pedido,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        // Se obtiene el nuevo estado enviado desde el formulario
        $nuevoEstado = $request->request->get('estado');

        // Lista blanca de estados permitidos
        $estadosValidos = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];

        // Se valida que el estado recibido sea válido antes de actualizar
        if (in_array($nuevoEstado, $estadosValidos)) {
            $pedido->setEstado($nuevoEstado);
            $em->flush(); // Se guarda el cambio en base de datos

            $this->addFlash('success', 'Estado actualizado correctamente.');
        }

        return $this->redirectToRoute('admin_pedido_index');
    }
}

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
#[IsGranted('ROLE_ADMIN')]
class AdminPedidoController extends AbstractController
{
    #[Route('/', name: 'admin_pedido_index')]
    public function index(PedidoRepository $pedidoRepository): Response
    {
        $pedidos = $pedidoRepository->findBy([], ['fecha' => 'DESC']);

        return $this->render('admin_pedido/index.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/{id}', name: 'admin_pedido_show', methods: ['GET'])]
    public function show(Pedido $pedido): Response
    {
        return $this->render('admin_pedido/show.html.twig', [
            'pedido' => $pedido,
        ]);
    }

    #[Route('/{id}/estado', name: 'admin_pedido_estado', methods: ['POST'])]
    public function cambiarEstado(
        Pedido $pedido,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $nuevoEstado   = $request->request->get('estado');
        $estadosValidos = ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'];

        if (in_array($nuevoEstado, $estadosValidos)) {
            $pedido->setEstado($nuevoEstado);
            $em->flush();
            $this->addFlash('success', 'Estado actualizado correctamente.');
        }

        // Si viene del show, volvemos al show; si no, al listado
        $referer = $request->headers->get('referer', '');
        if (str_contains($referer, '/admin/pedido/' . $pedido->getId())) {
            return $this->redirectToRoute('admin_pedido_show', ['id' => $pedido->getId()]);
        }

        return $this->redirectToRoute('admin_pedido_index');
    }
}

<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Repository\PedidoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mis-pedidos')] 
class PedidoController extends AbstractController
{
    #[Route('/', name: 'pedido_index')]
    public function index(PedidoRepository $pedidoRepository): Response
    {
        // Solo usuarios autenticados pueden acceder
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Se obtienen únicamente los pedidos del usuario logueado
        $pedidos = $pedidoRepository->findBy(
            ['usuario' => $this->getUser()],
            ['fecha' => 'DESC'] // Más recientes primero
        );

        return $this->render('pedido/index.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/{id}', name: 'pedido_show')]
    public function show(Pedido $pedido): Response
    {
        // Si no es admin, se aplican restricciones
        if (!$this->isGranted('ROLE_ADMIN')) {

            // Debe ser usuario autenticado
            $this->denyAccessUnlessGranted('ROLE_USER');

            // Solo puede ver su propio pedido
            if ($pedido->getUsuario() !== $this->getUser()) {
                throw $this->createAccessDeniedException();
            }
        }

        return $this->render('pedido/show.html.twig', [
            'pedido' => $pedido,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Resena;
use App\Form\ResenaType;
use App\Repository\DetallePedidoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/resena')]
class ResenaController extends AbstractController
{
    #[Route('/resena/producto/{id}', name: 'resena_new', methods: ['POST'])]
    public function new(
        Producto $producto,
        Request $request,
        EntityManagerInterface $entityManager,
        DetallePedidoRepository $detallePedidoRepository  // NUEVO
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // NUEVO: comprobar si el usuario ha comprado este producto
        $haComprado = $detallePedidoRepository->findOneBy([
            'producto' => $producto,
        ]);

        // Buscar si alguno de esos detalles pertenece a un pedido del usuario actual
        $detalles = $detallePedidoRepository->findBy(['producto' => $producto]);
        $usuarioHaComprado = false;
        foreach ($detalles as $detalle) {
            if ($detalle->getPedido()->getUsuario() === $this->getUser()) {
                $usuarioHaComprado = true;
                break;
            }
        }

        if (!$usuarioHaComprado) {
            $this->addFlash('danger', 'Solo puedes reseñar productos que hayas comprado.');
            return $this->redirectToRoute('catalogo_producto', ['id' => $producto->getId()]);
        }

        $resena = new Resena();
        $form = $this->createForm(ResenaType::class, $resena);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resena->setUsuario($this->getUser());
            $resena->setProducto($producto);

            $entityManager->persist($resena);
            $entityManager->flush();

            $this->addFlash('success', 'Reseña añadida correctamente.');
        }

        return $this->redirectToRoute('catalogo_producto', [
            'id' => $producto->getId()
        ]);
    }
}

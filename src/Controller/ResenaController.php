<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ResenaType;
use App\Services\ResenaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/resena')]
class ResenaController extends AbstractController
{
    public function __construct(
        private ResenaService $resenaService
    ) {}

    #[Route('/producto/{id}', name: 'resena_new', methods: ['POST'])]
    public function new(
        Producto $producto,
        Request  $request,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Toda la lógica de validación y persistencia está en el servicio
        $resena = new \App\Entity\Resena();
        $form   = $this->createForm(ResenaType::class, $resena);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->resenaService->crearResena(
                    $this->getUser(),
                    $producto,
                    $resena->getPuntuacion(),
                    $resena->getComentario()
                );

                $this->addFlash('success', 'Reseña añadida correctamente.');
            } catch (\LogicException) {
                $this->addFlash('danger', 'Solo puedes reseñar productos que hayas comprado.');
            }
        }

        return $this->redirectToRoute('catalogo_producto', [
            'id' => $producto->getId(),
        ]);
    }
}

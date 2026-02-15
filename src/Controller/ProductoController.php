<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ProductoType;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/producto')]
#[IsGranted('ROLE_ADMIN')] // Solo administradores pueden acceder a este controlador
final class ProductoController extends AbstractController
{
    #[Route(name: 'app_producto_index', methods: ['GET'])]
    public function index(ProductoRepository $productoRepository): Response
    {
        // Lista todos los productos ordenados por nombre
        return $this->render('producto/index.html.twig', [
            'productos' => $productoRepository->findAllOrderedByName(),
        ]);
    }

    #[Route('/new', name: 'app_producto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Se crea una nueva instancia vacía
        $producto = new Producto();

        // Se construye el formulario asociado a la entidad
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        // Si el formulario es válido, se guarda en base de datos
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($producto);
            $entityManager->flush();

            $this->addFlash('success', 'Producto creado correctamente.');

            return $this->redirectToRoute('app_producto_index');
        }

        return $this->render('producto/new.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_producto_show', methods: ['GET'])]
    public function show(Producto $producto): Response
    {
        // ParamConverter: Symfony obtiene automáticamente el producto por su ID
        return $this->render('producto/show.html.twig', [
            'producto' => $producto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_producto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Producto $producto, EntityManagerInterface $entityManager): Response
    {
        // Se reutiliza el mismo formulario para edición
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        // En edición solo se hace flush, ya está gestionado por Doctrine
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Producto actualizado correctamente.');

            return $this->redirectToRoute('app_producto_index');
        }

        return $this->render('producto/edit.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_producto_delete', methods: ['POST'])]
    public function delete(Request $request, Producto $producto, EntityManagerInterface $entityManager): Response
    {
        // Protección CSRF para evitar eliminación maliciosa
        if ($this->isCsrfTokenValid('delete' . $producto->getId(), $request->request->get('_token'))) {

            $entityManager->remove($producto);
            $entityManager->flush();

            $this->addFlash('success', 'Producto eliminado correctamente.');

        } else {
            $this->addFlash('danger', 'Error de seguridad al intentar eliminar el producto.');
        }

        return $this->redirectToRoute('app_producto_index');
    }
}

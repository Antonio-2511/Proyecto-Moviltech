<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categoria')]
#[IsGranted('ROLE_ADMIN')] // Solo usuarios con rol ADMIN pueden acceder
final class CategoriaController extends AbstractController
{
    #[Route('/', name: 'app_categoria_index', methods: ['GET'])]
    public function index(CategoriaRepository $categoriaRepository): Response
    {
        // Listado de categorías ordenadas por nombre
        return $this->render('categoria/index.html.twig', [
            'categorias' => $categoriaRepository->findAllOrderedByName(),
        ]);
    }

    #[Route('/new', name: 'app_categoria_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Se crea una nueva entidad vacía
        $categoria = new Categoria();

        // Se genera el formulario asociado a la entidad
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        // Si el formulario es enviado y válido, se guarda en BD
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoria);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría creada correctamente.');

            return $this->redirectToRoute('app_categoria_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categoria/new.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_categoria_show', methods: ['GET'])]
    public function show(Categoria $categoria): Response
    {
        // ParamConverter obtiene automáticamente la categoría por ID
        return $this->render('categoria/show.html.twig', [
            'categoria' => $categoria,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categoria_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categoria $categoria, EntityManagerInterface $entityManager): Response
    {
        // Se reutiliza el mismo formulario para editar
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        // Al ser entidad gestionada por Doctrine, solo hace falta flush()
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Categoría actualizada correctamente.');

            return $this->redirectToRoute('app_categoria_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categoria/edit.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_categoria_delete', methods: ['POST'])]
    public function delete(Request $request, Categoria $categoria, EntityManagerInterface $entityManager): Response
    {
        // Protección CSRF para evitar eliminación no autorizada
        if ($this->isCsrfTokenValid('delete' . $categoria->getId(), $request->request->get('_token'))) {

            $entityManager->remove($categoria);
            $entityManager->flush();

            $this->addFlash('danger', 'Categoría eliminada.');
        }

        return $this->redirectToRoute('app_categoria_index', [], Response::HTTP_SEE_OTHER);
    }
}

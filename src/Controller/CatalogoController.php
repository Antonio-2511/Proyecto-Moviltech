<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Resena;
use App\Form\ResenaType;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalogo')]
class CatalogoController extends AbstractController
{
    #[Route('/', name: 'catalogo_index')]
    public function index(
        ProductoRepository $productoRepository,
        CategoriaRepository $categoriaRepository
    ): Response {
        return $this->render('catalogo/index.html.twig', [
            'productos' => $productoRepository->findBy([], ['nombre' => 'ASC']),
            'categorias' => $categoriaRepository->findAll(),
        ]);
    }

    #[Route('/producto/{id}', name: 'catalogo_producto', methods: ['GET'])]
    public function show(
        Producto $producto,
        EntityManagerInterface $entityManager
    ): Response {

        // Se crea una nueva reseña vacía para el formulario
        $resena = new Resena();

        // Se genera el formulario basado en ResenaType
        $form = $this->createForm(ResenaType::class, $resena);

        return $this->render('catalogo/show.html.twig', [
            'producto' => $producto,
            // Se pasa la vista del formulario al Twig
            'form' => $form->createView(),
        ]);
    }
}

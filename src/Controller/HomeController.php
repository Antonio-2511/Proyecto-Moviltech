<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        ProductoRepository $productoRepository,
        CategoriaRepository $categoriaRepository
    ): Response {

        // Se obtiene el parámetro "categoria" de la URL (?categoria=ID)
        $categoriaId = $request->query->get('categoria');

        // Si hay filtro por categoría, se buscan solo los productos de esa categoría
        if ($categoriaId) {
            $productos = $productoRepository->findBy(
                ['categoria' => $categoriaId],
                ['nombre' => 'ASC'] // Orden alfabético
            );
        } else {
            // Si no hay filtro, se muestran todos los productos
            $productos = $productoRepository->findBy([], ['nombre' => 'ASC']);
        }

        // Se envían productos, categorías y categoría seleccionada a la vista
        return $this->render('home/index.html.twig', [
            'productos' => $productos,
            'categorias' => $categoriaRepository->findAll(),
            'categoriaSeleccionada' => $categoriaId,
        ]);
    }
}

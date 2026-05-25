<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ProductoRepository  $productoRepository,
        CategoriaRepository $categoriaRepository
    ): Response {
        return $this->render('home/index.html.twig', [
            'productos'  => $productoRepository->findBy([], ['nombre' => 'ASC']),
            'categorias' => $categoriaRepository->findAll(),
        ]);
    }

    #[Route('/filtrar', name: 'catalogo_filtrar', methods: ['GET'])]
    public function filtrar(Request $request, ProductoRepository $productoRepository): JsonResponse
    {
        $categoriaId = $request->query->get('categoria') ? (int)   $request->query->get('categoria') : null;
        $precioMax   = $request->query->get('precioMax')  ? (float) $request->query->get('precioMax')  : null;
        $marca       = $request->query->get('marca')      ? trim($request->query->get('marca'))         : null;
        $busqueda    = $request->query->get('busqueda')   ? trim($request->query->get('busqueda'))      : null;

        $productos = $productoRepository->findByFiltros($categoriaId, $precioMax, $marca, $busqueda);

        $data = array_map(static function (Producto $p) {
            return [
                'id'          => $p->getId(),
                'nombre'      => $p->getNombre(),
                'descripcion' => mb_substr($p->getDescripcion(), 0, 80) . '...',
                'precio'      => $p->getPrecio(),
                'stock'       => $p->getStock(),
                'imagen'      => $p->getImagen(),
            ];
        }, $productos);

        return new JsonResponse($data);
    }
}

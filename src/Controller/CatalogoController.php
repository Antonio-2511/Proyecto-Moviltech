<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ResenaType;
use App\Repository\CategoriaRepository;
use App\Repository\ProductoRepository;
use App\Services\ResenaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogoController extends AbstractController
{
    public function __construct(
        private ResenaService $resenaService
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(
        ProductoRepository  $productoRepository,
        CategoriaRepository $categoriaRepository
    ): Response {
        return $this->render('catalogo/index.html.twig', [
            'productos'  => $productoRepository->findAllOrderedByName(),
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

    #[Route('/catalogo/producto/{id}', name: 'catalogo_producto')]
    public function show(Producto $producto): Response
    {
        $resena     = new \App\Entity\Resena();
        $form       = $this->createForm(ResenaType::class, $resena);
        $haComprado = false;

        if ($this->getUser()) {
            $haComprado = $this->resenaService->usuarioHaComprado($this->getUser(), $producto);
        }

        return $this->render('catalogo/show.html.twig', [
            'producto'   => $producto,
            'form'       => $form,
            'haComprado' => $haComprado,
        ]);
    }
}

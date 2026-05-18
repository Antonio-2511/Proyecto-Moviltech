<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Resena;
use App\Form\ResenaType;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use App\Repository\DetallePedidoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;  // NUEVO
use Symfony\Component\HttpFoundation\Request;        // NUEVO
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

    // NUEVO: ruta AJAX para filtrar productos
    #[Route('/filtrar', name: 'catalogo_filtrar', methods: ['GET'])]
    public function filtrar(
        Request $request,
        ProductoRepository $productoRepository
    ): JsonResponse {
        $categoriaId = $request->query->get('categoria');
        $precioMax   = $request->query->get('precioMax');
        $marca       = $request->query->get('marca');

        $productos = $productoRepository->findByFiltros(
            $categoriaId ? (int) $categoriaId : null,
            $precioMax   ? (float) $precioMax : null,
            $marca       ?: null
        );

        // Convertir entidades a array para devolver como JSON
        $data = array_map(fn($p) => [
            'id'          => $p->getId(),
            'nombre'      => $p->getNombre(),
            'descripcion' => mb_substr($p->getDescripcion(), 0, 80) . '...',
            'precio'      => $p->getPrecio(),
            'imagen'      => $p->getImagen(),
            'stock'       => $p->getStock(),
        ], $productos);

        return new JsonResponse($data);
    }

    #[Route('/producto/{id}', name: 'catalogo_producto', methods: ['GET'])]
    public function show(
        Producto $producto,
        EntityManagerInterface $entityManager,
        DetallePedidoRepository $detallePedidoRepository
    ): Response {
        $resena = new Resena();
        $form = $this->createForm(ResenaType::class, $resena);

        $haComprado = false;
        if ($this->getUser()) {
            $detalles = $detallePedidoRepository->findBy(['producto' => $producto]);
            foreach ($detalles as $detalle) {
                if ($detalle->getPedido()->getUsuario() === $this->getUser()) {
                    $haComprado = true;
                    break;
                }
            }
        }

        return $this->render('catalogo/show.html.twig', [
            'producto'    => $producto,
            'form'        => $form->createView(),
            'haComprado'  => $haComprado,
        ]);
    }
}

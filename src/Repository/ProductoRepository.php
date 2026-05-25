<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByFiltros(
        ?int    $categoriaId,
        ?float  $precioMax,
        ?string $marca,
        ?string $busqueda
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.nombre', 'ASC');

        if ($busqueda) {
            $qb->andWhere('p.nombre LIKE :busqueda')
                ->setParameter('busqueda', '%' . $busqueda . '%');
        }

        if ($categoriaId) {
            $qb->andWhere('p.categoria = :categoria')
                ->setParameter('categoria', $categoriaId);
        }

        if ($precioMax) {
            $qb->andWhere('p.precio <= :precioMax')
                ->setParameter('precioMax', $precioMax);
        }

        if ($marca) {
            $qb->andWhere('p.marca LIKE :marca')
                ->setParameter('marca', '%' . $marca . '%');
        }

        return $qb->getQuery()->getResult();
    }
}

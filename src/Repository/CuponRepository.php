<?php

namespace App\Repository;

use App\Entity\Cupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CuponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cupon::class);
    }

    /**
     * Busca un cupón activo por su código (insensible a mayúsculas).
     */
    public function findActivoByCodigo(string $codigo): ?Cupon
    {
        return $this->createQueryBuilder('c')
            ->where('UPPER(c.codigo) = :codigo')
            ->andWhere('c.activo = true')
            ->setParameter('codigo', strtoupper(trim($codigo)))
            ->getQuery()
            ->getOneOrNullResult();
    }
}

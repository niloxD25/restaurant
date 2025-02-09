<?php

namespace App\Repository;

use App\Entity\IngredientPlat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IngredientPlat>
 */
class IngredientPlatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IngredientPlat::class);
    }

    /**
     * Trouver tous les ingrédients d'un plat donné
     */
    public function findByPlatId(int $platId): array
    {
        return $this->createQueryBuilder('ip')
            ->andWhere('ip.plat = :platId')
            ->setParameter('platId', $platId)
            ->getQuery()
            ->getResult();
    }
}

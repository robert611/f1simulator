<?php

namespace Computer\Repository;

use Computer\Entity\RaceResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RaceResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaceResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaceResult[]    findAll()
 * @method RaceResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaceResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaceResult::class);
    }
}

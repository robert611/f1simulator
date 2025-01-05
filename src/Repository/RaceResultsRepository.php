<?php

namespace App\Repository;

use App\Entity\RaceResults;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RaceResults|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaceResults|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaceResults[]    findAll()
 * @method RaceResults[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaceResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaceResults::class);
    }
}

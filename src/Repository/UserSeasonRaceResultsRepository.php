<?php

namespace App\Repository;

use App\Entity\UserSeasonRaceResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSeasonRaceResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonRaceResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonRaceResult[]    findAll()
 * @method UserSeasonRaceResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonRaceResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonRaceResult::class);
    }
}

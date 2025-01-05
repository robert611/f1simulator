<?php

namespace App\Repository;

use App\Entity\UserSeasonRaces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSeasonRaces|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonRaces|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonRaces[]    findAll()
 * @method UserSeasonRaces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonRacesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonRaces::class);
    }
}

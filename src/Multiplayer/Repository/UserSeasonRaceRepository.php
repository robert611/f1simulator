<?php

namespace Multiplayer\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Multiplayer\Entity\UserSeasonRace;

/**
 * @method UserSeasonRace|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonRace|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonRace[]    findAll()
 * @method UserSeasonRace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonRaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonRace::class);
    }
}

<?php

namespace App\Repository;

use App\Entity\UserSeason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSeason|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeason|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeason[]    findAll()
 * @method UserSeason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeason::class);
    }
}

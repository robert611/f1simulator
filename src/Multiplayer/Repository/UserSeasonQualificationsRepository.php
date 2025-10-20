<?php

namespace Multiplayer\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Multiplayer\Entity\UserSeasonQualification;

/**
 * @method UserSeasonQualification|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonQualification|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonQualification[]    findAll()
 * @method UserSeasonQualification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonQualificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonQualification::class);
    }
}

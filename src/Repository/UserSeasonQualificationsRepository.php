<?php

namespace App\Repository;

use App\Entity\UserSeasonQualifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSeasonQualifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonQualifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonQualifications[]    findAll()
 * @method UserSeasonQualifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonQualificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonQualifications::class);
    }
}

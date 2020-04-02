<?php

namespace App\Repository;

use App\Entity\UserSeasonQualifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    // /**
    //  * @return UserSeasonQualifications[] Returns an array of UserSeasonQualifications objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserSeasonQualifications
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

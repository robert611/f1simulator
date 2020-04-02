<?php

namespace App\Repository;

use App\Entity\UserSeasonRaceResults;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserSeasonRaceResults|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonRaceResults|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonRaceResults[]    findAll()
 * @method UserSeasonRaceResults[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonRaceResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonRaceResults::class);
    }

    // /**
    //  * @return UserSeasonRaceResults[] Returns an array of UserSeasonRaceResults objects
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
    public function findOneBySomeField($value): ?UserSeasonRaceResults
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

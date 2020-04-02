<?php

namespace App\Repository;

use App\Entity\UserSeasonPlayers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserSeasonPlayers|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonPlayers|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonPlayers[]    findAll()
 * @method UserSeasonPlayers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonPlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonPlayers::class);
    }

    // /**
    //  * @return UserSeasonPlayers[] Returns an array of UserSeasonPlayers objects
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
    public function findOneBySomeField($value): ?UserSeasonPlayers
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

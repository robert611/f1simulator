<?php

namespace Multiplayer\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;

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

    /**
     * @return UserSeasonPlayer[]
     */
    public function getUserSeasons(int $userId): array
    {
        return $this->createQueryBuilder('us')
            ->select('us')
            ->leftJoin('us.players', 'p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('us.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

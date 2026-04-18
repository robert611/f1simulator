<?php

namespace Multiplayer\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Shared\Clock\Clock;

/**
 * @method UserSeason|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeason|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeason[]    findAll()
 * @method UserSeason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Clock $clock,
    ) {
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

    /**
     * @return array<array{month: int, year: int, seasonsPlayed: int}>
     */
    public function getLast12MonthsSeasonsPlayed(): array
    {
        return $this->createQueryBuilder('us')
            ->select('MONTH(us.completedAt) as month, YEAR(us.completedAt) as year, COUNT(us.id) as seasonsPlayed')
            ->where('us.completedAt IS NOT NULL')
            ->andWhere('us.completedAt >= :fromDate')
            ->setParameter('fromDate', $this->clock->now('-12 months first day of next month 00:00:00'))
            ->groupBy('month', 'year')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

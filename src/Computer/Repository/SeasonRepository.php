<?php

namespace Computer\Repository;

use Computer\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Shared\Clock\Clock;

/**
 * @method Season|null find($id, $lockMode = null, $lockVersion = null)
 * @method Season|null findOneBy(array $criteria, array $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Clock $clock,
    ) {
        parent::__construct($registry, Season::class);
    }

    /**
     * @return array<array{month: int, year: int, seasonsPlayed: int}>
     */
    public function getLast12MonthsSeasonPlayed(): array
    {
        return $this->createQueryBuilder('s')
            ->select('MONTH(s.completedAt) as month, YEAR(s.completedAt) as year, COUNT(s.id) as seasonsPlayed')
            ->where('s.completedAt IS NOT NULL')
            ->andWhere('s.completedAt >= :fromDate')
            ->setParameter('fromDate', $this->clock->now('-12 months first day of next month 00:00:00'))
            ->groupBy('month', 'year')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Entity\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Driver|null find($id, $lockMode = null, $lockVersion = null)
 * @method Driver|null findOneBy(array $criteria, array $orderBy = null)
 * @method Driver[]    findAll()
 * @method Driver[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Driver::class);
    }

    /**
     * @param int[] $driversIds
     */
    public function getDriversWithTeams(array $driversIds): array
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->addSelect('t')
            ->leftJoin('d.team', 't')
            ->where('d.id IN (:driversIds)')
            ->setParameter('driversIds', $driversIds)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Driver[]
     */
    public function findAllWithTeams(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->addSelect('t')
            ->leftJoin('d.team', 't')
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

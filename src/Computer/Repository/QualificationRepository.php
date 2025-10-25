<?php

declare(strict_types=1);

namespace Computer\Repository;

use Computer\Entity\Qualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Qualification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Qualification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Qualification[]    findAll()
 * @method Qualification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QualificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Qualification::class);
    }

    /**
     * @return Qualification[]
     */
    public function getSortedRaceQualifications(int $raceId): array
    {
        return $this->createQueryBuilder('q')
            ->select('q')
            ->andWhere('q.race = :raceId')
            ->setParameter('raceId', $raceId)
            ->orderBy('q.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

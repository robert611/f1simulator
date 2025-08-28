<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    public function getFirstTrack(): ?Track
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getNextTrack(int $previousTrackId): ?Track
    {
        $result = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.id > :previousTrackId')
            ->setParameter('previousTrackId', $previousTrackId)
            ->setMaxResults(1)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $result[0] ?? null;
    }
}

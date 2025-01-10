<?php

namespace App\Repository;

use App\Entity\UserSeasonPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSeasonPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSeasonPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSeasonPlayer[]    findAll()
 * @method UserSeasonPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSeasonPlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSeasonPlayer::class);
    }
}

<?php

namespace App\Repository;

use App\Entity\UserSeasonPlayers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}

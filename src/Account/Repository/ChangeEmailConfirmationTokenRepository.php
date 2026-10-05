<?php

declare(strict_types=1);

namespace Account\Repository;

use Account\Entity\ChangeEmailConfirmationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChangeEmailConfirmationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChangeEmailConfirmationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChangeEmailConfirmationToken[] findAll()
 * @method ChangeEmailConfirmationToken[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChangeEmailConfirmationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChangeEmailConfirmationToken::class);
    }
}

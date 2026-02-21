<?php

declare(strict_types=1);

namespace Security\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Security\Entity\UserConfirmation;

/**
 * @method UserConfirmation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserConfirmation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserConfirmation[]    findAll()
 * @method UserConfirmation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserConfirmationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserConfirmation::class);
    }
}

<?php

declare(strict_types=1);

namespace Security\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Security\Entity\UserConfirmationToken;

/**
 * @method UserConfirmationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserConfirmationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserConfirmationToken[]    findAll()
 * @method UserConfirmationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserConfirmationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserConfirmationToken::class);
    }

    public function invalidateUserTokens(int $userId): void
    {
        $this->createQueryBuilder('t')
            ->update()
            ->set('t.isValid', 'false')
            ->where('t.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }

    public function findLastTokenForUser(int $userId): ?UserConfirmationToken
    {
        $result = $this->createQueryBuilder('t')
            ->where('t.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ?? null;
    }
}

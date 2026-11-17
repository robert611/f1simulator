<?php

declare(strict_types=1);

namespace OutgoingApi\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OutgoingApi\Entity\ApiToken;
use Security\Entity\User;
use SensitiveParameter;

/**
 * @method ApiToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiToken[] findAll()
 * @method ApiToken[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    public function findOneByPlainToken(#[SensitiveParameter] string $plainToken): ?ApiToken
    {
        return $this->findOneBy(['tokenHash' => ApiToken::hash($plainToken)]);
    }

    public function findOneByUser(User $user): ?ApiToken
    {
        return $this->findOneBy(['user' => $user]);
    }
}

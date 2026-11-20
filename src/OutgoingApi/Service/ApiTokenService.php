<?php

declare(strict_types=1);

namespace OutgoingApi\Service;

use Doctrine\ORM\EntityManagerInterface;
use OutgoingApi\Entity\ApiToken;
use OutgoingApi\Repository\ApiTokenRepository;
use Security\Entity\User;
use Shared\Service\TokenGenerator;

final readonly class ApiTokenService
{
    public function __construct(
        private ApiTokenRepository $apiTokenRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getUserToken(User $user): ?ApiToken
    {
        return $this->apiTokenRepository->findOneByUser($user);
    }

    /**
     * Creates a token for the user, or replaces the existing one (the old token stops working immediately).
     *
     * @return string The plain token. Only its hash is stored, so this is the only chance to show it to the user.
     */
    public function generate(User $user): string
    {
        $plainToken = TokenGenerator::bin2hex(32);
        $apiToken = $this->apiTokenRepository->findOneByUser($user);

        if (null === $apiToken) {
            $this->entityManager->persist(ApiToken::create($user, $plainToken));
        } else {
            $apiToken->regenerate($plainToken);
        }

        $this->entityManager->flush();

        return $plainToken;
    }
}

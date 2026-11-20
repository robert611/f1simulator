<?php

declare(strict_types=1);

namespace OutgoingApi\Service;

use OutgoingApi\Repository\ApiTokenRepository;
use SensitiveParameter;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final readonly class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private ApiTokenRepository $apiTokenRepository,
    ) {
    }

    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        $apiToken = $this->apiTokenRepository->findOneByPlainToken($accessToken);

        if (null === $apiToken) {
            throw new BadCredentialsException('Invalid API token.');
        }

        $user = $apiToken->getUser();

        return new UserBadge($user->getUserIdentifier(), static fn () => $user);
    }
}

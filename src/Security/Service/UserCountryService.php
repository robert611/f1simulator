<?php

declare(strict_types=1);

namespace Security\Service;

use Security\Contract\UserCountryServiceInterface;
use Security\Repository\UserRepository;

final readonly class UserCountryService implements UserCountryServiceInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @return array<string, array{country: string, users: int, percentageOfAllUsers: float}>
     */
    public function getUserCountryMapData(): array
    {
        $userMapData = $this->userRepository->getUserCountryMapData();
        $usersCount = $this->userRepository->count();

        $result = [];

        foreach ($userMapData as $userData) {
            $result[$userData['country']->value] = [
                'country' => $userData['country']->value,
                'users' => $userData['users'],
                'percentageOfAllUsers' => round(($userData['users'] / $usersCount) * 100, 2),
            ];
        }

        return $result;
    }
}

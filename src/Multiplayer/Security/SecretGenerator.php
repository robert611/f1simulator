<?php

declare(strict_types=1);

namespace Multiplayer\Security;

use Multiplayer\Repository\UserSeasonRepository;

class SecretGenerator
{
    public function __construct(
        private readonly UserSeasonRepository $userSeasonRepository,
    ) {
    }

    public function getLeagueUniqueSecret(): string
    {
        do {
            $secret = self::getSecret();
        } while ($this->userSeasonRepository->findOneBy(['secret' => $secret]));

        return $secret;
    }

    public static function getSecret(): string
    {
        $alphabet = range('A', 'Z');

        $secret = null;

        for ($i = 1; $i <= 12; $i++) {
            $character = rand(0, 1);
            $secret .= $character === 1 ? $alphabet[array_rand($alphabet)] : rand(0, 9);
        }

        return $secret;
    }
}

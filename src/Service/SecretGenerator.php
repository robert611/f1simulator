<?php

declare(strict_types=1);

namespace App\Service;

class SecretGenerator
{
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

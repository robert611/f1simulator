<?php

declare(strict_types=1);

namespace Domain\Contract\Configuration;

class RaceScoringSystem
{
    public static function getRaceScoringSystem(): array
    {
        return [
            '1' => 25,
            '2' => 18,
            '3' => 15,
            '4' => 12,
            '5' => 10,
            '6' => 8,
            '7' => 6,
            '8' => 4,
            '9' => 2,
            '10' => 1,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            '17' => 0,
            '18' => 0,
            '19' => 0,
            '20' => 0,
        ];
    }

    public static function getPositionScore(int $position): int
    {
        return self::getRaceScoringSystem()[$position];
    }
}

<?php

declare(strict_types=1);

namespace Computer\Service\DriverStatistics;

use Computer\Entity\RaceResult;
use Computer\Entity\Season;
use Domain\Contract\Configuration\RaceScoringSystem;
use Domain\Contract\DTO\DriverDTO;

class DriverPoints
{
    public static function getDriverPoints(DriverDTO $driver, Season $season): int
    {
        $points = 0;

        $races = $season->getRaces();

        foreach ($races as $race) {
            foreach ($race->getRaceResults() as $raceResult) {
                if ($raceResult->getDriver()->getId() === $driver->getId()) {
                    $points += RaceScoringSystem::getPositionScore($raceResult->getPosition());
                }
            }
        }

        return $points;
    }

    public static function getDriverPointsByRace(RaceResult $raceResult): int
    {
        $position = $raceResult->getPosition();

        return RaceScoringSystem::getPositionScore($position);
    }
}

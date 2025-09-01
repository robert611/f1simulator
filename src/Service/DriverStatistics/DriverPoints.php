<?php

declare(strict_types=1);

namespace App\Service\DriverStatistics;

use App\Entity\Driver;
use App\Entity\Race;
use App\Entity\RaceResult;
use App\Entity\Season;
use App\Model\Configuration\RaceScoringSystem;

class DriverPoints
{
    public static function getDriverPoints(Driver $driver, ?Season $season): int
    {
        if (null === $season) {
            return 0;
        }

        $points = 0;

        foreach ($driver->getRaceResults() as $raceResult) {
            if ($raceResult->getRace()->getSeason()->getId() === $season->getId()) {
                $points += RaceScoringSystem::getPositionScore($raceResult->getPosition());
            }
        }

        return $points;
    }

    public function getDriverPointsByRace(Driver $driver, Race $race): int
    {
        $raceResult = $driver->getRaceResults()->filter(function (RaceResult $result) use ($race) {
            return $result->getRace()->getId() === $race->getId();
        });

        if ($raceResult->isEmpty()) {
            return 0;
        }

        $position = $raceResult->first()->getPosition();

        return RaceScoringSystem::getPositionScore($position);
    }
}

<?php

declare(strict_types=1);

namespace Computer\Service\TeamStatistics;

use Computer\Entity\Season;
use Computer\Service\DriverStatistics\DriverPoints;
use Domain\Entity\Team;

class TeamPoints
{
    public static function getTeamPoints(Team $team, Season $season): int
    {
        $teamDrivers = $team->getDrivers();
        $points = 0;

        foreach ($teamDrivers as $driver) {
            $points += DriverPoints::getDriverPoints($driver, $season);
        }

        return $points;
    }
}

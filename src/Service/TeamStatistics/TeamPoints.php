<?php

declare(strict_types=1);

namespace App\Service\TeamStatistics;

use App\Entity\Season;
use App\Entity\Team;
use App\Service\DriverStatistics\DriverPoints;

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

<?php

declare(strict_types=1);

namespace Computer\Service\TeamStatistics;

use Computer\Entity\Season;
use Computer\Service\DriverStatistics\DriverPoints;
use Domain\Contract\DTO\TeamDTO;

class TeamPoints
{
    public static function getTeamPoints(TeamDTO $team, Season $season): int
    {
        $teamDrivers = $team->getDrivers();
        $points = 0;

        foreach ($teamDrivers as $driver) {
            $points += DriverPoints::getDriverPoints($driver, $season);
        }

        return $points;
    }
}

<?php 

namespace App\Service\TeamStatistics;

use App\Service\DriverStatistics\DriverPoints;

class TeamPoints 
{
    public function getTeamPoints($team, $season)
    {
        $teamDrivers = $team->getDrivers();
        $points = 0;

        foreach ($teamDrivers as $driver)
        {
            $points += (new DriverPoints())->getDriverPoints($driver, $season);
        }

        return $points;
    }
}
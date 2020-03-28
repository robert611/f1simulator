<?php 

namespace App\Model;

use App\Model\DriverPoints;

class TeamPoints 
{
    public object $driversRepository;
    public object $raceResultsRepository;

    public function __construct($driversRepository, $raceResultsRepository)
    {
        $this->driversRepository = $driversRepository;
        $this->raceResultsRepository = $raceResultsRepository;
    }

    public function getTeamPoints($teamId, $season)
    {
        $teamDrivers = $this->driversRepository->findBy(['team' => $teamId]);
        $points = 0;

        foreach ($teamDrivers as $driver)
        {
            $points += (new DriverPoints($this->raceResultsRepository))->getDriverPoints($driver->getId(), $season);
        }

        return $points;
    }
}
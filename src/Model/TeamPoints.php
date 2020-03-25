<?php 

namespace App\Model;

use App\Entity\Driver;
use App\Model\DriverPoints;

class TeamPoints 
{
    public object $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getTeamPoints($teamId, $season)
    {
        $teamDrivers = $this->doctrine->getRepository(Driver::class)->findBy(['team' => $teamId]);
        $points = 0;

        foreach ($teamDrivers as $driver)
        {
            $points += (new DriverPoints($this->doctrine))->getDriverPoints($driver->getId(), $season);
        }

        return $points;
    }
}
<?php 

namespace App\Service\DriverStatistics;

use App\Entity\Driver;
use App\Service\Configuration\RacePunctation;

class DriverPoints 
{
    public array $punctation;

    public function __construct()
    {
        $this->punctation = (new RacePunctation)->getPunctation();
    }

    public function getDriverPoints(Driver $driver, ?object $season): int
    {
        if ($season == null) {
            return 0;
        }
        
        $points = 0;

        foreach ($driver->getRaceResults() as $result) {
            if ($result->getRace()->getSeason()->getId() == $season->getId())
            {
                $points += $this->punctation[$result->getPosition()];
            }
        }

        return $points;
    }

    public function getDriverPointsByRace(object $driver, object $race): int
    {
        if ($raceResult = $driver->getRaceResults()->filter(function($result) use ($race){ 
            return $result->getRace()->getId() == $race->getId();
        })) {
            $position = $raceResult->first()->getPosition();
        } else {
            return 0;
        }

        return $this->punctation[$position];
    }
}
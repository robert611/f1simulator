<?php 

namespace App\Service\DriverStatistics;

use App\Entity\Driver;
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

    public function getDriverPointsByRace(object $driver, object $race): int
    {
        if ($raceResult = $driver->getRaceResults()->filter(function($result) use ($race){ 
            return $result->getRace()->getId() == $race->getId();
        })) {
            $position = $raceResult->first()->getPosition();
        } else {
            return 0;
        }

        return RaceScoringSystem::getPositionScore($position);
    }
}
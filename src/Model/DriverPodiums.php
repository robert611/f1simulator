<?php 

namespace App\Model;

class DriverPodiums
{
    public function getDriverPodiums($driver, $season)
    {
        $races = $season->getRaces();

        $podiumsTable = $this->getPodiumsTable();

        foreach ($races as $race) {
            if ($raceResult = $driver->getRaceResults()->filter(function($result) use ($race){ 
                return $result->getRace()->getId() == $race->getId();
            })) {
                $position = $raceResult->first()->getPosition();
            } else {
                continue;
            }

            if ($position >= 1 && $position <= 3)  $podiumsTable[$position] += 1;
        }

        return $podiumsTable;
    }

    public function getPodiumsTable() {
        return [
            1 => 0,
            2 => 0,
            3 => 0
        ];
    }
}
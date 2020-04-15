<?php 

namespace App\Model\GameSimulation;

use App\Model\GameSimulation\SimulateQualifications;
use App\Model\GameSimulation\SimulateRace;

class SimulateLeagueRace
{
    public function getRaceResults(object $players): array
    {
        /* Drivers who are replaced by players */
        $drivers = $this->getDrivers($players);

        $qualificationsResults = (new SimulateQualifications)->getQualificationsResults($drivers);

        $raceResults = (new SimulateRace)->getRaceResults($drivers, $qualificationsResults);
    
        return [
            $this->setQualificationsResultsToPlayers($qualificationsResults, $players),
            $this->setRaceResultsToPlayers($raceResults, $players)
        ];
    }

    private function setQualificationsResultsToPlayers($qualificationsResults, $players): array
    {
        foreach ($qualificationsResults as $key => $result) {
            $player = $players->filter(function($player) use ($result) {
                return $player->getDriver() == $result;
            })->first();
            $qualificationsResults[$key] = $player;
        }

       return $qualificationsResults;
    }

    private function setRaceResultsToPlayers($raceResults, $players): array
    {
        foreach ($raceResults as $key => $result) {
            $player = $players->filter(function($player) use ($result) {
                return $player->getDriver()->getId() == $result;
            })->first();
            $raceResults[$key] = $player;
        }

       return $raceResults;
    }

    private function getDrivers($players): array
    {
        $drivers = $players->map(function($player) {
            return $player->getDriver();
        });

        return [...$drivers->getValues()];
    }
}
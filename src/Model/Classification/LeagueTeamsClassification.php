<?php 

namespace App\Model\Classification;

use App\Model\TeamStatistics\LeagueTeamsPoints;

class LeagueTeamsClassification 
{
    public function getClassification(object $players)
    {
        $drivers = $this->getDrivers($players); /* Drivers who are replaced by players */
        $teams = $this->getTeams($drivers); /* Get all teams involved in league from which players are given */
        
        $teamsPoints = new LeagueTeamsPoints();

        /* This function takes teams, and return teams with set points */
        $teams = $teamsPoints->getTeamsPoints($this->getTeamsWithPlayers($teams, $players));
        
        /* Change Array Collection to array so it can be sort */
        $teams = [...$teams->getValues()];

        /* Sort Teams according to it's got points */
        usort($teams, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        
        return $teams;
    }

    private function getTeamsWithPlayers(object $teams, object $players): object
    {
        /* In this way, every team from given league will have assigned its players to its object, and it will be easier to get points in LeagueTeamPoints model */
        $teams->map(function($team) use ($players) {
            $players->map(function($player) use ($team) {
                if ($player->getDriver()->getTeam()->getId() == $team->getId())
                    $team->addPlayer($player);
            });
        });

        return $teams;
    }

    private function getTeams(object $drivers): object
    {
        $uniqueTeams = array();

        $teams = $drivers->map(function($driver) {
            return $driver->getTeam(); 
        });

        /* Filter teams so only unique ones will be return */
        $teams = $teams->filter(function($team) use (&$uniqueTeams) {
            if (!in_array($team->getId(), $uniqueTeams)) {
                $uniqueTeams[] = $team->getId();
                return true;
            }

            return false;
        });

        return $teams;
    }

    private function getDrivers(object $players): object
    {
        $drivers = $players->map(function($player) {
            return $player->getDriver();
        });

        return $drivers;
    }
}
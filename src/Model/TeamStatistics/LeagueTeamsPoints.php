<?php 

namespace App\Model\TeamStatistics;

use App\Model\DriverStatistics\LeaguePlayerPoints;

class LeagueTeamsPoints 
{
    public function getTeamsPoints(object $league)
    {
        $players = $league->getPlayers();

        $drivers = $this->getDrivers($players); /* Drivers who are replaced by players */
        $teams = $this->getTeams($drivers); /* Get all teams involved in league from which players are given */

        $teams = $this->getTeamsWithPlayers($teams, $players);

        $teams->map(function($team) {
            $points = 0;

            $team->getPlayers()->map(function($player) use (&$points) {
                $points += (new LeaguePlayerPoints)->getPlayerPoints($player);
            });

            $team->setPoints($points);
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
        /* It is important to know that filter changes unwanted values to null */
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
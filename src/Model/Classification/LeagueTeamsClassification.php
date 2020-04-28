<?php 

namespace App\Model\Classification;

use App\Model\TeamStatistics\LeagueTeamsPoints;

class LeagueTeamsClassification 
{
    public function getClassification(object $league)
    {
        $teamsPoints = new LeagueTeamsPoints();

        /* This function takes league, and return teams from that league with set points */
        $teams = $teamsPoints->getTeamsPoints($league);
        
        /* Change Array Collection to array so it can be sort */
        $teams = [...$teams->getValues()];

        /* Sort Teams according to it's got points */
        usort($teams, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        
        return $teams;
    }
}
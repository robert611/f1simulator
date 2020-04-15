<?php 

namespace App\Model\Classification;

use App\Model\TeamStatistics\TeamPoints;

class SeasonTeamsClassification 
{
    public function getClassification(array $teams, $season): array
    {
        /* In default teams have no assign points got in current season in database, so it has to be done here */
        foreach($teams as $team) {
            $points = $season ? (new TeamPoints())->getTeamPoints($team, $season) : 0;
            $team->setPoints($points);
        }

        /* Sort Teams according to it's got points */
        usort($teams, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        
        foreach($teams as $key => &$team) {
            $team->setPosition($key + 1);
        }

        return $teams;
    }
}
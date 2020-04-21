<?php 

namespace App\Model\Classification;

use App\Model\TeamStatistics\TeamPoints;

class SeasonTeamsClassification 
{
    public function getClassification(array $teams, ?object $season): array
    {
        $this->assaignTeamsPoints($teams, $season);
        $this->sortTeamsAccordingToPoints($teams);

        return $teams;
    }

    private function assaignTeamsPoints(&$teams, $season)
    {
        /* Teams points are not assaign in database */
        /* If user did not start season, then every team has 0 points by default */
        foreach($teams as $team) {
            $points = $season ? (new TeamPoints())->getTeamPoints($team, $season) : 0;
            $team->setPoints($points);
        }
    }

    private function sortTeamsAccordingToPoints(&$teams)
    {
        usort($teams, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
    }
}
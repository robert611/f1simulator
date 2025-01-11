<?php 

declare(strict_types=1);

namespace App\Service\Classification;

use App\Entity\UserSeason;
use App\Service\TeamStatistics\LeagueTeamsPoints;

class LeagueTeamsClassification 
{
    public function __construct(
        public readonly LeagueTeamsPoints $leagueTeamsPoints,
    ) {
    }

    public function getClassification(UserSeason $league): array
    {
        /* This function takes league, and return teams from that league with set points */
        $teams = $this->leagueTeamsPoints->getTeamsPoints($league);

        /* Sort teams according to their points */
        usort($teams, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        
        return $teams;
    }
}
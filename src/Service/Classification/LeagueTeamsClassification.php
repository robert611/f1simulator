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
        /* This function takes the league and returns teams from that league with set points */
        // @TODO Tutaj dodawane jest properties points w magiczny sposób do encji
        // @TODO Trzeba to zrefaktoryzować
        $teams = $this->leagueTeamsPoints->getTeamsPoints($league);

        /* Sort teams according to their points */
        usort($teams, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        
        return $teams;
    }
}
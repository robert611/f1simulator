<?php 

declare(strict_types=1);

namespace App\Service\TeamStatistics;

use App\Entity\Season;
use App\Entity\Team;
use App\Service\DriverStatistics\DriverPoints;

class TeamPoints 
{
    public static function getTeamPoints(Team $team, Season $season): int
    {
        $teamDrivers = $team->getDrivers();
        $points = 0;

        // Mam przed sobą zadanie obliczenia ile zespół w danym sezonie z komputerem
        // Ma punktów

        // Obecnie pobieram jego kierowców, wyszukuje kierowców (tabela driver) z rezultatów wyścigu sezonu
        // Sezon też trzeba podać
        // Zastanawiam się nad dodaniem większej ilości danych do bazy danych
        // Może nawet tabeli season_team_result
        // Żeby uprościć obliczanie, trzeba się tu zastanowić jak najprościej będzie można to zrobić


        foreach ($teamDrivers as $driver) {
            $points += DriverPoints::getDriverPoints($driver, $season);
        }

        return $points;
    }
}
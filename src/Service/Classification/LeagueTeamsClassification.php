<?php

declare(strict_types=1);

namespace App\Service\Classification;

use Domain\Entity\Team;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Model\TeamsClassification;
use App\Model\TeamSeasonResult;
use Doctrine\Common\Collections\Collection;

class LeagueTeamsClassification
{
    public function getClassification(UserSeason $league): TeamsClassification
    {
        $teams = $league->getLeagueTeams();

        $teamsPointsTable = [];

        foreach ($teams as $team) {
            $points = 0;

            $players = $this->getTeamPlayers($team, $league->getPlayers());

            foreach ($players as $player) {
                $points += $player->getPoints();
            }

            $teamsPointsTable[$team->getId()] = $points;
        }

        // Sorts using descending order and preserves array keys
        arsort($teamsPointsTable);

        $teamSeasonResults = [];

        foreach ($teams as $team) {
            $keyPosition = array_search($team->getId(), array_keys($teamsPointsTable));
            $position = $keyPosition + 1;
            $teamSeasonResults[] = TeamSeasonResult::create($team, $teamsPointsTable[$team->getId()], $position);
        }

        return TeamsClassification::create($teamSeasonResults);
    }

    /**
     * @param Collection<UserSeasonPlayer> $players
     * @return UserSeasonPlayer[]
     */
    private function getTeamPlayers(Team $team, Collection $players): array
    {
        return $players
            ->filter(function (UserSeasonPlayer $player) use ($team): bool {
                return $player->getDriver()->getTeam()->getId() === $team->getId();
            })
            ->toArray();
    }
}

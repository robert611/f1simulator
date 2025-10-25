<?php

declare(strict_types=1);

namespace Multiplayer\Service;

use Multiplayer\Model\TeamsClassification;
use Multiplayer\Model\TeamLeagueResult;
use Doctrine\Common\Collections\Collection;
use Domain\Entity\Team;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;

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
            $teamSeasonResults[] = TeamLeagueResult::create($team, $teamsPointsTable[$team->getId()], $position);
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

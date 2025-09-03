<?php 

declare(strict_types=1);

namespace App\Service\TeamStatistics;

use App\Entity\Team;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Collection;

class LeagueTeamsPoints
{
    public function __construct(
        public readonly TeamRepository $teamRepository,
    ) {
    }

    public function getTeamsPoints(UserSeason $league): array
    {
        $players = $league->getPlayers();

        $teams = $league->getLeagueTeams(); /* Get all teams involved in the league from which players are given */

        $teams = $this->getTeamsWithPlayers($teams, $players);

        foreach ($teams as $team) {
            $points = 0;

            foreach ($team->getPlayers() as $player) {
                $points += $player->getPoints();
            }

            $team->setPoints($points);
        }

        return $teams;
    }

    /**
     * @param Team[] $teams
     * @param Collection<UserSeasonPlayer> $players
     * @return Team[]
     */
    private function getTeamsWithPlayers(array $teams, Collection $players): array
    {
        foreach ($teams as $team) {
            $players->map(function(UserSeasonPlayer $player) use ($team) {
                if ($player->getDriver()->getTeam()->getId() === $team->getId()) {
                    $team->addPlayer($player);
                }
            });
        }

        return $teams;
    }
}

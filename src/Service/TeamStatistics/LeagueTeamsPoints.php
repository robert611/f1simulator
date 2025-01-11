<?php 

declare(strict_types=1);

namespace App\Service\TeamStatistics;

use App\Entity\Driver;
use App\Entity\Team;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Service\DriverStatistics\LeaguePlayerPoints;
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

        $drivers = $this->getDrivers($players); /* Drivers who are replaced by players */
        $teams = $this->getTeams($drivers); /* Get all teams involved in league from which players are given */

        $teams = $this->getTeamsWithPlayers($teams, $players);

        foreach ($teams as $team) {
            $points = 0;

            foreach ($team->getPlayers() as $player) {
                $points += (new LeaguePlayerPoints)->getPlayerPoints($player);
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
            $players->map(function($player) use ($team) {
                /** @var UserSeasonPlayer $player */
                if ($player->getDriver()->getTeam()->getId() === $team->getId()) {
                    $team->addPlayer($player);
                }
            });
        }

        return $teams;
    }

    /**
     * @param Collection<Driver> $drivers
     * @return Team[]
     */
    private function getTeams(Collection $drivers): array
    {
        $teamsIds = $drivers->map(function($driver) {
            /** @var Driver $driver */
            return $driver->getTeam()->getId();
        })->toArray();

        return $this->teamRepository->findBy(['id' => $teamsIds]);
    }

    /**
     * @param Collection<UserSeasonPlayer> $players
     * @return Collection<Driver>
     */
    private function getDrivers(Collection $players): Collection
    {
        return $players->map(function($player) {
            /** @var UserSeasonPlayer $player */
            return $player->getDriver();
        });
    }
}
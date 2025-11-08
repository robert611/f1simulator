<?php

declare(strict_types=1);

namespace Multiplayer\Service;

use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\DTO\TeamDTO;
use Domain\DomainFacadeInterface;
use Multiplayer\Model\TeamsClassification;
use Multiplayer\Model\TeamLeagueResult;
use Doctrine\Common\Collections\Collection;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Shared\HashTable;

class LeagueTeamsClassification
{
    public function __construct(
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    public function getClassification(UserSeason $league): TeamsClassification
    {
        $drivers = $this->domainFacade->getAllDrivers();

        /** @var DriverDTO[] $drivers */
        $drivers = HashTable::fromObjectArray($drivers, 'getId');

        $driversIds = $league->getLeagueDriversIds();

        $teams = $this->domainFacade->getTeamsByDriversIds($driversIds);

        $teamsPointsTable = [];

        foreach ($teams as $team) {
            $points = 0;

            $players = $this->getTeamPlayers($team, $league->getPlayers(), $drivers);

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
     * @param DriverDTO[] $drivers
     * @return UserSeasonPlayer[]
     */
    private function getTeamPlayers(TeamDTO $team, Collection $players, array $drivers): array
    {
        return $players
            ->filter(function (UserSeasonPlayer $player) use ($team, $drivers): bool {
                return $drivers[$player->getDriverId()]->getTeam()->getId() === $team->getId();
            })
            ->toArray();
    }
}

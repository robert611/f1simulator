<?php

declare(strict_types=1);

namespace Multiplayer\Service\GameSimulation;

use Doctrine\Common\Collections\Collection;
use Domain\Contract\DTO\DriverDTO;
use Domain\DomainFacadeInterface;
use Domain\Service\GameSimulation\CouponsGenerator;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Model\GameSimulation\LeagueQualificationResultsCollection;
use Multiplayer\Model\GameSimulation\LeagueRaceResultsDTO;

class SimulateLeagueRace
{
    public function __construct(
        private readonly SimulateLeagueQualifications $simulateLeagueQualifications,
        private readonly CouponsGenerator $couponsGenerator,
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    public function simulateRaceResults(UserSeason $userSeason): LeagueRaceResultsDTO
    {
        $players = $userSeason->getPlayers();

        $driversIds = UserSeasonPlayer::getPlayersDriversIds($players);

        $drivers = $this->domainFacade->getDriversByIds($driversIds);

        $qualificationsResults = $this->simulateLeagueQualifications->getLeagueQualificationsResults($userSeason);

        $raceResults = $this->getLeagueRaceResults($drivers, $qualificationsResults);

        $preparedRaceResults = $this->setRaceResultsToPlayers($raceResults, $players);

        return LeagueRaceResultsDTO::create($qualificationsResults, $preparedRaceResults);
    }

    /**
     * @param DriverDTO[] $drivers
     *
     * @return int[]
     */
    public function getLeagueRaceResults(
        array $drivers,
        LeagueQualificationResultsCollection $qualificationsResults,
    ): array {
        $results = [];

        $coupons = $this->couponsGenerator->generateCoupons($qualificationsResults->toPlainDriverArray());

        for ($position = 1; $position <= count($drivers); $position++) {
            do {
                $driverId = $coupons[array_rand($coupons)];
            } while (in_array($driverId, $results));

            $results[$position] = $driverId;
        }

        return $results;
    }

    /**
     * @param int[] $raceResults
     * @param Collection<UserSeasonPlayer> $players
     *
     * @return UserSeasonPlayer[]
     */
    private function setRaceResultsToPlayers(array $raceResults, Collection $players): array
    {
        foreach ($raceResults as $key => $driverId) {
            $player = UserSeasonPlayer::getPlayerByDriverId($players, $driverId);
            $raceResults[$key] = $player;
        }

        return $raceResults;
    }
}

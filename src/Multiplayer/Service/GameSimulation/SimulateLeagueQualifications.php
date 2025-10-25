<?php

declare(strict_types=1);

namespace Multiplayer\Service\GameSimulation;

use Domain\Service\GameSimulation\QualificationsHelperService;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Model\GameSimulation\LeagueQualificationResult;
use Multiplayer\Model\GameSimulation\LeagueQualificationResultsCollection;

class SimulateLeagueQualifications
{
    public function __construct(
        private readonly QualificationsHelperService $helperService,
    ) {
    }

    public function getLeagueQualificationsResults(UserSeason $userSeason): LeagueQualificationResultsCollection
    {
        $players = $userSeason->getPlayers();

        $drivers = UserSeasonPlayer::getPlayersDrivers($players);

        $result = LeagueQualificationResultsCollection::create();

        $driversInResults = [];

        $coupons = $this->helperService->generateCoupons();

        $totalDrivers = count($drivers);

        for ($position = 1; $position <= $totalDrivers; $position++) {
            // If both drivers from a given team are already drawn,
            // repeat the draw until a team with < 2 finished drivers is picked
            do {
                $teamName = $coupons[array_rand($coupons)];
            } while ($this->helperService->checkIfBothDriversFromATeamAlreadyFinished($teamName, $driversInResults));

            // Draw one of the remaining drivers from the selected team
            $driver = $this->helperService->drawDriverFromATeam($teamName, $drivers, $driversInResults);

            // If there is no driver (e.g., team not in league or all finished), retry this position
            if ($driver) {
                $userSeasonPlayer = UserSeasonPlayer::getPlayerByDriverId($players, $driver->getId());
                $qualificationResult = LeagueQualificationResult::create($userSeasonPlayer, $position);
                $result->addQualificationResult($qualificationResult);
                $driversInResults[] = $driver;
            } else {
                $position = $position - 1;
            }
        }

        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Multiplayer\Service\GameSimulation;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\Configuration\RaceScoringSystem;
use Domain\DomainFacadeInterface;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Entity\UserSeasonQualification;
use Multiplayer\Entity\UserSeasonRace;
use Multiplayer\Entity\UserSeasonRaceResult;
use Multiplayer\Service\LeagueClassifications;
use Throwable;

readonly class SimulateLeagueRace
{
    public function __construct(
        private SimulateLeagueRaceResults $simulateLeagueRaceResults,
        private LeagueClassifications $leagueClassifications,
        private DomainFacadeInterface $domainFacade,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function simulateRace(UserSeason $userSeason): void
    {
        /** @var null|UserSeasonRace $lastRace */
        $lastRace = $userSeason->getRaces()->last();
        $track = $lastRace
            ? $this->domainFacade->getNextTrack($lastRace->getTrackId())
            : $this->domainFacade->getFirstTrack();

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            /* Save race in the database */
            $race = new UserSeasonRace();

            $race->setTrackId($track->getId());
            $race->setSeason($userSeason);

            $this->entityManager->persist($race);
            $this->entityManager->flush();

            $leagueRaceResultsDTO = $this->simulateLeagueRaceResults->simulateRaceResults($userSeason);

            $qualificationsResults = $leagueRaceResultsDTO->getQualificationsResults();

            foreach ($qualificationsResults->getQualificationResults() as $result) {
                $qualification = new UserSeasonQualification();
                $qualification->setRace($race);
                $qualification->setPlayer($result->getUserSeasonPlayer());
                $qualification->setPosition($result->getPosition());

                $this->entityManager->persist($qualification);
            }

            $this->entityManager->flush();

            $raceResults = $leagueRaceResultsDTO->getRaceResults();

            /** @var UserSeasonPlayer $player */
            foreach ($raceResults as $position => $player) {
                $points = RaceScoringSystem::getPositionScore($position);

                $raceResult = UserSeasonRaceResult::create($position, $points, $race, $player);
                $player->addPoints($points);

                $this->entityManager->persist($raceResult);
            }

            $this->entityManager->flush();
            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollBack();
            throw $e;
        }

        $this->leagueClassifications->recalculatePlayersPositions($userSeason);
    }
}

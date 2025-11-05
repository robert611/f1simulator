<?php

declare(strict_types=1);

namespace Computer\Service;

use Computer\Model\CurrentDriverSeason;
use Computer\Repository\RaceRepository;
use Computer\Repository\SeasonRepository;
use Computer\Service\DriverStatistics\DriverPoints;
use Domain\DomainFacadeInterface;

class CurrentDriverSeasonService
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly RaceRepository $raceRepository,
        private readonly SeasonClassifications $seasonClassifications,
        private readonly SeasonTeamsClassification $seasonTeamsClassification,
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    public function buildCurrentDriverSeasonData(
        int $userId,
        ClassificationType $classificationType,
        ?int $raceId,
    ): ?CurrentDriverSeason {
        $season = $this->seasonRepository->findOneBy(['user' => $userId, 'completed' => 0]);

        if (null === $season) {
            return null;
        }

        $driver = $season->getDriver();

        $driver = $this->domainFacade->getDriverById($driver->getId());

        $driverPoints = DriverPoints::getDriverPoints($driver, $season);

        if ($season->getRaces()->last()) {
            $currentTrack = $this->domainFacade->getNextTrack($season->getRaces()->last()->getTrack()->getId());
        } else {
            $currentTrack = $this->domainFacade->getFirstTrack();
        }

        $numberOfRacesInTheSeason = $this->domainFacade->getTracksCount();

        $classification = $this->seasonClassifications->getClassificationBasedOnType(
            $season,
            $classificationType,
            $raceId,
        );

        $teamsClassification = $this->seasonTeamsClassification->getClassification($userId);

        $race = null;
        if ($raceId) {
            $race = $this->raceRepository->find($raceId);
        }

        return CurrentDriverSeason::create(
            $season,
            $driverPoints,
            $season->getDriverPodiumsDTO(),
            $currentTrack,
            $numberOfRacesInTheSeason,
            $classification,
            $teamsClassification,
            $race,
        );
    }
}

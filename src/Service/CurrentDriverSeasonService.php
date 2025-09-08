<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\CurrentDriverSeason;
use App\Repository\RaceRepository;
use App\Repository\SeasonRepository;
use App\Repository\TrackRepository;
use App\Service\Classification\ClassificationType;
use App\Service\Classification\SeasonClassifications;
use App\Service\Classification\SeasonTeamsClassification;
use App\Service\DriverStatistics\DriverPoints;

class CurrentDriverSeasonService
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly TrackRepository $trackRepository,
        private readonly RaceRepository $raceRepository,
        private readonly SeasonClassifications $seasonClassifications,
        private readonly SeasonTeamsClassification $seasonTeamsClassification,
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

        $driverPoints = DriverPoints::getDriverPoints($driver, $season);

        if ($season->getRaces()->last()) {
            $currentTrack = $this->trackRepository->getNextTrack($season->getRaces()->last()->getTrack()->getId());
        } else {
            $currentTrack = $this->trackRepository->getFirstTrack();
        }

        $numberOfRacesInTheSeason = $this->trackRepository->count();

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

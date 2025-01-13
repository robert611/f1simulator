<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\CurrentDriverSeason;
use App\Repository\DriverRepository;
use App\Repository\SeasonRepository;
use App\Repository\TrackRepository;
use App\Service\Classification\ClassificationType;
use App\Service\Classification\SeasonClassifications;
use App\Service\DriverStatistics\DriverPodiumsService;
use App\Service\DriverStatistics\DriverPoints;

class CurrentDriverSeasonService
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly TrackRepository $trackRepository,
        private readonly DriverRepository $driverRepository,
        private readonly SeasonClassifications $seasonClassifications,
    ) {
    }

    public function buildCurrentDriverSeasonData(
        int $userId,
        ClassificationType $classificationType,
        null|string|int $raceId,
    ): ?CurrentDriverSeason {
        $season = $this->seasonRepository->findOneBy(['user' => $userId, 'completed' => 0]);

        if (null === $season) {
            return null;
        }

        $driver = $season->getDriver();

        $driverPoints = DriverPoints::getDriverPoints($driver, $season);

        $driverPodiums = DriverPodiumsService::getDriverPodiumsDTO($driver, $season);

        if ($season->getRaces()->last()) {
            $currentTrack = $this->trackRepository->find($season->getRaces()->last()->getTrack()->getId() + 1);
        } else {
            $currentTrack = $this->trackRepository->findOneBy([], ['id' => 'ASC']);
        }

        $numberOfRacesInTheSeason = $this->trackRepository->count();

        $drivers = $this->driverRepository->findAll();

        $this->seasonClassifications->setEntryData($drivers, $season, $raceId);

        $classification = $this->seasonClassifications->getClassificationBasedOnType($classificationType);

        return CurrentDriverSeason::create(
            $season,
            $driverPoints,
            $driverPodiums,
            $currentTrack,
            $numberOfRacesInTheSeason,
            $classification,
            null,
        );
    }
}

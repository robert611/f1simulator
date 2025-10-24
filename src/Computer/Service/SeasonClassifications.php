<?php

declare(strict_types=1);

namespace Computer\Service;

use Computer\Entity\Qualification;
use Computer\Entity\Season;
use Computer\Model\DriverRaceResult;
use Computer\Model\DriversClassification;
use Computer\Repository\QualificationRepository;
use Computer\Repository\RaceRepository;
use Computer\Repository\RaceResultRepository;
use Computer\Service\DriverStatistics\DriverPoints;
use Domain\Repository\DriverRepository;

class SeasonClassifications
{
    public function __construct(
        private readonly DriverRepository $driverRepository,
        private readonly RaceRepository $raceRepository,
        private readonly RaceResultRepository $raceResultRepository,
        private readonly QualificationRepository $qualificationRepository,
    ) {
    }

    public function getClassificationBasedOnType(
        Season $season,
        ClassificationType $classificationType,
        ?int $raceId,
    ): array|DriversClassification {
        return match ($classificationType) {
            ClassificationType::RACE => $this->getRaceClassification($season, $raceId),
            ClassificationType::QUALIFICATIONS => $this->getQualificationsClassification($season, $raceId),
            default => $this->getDriversClassification($season),
        };
    }

    public function getDefaultDriversClassification(): DriversClassification
    {
        $drivers = $this->driverRepository->findAll();

        return DriversClassification::createDefaultClassification($drivers);
    }

    public function getDriversClassification(Season $season): DriversClassification
    {
        $drivers = $this->driverRepository->findAll();

        $driverRaceResults = [];

        foreach ($drivers as $driver) {
            $points = DriverPoints::getDriverPoints($driver, $season);
            $driverRaceResults[] = DriverRaceResult::create($driver, $points, 0);
        }

        $driverRaceResults = DriverRaceResult::calculatePositions($driverRaceResults);

        return DriversClassification::create($driverRaceResults);
    }

    public function getRaceClassification(Season $season, int $raceId): DriversClassification
    {
        $raceResults = $this->raceResultRepository->findBy(['race' => $raceId]);

        $driverRaceResults = [];

        foreach ($raceResults as $raceResult) {
            $points = DriverPoints::getDriverPointsByRace($raceResult);
            $driverRaceResults[] = DriverRaceResult::create($raceResult->getDriver(), $points, 0);
        }

        $driverRaceResults = DriverRaceResult::calculatePositions($driverRaceResults);

        return DriversClassification::create($driverRaceResults);
    }

    /**
     * @return Qualification[]
     */
    public function getQualificationsClassification(Season $season, int $raceId): array
    {
        $race = $this->raceRepository->findOneBy(['id' => $raceId, 'season' => $season]);

        return $this->qualificationRepository->getSortedRaceQualifications($race->getId());
    }
}

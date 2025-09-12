<?php

declare(strict_types=1);

namespace App\Service\Classification;

use App\Entity\Driver;
use App\Entity\Qualification;
use App\Entity\Season;
use App\Model\DriverRaceResult;
use App\Model\DriversClassification;
use App\Repository\DriverRepository;
use App\Repository\RaceRepository;
use App\Service\DriverStatistics\DriverPoints;
use Doctrine\Common\Collections\Collection;

class SeasonClassifications
{
    public function __construct(
        private readonly DriverRepository $driverRepository,
        private readonly RaceRepository $raceRepository,
    ) {
    }

    public function getClassificationBasedOnType(
        Season $season,
        ClassificationType $classificationType,
        ?int $raceId,
    ): Collection|array|DriversClassification {
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

    private function getDriversClassification(Season $season): DriversClassification
    {
        // @todo, write test for this function
        $drivers = $this->driverRepository->findAll();

        $driverRaceResults = [];

        foreach ($drivers as $driver) {
            $points = DriverPoints::getDriverPoints($driver, $season);
            $driverRaceResults[] = DriverRaceResult::create($driver, $points, 0);
        }

        $driverRaceResults = DriverRaceResult::calculatePositions($driverRaceResults);

        return DriversClassification::create($driverRaceResults);
    }

    private function getRaceClassification(Season $season, int $raceId): array
    {
        $drivers = $this->driverRepository->findAll();

        $race = $this->raceRepository->findOneBy(['id' => $raceId, 'season' => $season]);

        /* By default, drivers have no assigned points in a database, so it has to be done here */
        foreach ($drivers as $driver) {
            $points = DriverPoints::getDriverPointsByRace($driver, $race);
            $driver->setPoints($points);
        }

        return $this->setDriversPositions($drivers);
    }

    /**
     * @return Collection<Qualification>
     */
    private function getQualificationsClassification(Season $season, int $raceId): Collection
    {
        $race = $this->raceRepository->findOneBy(['id' => $raceId, 'season' => $season]);

        return $race->getQualifications();
    }

    /**
     * @param Driver[] $drivers
     * @return Driver[]
     */
    private function setDriversPositions(array $drivers): array
    {
        /* Sort drivers according to possessed points */
        usort($drivers, function (Driver $a, Driver $b) {
            return $a->getPoints() < $b->getPoints();
        });

        foreach ($drivers as $key => $driver) {
            $driver->setPosition($key + 1);
        }

        return $drivers;
    }
}

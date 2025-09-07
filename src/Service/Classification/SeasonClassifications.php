<?php 

namespace App\Service\Classification;

use App\Entity\Driver;
use App\Entity\Qualification;
use App\Entity\Race;
use App\Entity\Season;
use App\Model\DriversClassification;
use App\Repository\DriverRepository;
use App\Service\DriverStatistics\DriverPoints;
use Doctrine\Common\Collections\Collection;

class SeasonClassifications
{
    public function __construct(
       private readonly DriverRepository $driverRepository,
    ) {
    }

    public function getClassificationBasedOnType(
        Season $season,
        ClassificationType $classificationType,
        ?int $raceId,
    ): Collection|array {
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

    private function getDriversClassification(Season $season): array
    {
        $drivers = $this->driverRepository->findAll();

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($drivers as $driver) {
            $points = DriverPoints::getDriverPoints($driver, $season);
            $driver->setPoints($points);
        }

        return $this->setDriversPositions($drivers);
    }

    private function getRaceClassification(Season $season, ?int $raceId): array
    {
        $drivers = $this->driverRepository->findAll();

        $race = $this->findRace($season, $raceId);

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
    private function getQualificationsClassification(Season $season, ?int $raceId): Collection
    {
        $race = $this->findRace($season, $raceId);

        return $race->getQualifications();
    }

    private function findRace(Season $season, ?int $raceId): Race
    {
        $race = $season->getRaces()->filter(function($race) use ($raceId) {
            return $race->getId() == $raceId;
        })->first();

        /* If user typed un proper race id, it matches the default name in twig */
        if (!$race) {
            $race = $season->getRaces()->first();
        }

        return $race;
    }

    /**
     * @param Driver[] $drivers
     * @return Driver[]
     */
    private function setDriversPositions(array $drivers): array
    {
        /* Sort drivers according to possessed points */
        usort ($drivers, function(Driver $a, Driver $b) {
            return $a->getPoints() < $b->getPoints();
        });

        foreach ($drivers as $key => $driver) {
            $driver->setPosition($key + 1);
        }

        return $drivers;
    }
}

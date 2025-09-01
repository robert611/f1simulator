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
    /** @var Driver[] $drivers */
    public array $drivers;
    public DriverPoints $driverPoints;
    public ?object $season;
    public $raceId;

    public function __construct(
       private readonly DriverRepository $driverRepository,
    ) {
    }

    /**
     * @param Driver[] $drivers
     */
    public function setEntryData(array $drivers, ?object $season, $raceId): void
    {
        $this->drivers = $drivers;
        $this->driverPoints = new DriverPoints();
        $this->season = $season;
        $this->raceId = $raceId;
    }

    public function getClassificationBasedOnType(ClassificationType $classificationType)
    {
        if (!$this->season) {
            return $this->getDriversClassification();
        }

        switch ($classificationType) {
            case ClassificationType::RACE:
                $classification = $this->getRaceClassification(); // zwraca Driver[]
                break;  
            case ClassificationType::DRIVERS:
                $classification = $this->getDriversClassification(); // zwraca Driver[]
                break;
            case ClassificationType::QUALIFICATIONS:
                $classification = $this->getQualificationsClassification(); // zwraca Collection<Qualification>
                break;
            default: 
                $classification = $this->getQualificationsClassification(); /* It matches the default option in html */
        }

        return $classification;
    }

    public function getDefaultDriversClassification(): DriversClassification
    {
        $drivers = $this->driverRepository->findAll();

        return DriversClassification::createDefaultClassification($drivers);
    }

    private function getDriversClassification()
    {
        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($this->drivers as &$driver) {
            $points = $this->driverPoints->getDriverPoints($driver, $this->season);
            $driver->setPoints($points);
        }

        return $this->setDriversPositions($this->drivers);
    }

    private function getRaceClassification(): array
    {
        $race = $this->findRace($this->raceId);

        /* By default, drivers have no assigned points in a database, so it has to be done here */
        foreach ($this->drivers as $driver) {
            $points = $this->driverPoints->getDriverPointsByRace($driver, $race);
            $driver->setPoints($points);
        }

        return $this->setDriversPositions($this->drivers);
    }

    /**
     * @return Collection<Qualification>
     */
    private function getQualificationsClassification(): Collection
    {
        $race = $this->findRace($this->raceId);

        return $race->getQualifications();
    }

    private function findRace($id): Race
    {
        $race = $this->season->getRaces()->filter(function($race) use ($id) {
            return $race->getId() == $id;
        })->first();

        /* If user typed un proper race id, it matches the default name in twig */
        if (!$race) {
            $race = $this->season->getRaces()->first();
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
        usort ($drivers, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });

        foreach ($drivers as $key => &$driver) {
            $driver->setPosition($key + 1);
        }

        return $drivers;
    }
}
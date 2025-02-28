<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Driver;

class DriversClassification
{
    /** @var DriverRaceResult[] $driversRaceResults */
    private array $driversRaceResults;

    /**
     * @return DriverRaceResult[]
     */
    public function getDriversRaceResults(): array
    {
        return $this->driversRaceResults;
    }

    /**
     * This classification is shown if there is no active user season
     * All drivers have zero points and are displayed in random order
     *
     * @param Driver[] $drivers
     */
    public static function createDefaultClassification(array $drivers): self
    {
        $driversRaceResults = [];

        $position = 1;

        foreach ($drivers as $driver) {
            $driversRaceResults[] = DriverRaceResult::create($driver, 0, $position);
            $position += 1;
        }

        $driversClassification = new self();
        $driversClassification->driversRaceResults = $driversRaceResults;

        return $driversClassification;
    }
}

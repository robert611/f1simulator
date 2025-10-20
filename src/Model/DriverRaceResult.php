<?php

declare(strict_types=1);

namespace App\Model;

use Domain\Entity\Driver;

class DriverRaceResult
{
    private Driver $driver;
    private int $points;
    private int $position;

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public static function create(Driver $driver, int $points, int $position): self
    {
        $driverRaceResult = new self();
        $driverRaceResult->driver = $driver;
        $driverRaceResult->points = $points;
        $driverRaceResult->position = $position;

        return $driverRaceResult;
    }

    public function overwritePosition($position): void
    {
        $this->position = $position;
    }

    /**
     * @param DriverRaceResult[] $driverRaceResults
     * @return DriverRaceResult[]
     */
    public static function calculatePositions(array $driverRaceResults): array
    {
        usort($driverRaceResults, function (DriverRaceResult $a, DriverRaceResult $b) {
            return $b->getPoints() <=> $a->getPoints();
        });

        foreach ($driverRaceResults as $index => $driverRaceResult) {
            $driverRaceResult->overwritePosition($index + 1);
        }

        return $driverRaceResults;
    }
}

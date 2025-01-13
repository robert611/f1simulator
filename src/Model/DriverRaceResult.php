<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Driver;

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
}

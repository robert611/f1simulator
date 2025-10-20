<?php

declare(strict_types=1);

namespace App\Model\GameSimulation;

use Domain\Entity\Driver;

class QualificationResult
{
    private Driver $driver;
    private int $position;

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public static function create(Driver $driver, int $position): self
    {
        $qualificationResult = new self();
        $qualificationResult->driver = $driver;
        $qualificationResult->position = $position;

        return $qualificationResult;
    }
}

<?php

declare(strict_types=1);

namespace Computer\Model\GameSimulation;

use Domain\Contract\DTO\DriverDTO;

class QualificationResult
{
    private DriverDTO $driver;
    private int $position;

    public function getDriver(): DriverDTO
    {
        return $this->driver;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public static function create(DriverDTO $driver, int $position): self
    {
        $qualificationResult = new self();
        $qualificationResult->driver = $driver;
        $qualificationResult->position = $position;

        return $qualificationResult;
    }
}

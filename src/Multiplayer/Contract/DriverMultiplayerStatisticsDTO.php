<?php

declare(strict_types=1);

namespace Multiplayer\Contract;

class DriverMultiplayerStatisticsDTO
{
    private int $seasonsPlayed = 0;

    public function getSeasonsPlayed(): int
    {
        return $this->seasonsPlayed;
    }

    public static function create(int $seasonPlayed): self
    {
        $driverComputerStatisticsDTO = new self();
        $driverComputerStatisticsDTO->seasonsPlayed = $seasonPlayed;

        return $driverComputerStatisticsDTO;
    }
}

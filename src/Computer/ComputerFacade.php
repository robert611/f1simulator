<?php

declare(strict_types=1);

namespace Computer;

use Computer\Contract\DriverComputerStatisticsDTO;
use Computer\Repository\SeasonRepository;

readonly class ComputerFacade implements ComputerFacadeInterface
{
    public function __construct(
        private SeasonRepository $seasonRepository,
    ) {
    }

    public function canDriverBeSafelyDeleted(int $driverId): bool
    {
        if ($this->seasonRepository->count(['driverId' => $driverId]) > 0) {
            return false;
        }

        return true;
    }

    public function getDriverStatistics(int $driverId): DriverComputerStatisticsDTO
    {
        $seasonsPlayed = $this->seasonRepository->count(['driverId' => $driverId]);

        return DriverComputerStatisticsDTO::create($seasonsPlayed);
    }
}

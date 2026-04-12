<?php

declare(strict_types=1);

namespace Computer;

use Computer\Contract\DriverComputerStatisticsDTO;
use Computer\Repository\RaceRepository;
use Computer\Repository\SeasonRepository;

readonly class ComputerFacade implements ComputerFacadeInterface
{
    public function __construct(
        private SeasonRepository $seasonRepository,
        private RaceRepository $raceRepository,
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

    public function canTrackBeSafelyDeleted(int $trackId): bool
    {
        if ($this->raceRepository->count(['trackId' => $trackId]) > 0) {
            return false;
        }

        return true;
    }

    public function getLast12MonthsSeasonPlayed(): array
    {
        return [];
    }
}

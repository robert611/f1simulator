<?php

declare(strict_types=1);

namespace Computer;

use Computer\Contract\DriverComputerStatisticsDTO;

interface ComputerFacadeInterface
{
    /**
     * This method checks if a driver with a given id is a part of any computer season or any other activity
     * in this module, that makes it impossible to delete a driver without breaking database integrity
     */
    public function canDriverBeSafelyDeleted(int $driverId): bool;

    /**
     * This method returns a DTO with driver statistics in played seasons
     */
    public function getDriverStatistics(int $driverId): DriverComputerStatisticsDTO;

    /**
     * This method checks if a track with a given id is a part of any computer race or any other activity
     * in this module, that makes it impossible to delete a track without breaking database integrity
     */
    public function canTrackBeSafelyDeleted(int $trackId): bool;

    public function getLast12MonthsSeasonPlayed(): array;
}

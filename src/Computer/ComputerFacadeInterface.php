<?php

declare(strict_types=1);

namespace Computer;

use Computer\Contract\DriverComputerStatisticsDTO;

interface ComputerFacadeInterface
{
    /**
     * This method checks if a driver with given id is a part of any computer season or any other activity
     * in this module, that makes it impossible to delete a driver without breaking database integrity
     */
    public function canDriverBeSafelyDeleted(int $driverId): bool;

    /**
     * This method returns a DTO with driver statistics in played seasons
     */
    public function getDriverStatistics(int $driverId): DriverComputerStatisticsDTO;
}

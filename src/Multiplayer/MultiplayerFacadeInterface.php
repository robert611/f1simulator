<?php

declare(strict_types=1);

namespace Multiplayer;

use Multiplayer\Contract\DriverMultiplayerStatisticsDTO;

interface MultiplayerFacadeInterface
{
    /**
     * This method checks if a driver with a given id is a part of any multiplayer season or any other activity
     * in this module, that makes it impossible to delete a driver without breaking database integrity
     */
    public function canDriverBeSafelyDeleted(int $driverId): bool;

    /**
     * This method returns a DTO with driver statistics in played multiplayer seasons
     */
    public function getDriverStatistics(int $driverId): DriverMultiplayerStatisticsDTO;

    /**
     * This method checks if a track with a given id is a part of any multiplayer race or any other activity
     * in this module, that makes it impossible to delete a track without breaking database integrity
     */
    public function canTrackBeSafelyDeleted(int $trackId): bool;

    /**
     * Returns multiplayer season played in the last 12 months
     *
     * @return array<array{month: int, seasonsPlayed: int}>
     */
    public function getLast12MonthsSeasonPlayed(): array;
}

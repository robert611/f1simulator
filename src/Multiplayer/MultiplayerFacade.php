<?php

declare(strict_types=1);

namespace Multiplayer;

use Multiplayer\Contract\DriverMultiplayerStatisticsDTO;
use Multiplayer\Repository\UserSeasonPlayersRepository;
use Multiplayer\Repository\UserSeasonRaceRepository;

readonly class MultiplayerFacade implements MultiplayerFacadeInterface
{
    public function __construct(
        private UserSeasonPlayersRepository $userSeasonPlayersRepository,
        private UserSeasonRaceRepository $userSeasonRaceRepository,
    ) {
    }

    public function canDriverBeSafelyDeleted(int $driverId): bool
    {
        if ($this->userSeasonPlayersRepository->count(['driverId' => $driverId]) > 0) {
            return false;
        }

        return true;
    }

    public function getDriverStatistics(int $driverId): DriverMultiplayerStatisticsDTO
    {
        $userSeasonsPlayed = $this->userSeasonPlayersRepository->count(['driverId' => $driverId]);

        return DriverMultiplayerStatisticsDTO::create($userSeasonsPlayed);
    }

    public function canTrackBeSafelyDeleted(int $trackId): bool
    {
        if ($this->userSeasonRaceRepository->count(['trackId' => $trackId]) > 0) {
            return false;
        }

        return true;
    }
}

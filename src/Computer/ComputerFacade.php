<?php

declare(strict_types=1);

namespace Computer;

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
}

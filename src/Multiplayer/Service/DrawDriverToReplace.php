<?php

declare(strict_types=1);

namespace Multiplayer\Service;

use Domain\Contract\DTO\DriverDTO;
use Domain\DomainFacadeInterface;
use Multiplayer\Entity\UserSeason;

class DrawDriverToReplace
{
    public function __construct(
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    public function getDriverToReplaceInUserLeague(UserSeason $league): ?DriverDTO
    {
        $allDrivers = $this->domainFacade->getAllDrivers();

        $takenDriversIds = $league->getLeagueDriversIds();

        $takenDrivers = $this->domainFacade->getDriversByIds($takenDriversIds);

        $availableDrivers = array_udiff(
            $allDrivers,
            $takenDrivers,
            fn (DriverDTO $a, DriverDTO $b) => $a->getId() <=> $b->getId(),
        );

        // Reindex array to make sure it starts from 0
        $availableDrivers = array_values($availableDrivers);

        if (empty($availableDrivers)) {
            return null;
        }

        $randomKey = array_rand($availableDrivers);

        return $availableDrivers[$randomKey];
    }
}

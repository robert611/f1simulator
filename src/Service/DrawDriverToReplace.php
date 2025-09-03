<?php 

declare(strict_types=1);

namespace App\Service;

use App\Entity\Driver;
use App\Entity\UserSeason;
use App\Repository\DriverRepository;

class DrawDriverToReplace
{
    public function __construct(
        private readonly DriverRepository $driverRepository,
    ) {
    }

    public function getDriverToReplaceInUserLeague(UserSeason $league): ?Driver
    {
        $allDrivers = $this->driverRepository->findAll();

        $takenDrivers = $league->getLeagueDrivers();

        $availableDrivers = array_udiff(
            $allDrivers,
            $takenDrivers,
            fn(Driver $a, Driver $b) => $a->getId() <=> $b->getId(),
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

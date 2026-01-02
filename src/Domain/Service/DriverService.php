<?php

declare(strict_types=1);

namespace Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\DriverServiceFacadeInterface;
use Domain\Repository\DriverRepository;
use Domain\Repository\TeamRepository;

readonly class DriverService implements DriverServiceFacadeInterface
{
    public function __construct(
        private DriverRepository $driverRepository,
        private TeamRepository $teamRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function update(int $driverId, string $name, string $surname, int $teamId, int $carNumber): void
    {
        $team = $this->teamRepository->find($teamId);
        $driver = $this->driverRepository->find($driverId);

        $driver->getTeam()->removeDriver($driver);
        $driver->update(
            $name,
            $surname,
            $team,
            $carNumber,
        );
        $team->addDriver($driver);

        $this->entityManager->flush();
    }
}

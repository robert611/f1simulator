<?php

declare(strict_types=1);

namespace Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\DriverServiceFacadeInterface;
use Domain\Entity\Team;
use Domain\Repository\DriverRepository;

readonly class DriverService implements DriverServiceFacadeInterface
{
    public function __construct(
        private DriverRepository $driverRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function update(int $driverId, string $name, string $surname, int $teamId, int $carNumber): void
    {
        $driver = $this->driverRepository->find($driverId);
        $driver->update(
            $name,
            $surname,
            $this->entityManager->getReference(Team::class, $teamId),
            $carNumber,
        );

        $this->entityManager->flush();
    }
}

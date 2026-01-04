<?php

declare(strict_types=1);

namespace Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\DriverServiceFacadeInterface;
use Domain\Contract\Exception\CarNumberTakenException;
use Domain\Contract\Exception\DriverCannotBeDeletedException;
use Domain\Entity\Driver;
use Domain\Repository\DriverRepository;
use Domain\Repository\TeamRepository;
use Multiplayer\MultiplayerFacadeInterface;

readonly class DriverService implements DriverServiceFacadeInterface
{
    public function __construct(
        private DriverRepository $driverRepository,
        private TeamRepository $teamRepository,
        private MultiplayerFacadeInterface $multiplayerFacade,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws CarNumberTakenException
     */
    public function add(string $name, string $surname, int $teamId, int $carNumber): void
    {
        $driversWithCarNumber = $this->driverRepository->count(['carNumber' => $carNumber]);

        if ($driversWithCarNumber > 0) {
            throw new CarNumberTakenException();
        }

        $team = $this->teamRepository->find($teamId);

        $driver = Driver::create($name, $surname, $team, $carNumber);
        $team->addDriver($driver);

        $this->entityManager->persist($driver);
        $this->entityManager->flush();
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

    /**
     * @throws DriverCannotBeDeletedException
     */
    public function delete(int $driverId): void
    {
        if (false === $this->multiplayerFacade->canDriverBeSafelyDeleted($driverId)) {
            throw new DriverCannotBeDeletedException();
        }


    }
}

<?php

declare(strict_types=1);

namespace Domain;

use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\DTO\TeamDTO;
use Domain\Repository\DriverRepository;
use Domain\Repository\TeamRepository;
use Domain\Repository\TrackRepository;

class DomainFacade implements DomainFacadeInterface
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly DriverRepository $driverRepository,
        private readonly TrackRepository $trackRepository,
    ) {
    }

    /**
     * @param int[] $driversIds
     *
     * @return TeamDTO[]
     */
    public function getTeamsByDriversIds(array $driversIds): array
    {
        $teams = $this->teamRepository->getTeamsByDriversIds($driversIds);

        return TeamDTO::fromEntityCollection($teams);
    }

    /**
     * @param int[] $driversIds
     *
     * @return DriverDTO[]
     */
    public function getDriversByIds(array $driversIds): array
    {
        $drivers = $this->driverRepository->getDriversWithTeams($driversIds);

        return DriverDTO::fromEntityCollection($drivers);
    }

    /**
     * @return DriverDTO[]
     */
    public function getAllDrivers(): array
    {
        $drivers = $this->driverRepository->findAllWithTeams();

        return DriverDTO::fromEntityCollection($drivers);
    }

    public function getTracksCount(): int
    {
        return $this->trackRepository->count();
    }
}

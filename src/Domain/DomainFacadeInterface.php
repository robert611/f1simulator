<?php

declare(strict_types=1);

namespace Domain;

use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\DTO\TeamDTO;

interface DomainFacadeInterface
{
    /**
     * Returns a collection of team entity dto based on given drivers ids
     *
     * @param int[] $driversIds
     *
     * @return TeamDTO[]
     */
    public function getTeamsByDriversIds(array $driversIds): array;


    /**
     * Returns a collection of driver entity dto based on given drivers ids
     *
     * @param int[] $driversIds
     *
     * @return DriverDTO[]
     */
    public function getDriversByIds(array $driversIds): array;

    /**
     * @return DriverDTO[]
     */
    public function getAllDrivers(): array;
}

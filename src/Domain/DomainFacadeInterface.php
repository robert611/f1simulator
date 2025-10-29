<?php

declare(strict_types=1);

namespace Domain;

use Domain\Contract\DTO\TeamDTO;

interface DomainFacadeInterface
{
    /**
     * Function returns a collection of team entity dto based on given drivers ids
     *
     * @param int[] $driversIds
     *
     * @return TeamDTO[]
     */
    public function getTeamsByDriversIds(array $driversIds): array;
}

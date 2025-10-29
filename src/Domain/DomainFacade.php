<?php

declare(strict_types=1);

namespace Domain;

use Domain\Contract\DTO\TeamDTO;
use Domain\Repository\TeamRepository;

class DomainFacade implements DomainFacadeInterface
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
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
}

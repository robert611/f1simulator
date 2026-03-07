<?php

declare(strict_types=1);

namespace Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\TrackServiceFacadeInterface;
use Domain\Entity\Track;

final readonly class TrackService implements TrackServiceFacadeInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(string $name, string $picture): void
    {
        $track = Track::create($name, $picture);

        $this->entityManager->persist($track);
        $this->entityManager->flush();
    }
}

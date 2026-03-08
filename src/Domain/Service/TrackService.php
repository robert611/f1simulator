<?php

declare(strict_types=1);

namespace Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\TrackServiceFacadeInterface;
use Domain\Entity\Track;
use Domain\Repository\TrackRepository;

final readonly class TrackService implements TrackServiceFacadeInterface
{
    public function __construct(
        private TrackRepository $trackRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(string $name, string $picture): void
    {
        $track = Track::create($name, $picture);

        $this->entityManager->persist($track);
        $this->entityManager->flush();
    }

    public function update(int $trackId, string $name, ?string $picture): void
    {
        $track = $this->trackRepository->find($trackId);

        $track->update($name, $picture);

        $this->entityManager->flush();
    }
}

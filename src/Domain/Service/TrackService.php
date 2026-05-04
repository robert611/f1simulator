<?php

declare(strict_types=1);

namespace Domain\Service;

use Computer\ComputerFacadeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\Exception\TrackCannotBeDeletedException;
use Domain\Contract\TrackServiceFacadeInterface;
use Domain\Entity\Track;
use Domain\Repository\TrackRepository;
use Multiplayer\MultiplayerFacadeInterface;

final readonly class TrackService implements TrackServiceFacadeInterface
{
    public function __construct(
        private TrackRepository $trackRepository,
        private MultiplayerFacadeInterface $multiplayerFacade,
        private ComputerFacadeInterface $computerFacade,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(string $name, string $picture, string $latitude, string $longitude): void
    {
        $track = Track::create($name, $picture, $latitude, $longitude);

        $this->entityManager->persist($track);
        $this->entityManager->flush();
    }

    public function update(int $trackId, string $name, ?string $picture): void
    {
        $track = $this->trackRepository->find($trackId);

        $track->update($name, $picture);

        $this->entityManager->flush();
    }

    /**
     * @throws TrackCannotBeDeletedException
     */
    public function delete(int $trackId): void
    {
        if (false === $this->multiplayerFacade->canTrackBeSafelyDeleted($trackId)) {
            throw new TrackCannotBeDeletedException();
        }

        if (false === $this->computerFacade->canTrackBeSafelyDeleted($trackId)) {
            throw new TrackCannotBeDeletedException();
        }

        $track = $this->trackRepository->find($trackId);

        $this->entityManager->remove($track);
        $this->entityManager->flush();
    }
}

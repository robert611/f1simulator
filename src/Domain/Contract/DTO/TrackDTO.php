<?php

declare(strict_types=1);

namespace Domain\Contract\DTO;

use Domain\Entity\Track;

class TrackDTO
{
    private int $id;
    private string $name;
    private string $picture;
    private string $latitude;
    private string $longitude;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public static function fromEntity(Track $track): self
    {
        $trackDTO = new TrackDTO();
        $trackDTO->id = $track->getId();
        $trackDTO->name = $track->getName();
        $trackDTO->picture = $track->getPicture();
        $trackDTO->latitude = $track->getLatitude();
        $trackDTO->longitude = $track->getLongitude();

        return $trackDTO;
    }

    /**
     * @param Track[] $tracks
     *
     * @return TrackDTO[]
     */
    public static function fromEntityCollection(array $tracks): array
    {
        $tracksDTO = [];

        foreach ($tracks as $track) {
            $tracksDTO[] = TrackDTO::fromEntity($track);
        }

        return $tracksDTO;
    }
}

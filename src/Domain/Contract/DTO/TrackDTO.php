<?php

declare(strict_types=1);

namespace Domain\Contract\DTO;

use Domain\Entity\Track;

class TrackDTO
{
    private int $id;
    private string $name;
    private string $picture;

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

    public static function fromEntity(Track $track): self
    {
        $trackDTO = new TrackDTO();
        $trackDTO->id = $track->getId();
        $trackDTO->name = $track->getName();
        $trackDTO->picture = $track->getPicture();

        return $trackDTO;
    }
}

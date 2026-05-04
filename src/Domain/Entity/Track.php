<?php

declare(strict_types=1);

namespace Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\Repository\TrackRepository;

#[ORM\Entity(repositoryClass: TrackRepository::class)]
#[ORM\Table(name: 'track')]
class Track
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'picture', type: 'string', length: 255, nullable: false)]
    private string $picture;

    #[ORM\Column(name: 'latitude', type: 'string', length: 64, nullable: false)]
    private string $latitude;

    #[ORM\Column(name: 'longitude', type: 'string', length: 64, nullable: false)]
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

    public static function create(string $name, string $picture, string $latitude, string $longitude): self
    {
        $track = new self();
        $track->name = $name;
        $track->picture = $picture;
        $track->latitude = $latitude;
        $track->longitude = $longitude;

        return $track;
    }

    public function update(string $name, ?string $picture): void
    {
        $this->name = $name;

        if ($picture) {
            $this->picture = $picture;
        }
    }
}

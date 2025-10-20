<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\Repository\TrackRepository;
use App\Entity\Race;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\OneToMany(targetEntity: Race::class, mappedBy: 'track')]
    private Collection $races;

    public function __construct()
    {
        $this->races = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return Collection<Race>
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRace(Race $race): void
    {
        if (!$this->races->contains($race)) {
            $this->races[] = $race;
            $race->setTrack($this);
        }
    }

    public function removeRace(Race $race): void
    {
        if ($this->races->contains($race)) {
            $this->races->removeElement($race);
        }
    }

    public static function create(string $name, string $picture): self
    {
        $track = new self();
        $track->name = $name;
        $track->picture = $picture;

        return $track;
    }
}

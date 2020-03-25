<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    public $picture;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Driver", mappedBy="team", orphanRemoval=true)
     */
    public $drivers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Season", mappedBy="team")
     */
    public $seasons;

    public $points;

    public $position;

    public function __construct()
    {
        $this->car_id = new ArrayCollection();
        $this->seasons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|Driver[]
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): self
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers[] = $driver;
            $driver->setTeam($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): self
    {
        if ($this->driver->contains($driver)) {
            $this->driver->removeElement($driver);
            // set the owning side to null (unless already changed)
            if ($driver->getTeam() === $this) {
                $driver->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Season[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setTeamId($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->contains($season)) {
            $this->seasons->removeElement($season);
            // set the owning side to null (unless already changed)
            if ($season->getTeamId() === $this) {
                $season->setTeamId(null);
            }
        }

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points ? $this->points : 0;
    }

    public function setPoints(string $points)
    {
        $this->points = $points;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition(string $position)
    {
        $this->position = $position;

        return $this;
    }
}

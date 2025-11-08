<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'team')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    public int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 64, nullable: false)]
    public string $name;

    #[ORM\Column(name: 'picture', type: 'string', length: 64, nullable: false)]
    public string $picture;

    #[ORM\OneToMany(targetEntity: Driver::class, mappedBy: 'team', orphanRemoval: true)]
    public Collection $drivers;

    public function __construct()
    {
        $this->drivers = new ArrayCollection();
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
     * @return Collection<Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): void
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers[] = $driver;
            $driver->setTeam($this);
        }
    }

    public function removeDriver(Driver $driver): void
    {
        if ($this->drivers->contains($driver)) {
            $this->drivers->removeElement($driver);
        }
    }

    public static function create(string $name, string $picture): self
    {
        $team = new self();
        $team->name = $name;
        $team->picture = $picture;

        return $team;
    }

    public function drawDriverToReplace(): ?Driver
    {
        /** @var Driver[] $drivers */
        $drivers = $this->getDrivers()->toArray();

        return self::drawDriverToReplaceMethod($drivers);
    }

    /**
     * @param Driver[] $drivers
     */
    public static function drawDriverToReplaceMethod(array $drivers): ?Driver
    {
        if (empty($drivers)) {
            return null;
        }

        // Reindex array to make sure it starts from 0
        $drivers = array_values($drivers);

        $randomKey = array_rand($drivers);

        return $drivers[$randomKey];
    }
}

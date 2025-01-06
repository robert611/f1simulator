<?php

namespace App\Entity;

use App\Repository\UserSeasonRaceResultsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSeasonRaceResultsRepository::class)]
class UserSeasonRaceResults
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'smallint')]
    private int $position;

    #[ORM\ManyToOne(targetEntity: UserSeasonRaces::class, inversedBy: 'raceResults')]
    #[ORM\JoinColumn(nullable: false)]
    private UserSeasonRaces $race;

    #[ORM\ManyToOne(targetEntity: UserSeasonPlayers::class, inversedBy: 'raceResults')]
    #[ORM\JoinColumn(nullable: false)]
    private UserSeasonPlayers $player;

    public $points;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getRace(): UserSeasonRaces
    {
        return $this->race;
    }

    public function setRace(UserSeasonRaces $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getPlayer(): ?UserSeasonPlayers
    {
        return $this->player;
    }

    public function setPlayer(UserSeasonPlayers $player): self
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get the value of points
     */ 
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set the value of points
     *
     * @return  self
     */ 
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }
}

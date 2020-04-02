<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserSeasonRaceResultsRepository")
 */
class UserSeasonRaceResults
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserSeasonRaces", inversedBy="raceResults")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserSeasonPlayers", inversedBy="raceResults")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getRace(): ?UserSeasonRaces
    {
        return $this->race;
    }

    public function setRace(?UserSeasonRaces $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getPlayer(): ?UserSeasonPlayers
    {
        return $this->player;
    }

    public function setPlayer(?UserSeasonPlayers $player): self
    {
        $this->player = $player;

        return $this;
    }
}

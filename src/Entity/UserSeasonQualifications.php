<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserSeasonQualificationsRepository")
 */
class UserSeasonQualifications
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserSeasonPlayers", inversedBy="qualificationsResults")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserSeasonRaces", inversedBy="qualifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRace(): ?UserSeasonRaces
    {
        return $this->race;
    }

    public function setRace(?UserSeasonRaces $race): self
    {
        $this->race = $race;

        return $this;
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
}

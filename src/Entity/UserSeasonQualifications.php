<?php

namespace App\Entity;

use App\Repository\UserSeasonQualificationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSeasonQualificationsRepository::class)]
class UserSeasonQualifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserSeasonPlayers::class, inversedBy: 'qualificationsResults')]
    #[ORM\JoinColumn(nullable: false)]
    private UserSeasonPlayers $player;

    #[ORM\ManyToOne(targetEntity: UserSeasonRaces::class, inversedBy: 'qualifications')]
    #[ORM\JoinColumn(nullable: false)]
    private UserSeasonRaces $race;

    #[ORM\Column(type: 'smallint', nullable: false)]
    private int $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): UserSeasonPlayers
    {
        return $this->player;
    }

    public function setPlayer(UserSeasonPlayers $player): self
    {
        $this->player = $player;

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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}

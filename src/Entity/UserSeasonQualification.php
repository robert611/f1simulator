<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserSeasonQualificationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSeasonQualificationsRepository::class)]
#[ORM\Table(name: 'user_season_qualification')]
class UserSeasonQualification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserSeasonPlayer::class, inversedBy: 'qualificationsResults')]
    #[ORM\JoinColumn(name: 'player_id', nullable: false)]
    private UserSeasonPlayer $player;

    #[ORM\ManyToOne(targetEntity: UserSeasonRace::class, inversedBy: 'qualifications')]
    #[ORM\JoinColumn(name: 'race_id', nullable: false)]
    private UserSeasonRace $race;

    #[ORM\Column(name: 'position', type: 'smallint', nullable: false)]
    private int $position;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlayer(): UserSeasonPlayer
    {
        return $this->player;
    }

    public function setPlayer(UserSeasonPlayer $player): void
    {
        $this->player = $player;
    }

    public function getRace(): UserSeasonRace
    {
        return $this->race;
    }

    public function setRace(UserSeasonRace $race): void
    {
        $this->race = $race;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}

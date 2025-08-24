<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserSeasonRaceResultsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSeasonRaceResultsRepository::class)]
#[ORM\Table(name: 'user_season_race_result')]
class UserSeasonRaceResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'position', type: 'smallint')]
    private int $position;

    #[ORM\ManyToOne(targetEntity: UserSeasonRace::class, inversedBy: 'raceResults')]
    #[ORM\JoinColumn(name: 'race_id', nullable: false)]
    private UserSeasonRace $race;

    #[ORM\ManyToOne(targetEntity: UserSeasonPlayer::class, inversedBy: 'raceResults')]
    #[ORM\JoinColumn(name: 'player_id', nullable: false)]
    private UserSeasonPlayer $player;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getRace(): UserSeasonRace
    {
        return $this->race;
    }

    public function setRace(UserSeasonRace $race): void
    {
        $this->race = $race;
    }

    public function getPlayer(): UserSeasonPlayer
    {
        return $this->player;
    }

    public function setPlayer(UserSeasonPlayer $player): void
    {
        $this->player = $player;
    }

    public static function create(int $position, UserSeasonRace $race, UserSeasonPlayer $player): self
    {
        $userSeasonRaceResult = new self();
        $userSeasonRaceResult->position = $position;
        $userSeasonRaceResult->race = $race;
        $userSeasonRaceResult->player = $player;

        return $userSeasonRaceResult;
    }
}

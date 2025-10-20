<?php

declare(strict_types=1);

namespace Multiplayer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Multiplayer\Repository\UserSeasonRaceResultsRepository;

#[ORM\Entity(repositoryClass: UserSeasonRaceResultsRepository::class)]
#[ORM\Table(name: 'user_season_race_result')]
class UserSeasonRaceResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'position', type: 'smallint', nullable: false)]
    private int $position;

    #[ORM\Column(name: 'points', type: 'smallint', nullable: false)]
    private int $points;

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

    public function getPoints(): int
    {
        return $this->points;
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

    public static function create(int $position, int $points, UserSeasonRace $race, UserSeasonPlayer $player): self
    {
        $userSeasonRaceResult = new self();
        $userSeasonRaceResult->position = $position;
        $userSeasonRaceResult->points = $points;
        $userSeasonRaceResult->race = $race;
        $userSeasonRaceResult->player = $player;

        return $userSeasonRaceResult;
    }
}

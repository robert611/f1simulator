<?php

declare(strict_types=1);

namespace Multiplayer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Multiplayer\Repository\UserSeasonRaceRepository;

#[ORM\Entity(repositoryClass: UserSeasonRaceRepository::class)]
#[ORM\Table(name: 'user_season_race')]
class UserSeasonRace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'track_id', type: 'integer', nullable: false)]
    private int $trackId;

    #[ORM\ManyToOne(targetEntity: UserSeason::class, inversedBy: 'races')]
    #[ORM\JoinColumn(name: 'season_id', nullable: false)]
    private UserSeason $season;

    #[ORM\OneToMany(targetEntity: UserSeasonRaceResult::class, mappedBy: 'race', orphanRemoval: true)]
    private Collection $raceResults;

    #[ORM\OneToMany(targetEntity: UserSeasonQualification::class, mappedBy: 'race', orphanRemoval: true)]
    private Collection $qualifications;

    public function __construct()
    {
        $this->raceResults = new ArrayCollection();
        $this->qualifications = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTrackId(): int
    {
        return $this->trackId;
    }

    public function getSeason(): UserSeason
    {
        return $this->season;
    }

    public function setSeason(UserSeason $season): void
    {
        $this->season = $season;
    }

    /**
     * @return Collection<UserSeasonRaceResult>
     */
    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResult(UserSeasonRaceResult $raceResult): void
    {
        if (!$this->raceResults->contains($raceResult)) {
            $this->raceResults[] = $raceResult;
            $raceResult->setRace($this);
        }
    }

    public function removeRaceResult(UserSeasonRaceResult $raceResult): void
    {
        if ($this->raceResults->contains($raceResult)) {
            $this->raceResults->removeElement($raceResult);
        }
    }

    /**
     * @return Collection<UserSeasonQualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }

    public function addQualification(UserSeasonQualification $qualification): void
    {
        if (!$this->qualifications->contains($qualification)) {
            $this->qualifications[] = $qualification;
            $qualification->setRace($this);
        }
    }

    public function removeQualification(UserSeasonQualification $qualification): void
    {
        if ($this->qualifications->contains($qualification)) {
            $this->qualifications->removeElement($qualification);
        }
    }

    public static function create(int $trackId, UserSeason $userSeason): self
    {
        $userSeasonRace = new self();
        $userSeasonRace->trackId = $trackId;
        $userSeasonRace->season = $userSeason;

        return $userSeasonRace;
    }
}

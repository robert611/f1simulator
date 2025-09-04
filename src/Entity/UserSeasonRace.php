<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserSeasonRaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSeasonRaceRepository::class)]
#[ORM\Table(name: 'user_season_race')]
class UserSeasonRace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Track::class)]
    #[ORM\JoinColumn(name: 'track_id', nullable: false)]
    private Track $track;

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

    public function getTrack(): Track
    {
        return $this->track;
    }

    public function setTrack(Track $track): void
    {
        $this->track = $track;
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

    public static function create(Track $track, UserSeason $userSeason): self
    {
        $userSeasonRace = new self();
        $userSeasonRace->track = $track;
        $userSeasonRace->season = $userSeason;

        return $userSeasonRace;
    }
}

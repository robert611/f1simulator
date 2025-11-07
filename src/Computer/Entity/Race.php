<?php

declare(strict_types=1);

namespace Computer\Entity;

use Computer\Repository\RaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
#[ORM\Table(name: 'race')]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'track_id', type: 'integer', nullable: false)]
    private int $trackId;

    #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'races')]
    #[ORM\JoinColumn(name: 'season_id', nullable: false)]
    private Season $season;

    #[ORM\OneToMany(targetEntity: RaceResult::class, mappedBy: 'race')]
    private Collection $raceResults;

    #[ORM\OneToMany(targetEntity: Qualification::class, mappedBy: 'race', orphanRemoval: true)]
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

    public function setTrackId(int $trackId): void
    {
        $this->trackId = $trackId;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): void
    {
        $this->season = $season;
    }

    /**
     * @return Collection<RaceResult>
     */
    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResult(RaceResult $raceResult): void
    {
        if (!$this->raceResults->contains($raceResult)) {
            $this->raceResults[] = $raceResult;
            $raceResult->setRace($this);
        }
    }

    public function removeRaceResult(RaceResult $raceResult): void
    {
        if ($this->raceResults->contains($raceResult)) {
            $this->raceResults->removeElement($raceResult);
        }
    }

    /**
     * @return Collection<Qualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }

    public function addQualification(Qualification $qualification): void
    {
        if (!$this->qualifications->contains($qualification)) {
            $this->qualifications[] = $qualification;
            $qualification->setRace($this);
        }
    }

    public function removeQualification(Qualification $qualification): void
    {
        if ($this->qualifications->contains($qualification)) {
            $this->qualifications->removeElement($qualification);
        }
    }

    public static function create(int $trackId, Season $season): self
    {
        $race = new self();
        $race->trackId = $trackId;
        $race->season = $season;

        return $race;
    }
}

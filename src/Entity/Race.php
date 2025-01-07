<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Track::class, inversedBy: 'races')]
    #[ORM\JoinColumn(name: 'track_id', nullable: false)]
    private Track $track;

    #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'races')]
    #[ORM\JoinColumn(name: 'season_id', nullable: false)]
    private Season $season;

    #[ORM\OneToMany(targetEntity: RaceResults::class, mappedBy: 'race')]
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

    public function getTrack(): Track
    {
        return $this->track;
    }

    public function setTrack(Track $track): self
    {
        $this->track = $track;

        return $this;
    }

    public function getSeason(): season
    {
        return $this->season;
    }

    public function setSeason(season $season): self
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return Collection<RaceResults>
     */
    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResult(RaceResults $raceResult): self
    {
        if (!$this->raceResults->contains($raceResult)) {
            $this->raceResults[] = $raceResult;
            $raceResult->setRace($this);
        }

        return $this;
    }

    public function removeRaceResult(RaceResults $raceResult): self
    {
        if ($this->raceResults->contains($raceResult)) {
            $this->raceResults->removeElement($raceResult);
        }

        return $this;
    }

    /**
     * @return Collection<Qualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }

    public function addQualification(Qualification $qualification): self
    {
        if (!$this->qualifications->contains($qualification)) {
            $this->qualifications[] = $qualification;
            $qualification->setRace($this);
        }

        return $this;
    }

    public function removeQualification(Qualification $qualification): self
    {
        if ($this->qualifications->contains($qualification)) {
            $this->qualifications->removeElement($qualification);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserSeasonPlayersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSeasonPlayersRepository::class)]
class UserSeasonPlayers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserSeason::class, inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private UserSeason $season;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userSeasonPlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Driver $driver;

    #[ORM\OneToMany(targetEntity: UserSeasonRaceResults::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $raceResults;

    #[ORM\OneToMany(targetEntity: UserSeasonQualifications::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $qualificationsResults;

    public $points;

    public $position;

    public function __construct()
    {
        $this->raceResults = new ArrayCollection();
        $this->qualificationsResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeason(): ?UserSeason
    {
        return $this->season;
    }

    public function setSeason(?UserSeason $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return Collection|UserSeasonRaceResults[]
     */
    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResult(UserSeasonRaceResults $raceResult): self
    {
        if (!$this->raceResults->contains($raceResult)) {
            $this->raceResults[] = $raceResult;
            $raceResult->setPlayer($this);
        }

        return $this;
    }

    public function removeRaceResult(UserSeasonRaceResults $raceResult): self
    {
        if ($this->raceResults->contains($raceResult)) {
            $this->raceResults->removeElement($raceResult);
            // set the owning side to null (unless already changed)
            if ($raceResult->getPlayer() === $this) {
                $raceResult->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserSeasonQualifications[]
     */
    public function getQualificationsResults(): Collection
    {
        return $this->qualificationsResults;
    }

    public function addQualificationsResult(UserSeasonQualifications $qualificationsResult): self
    {
        if (!$this->qualificationsResults->contains($qualificationsResult)) {
            $this->qualificationsResults[] = $qualificationsResult;
            $qualificationsResult->setPlayer($this);
        }

        return $this;
    }

    public function removeQualificationsResult(UserSeasonQualifications $qualificationsResult): self
    {
        if ($this->qualificationsResults->contains($qualificationsResult)) {
            $this->qualificationsResults->removeElement($qualificationsResult);
            // set the owning side to null (unless already changed)
            if ($qualificationsResult->getPlayer() === $this) {
                $qualificationsResult->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of points
     */ 
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set the value of points
     *
     * @return  self
     */ 
    public function setPoints($points): static
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get the value of position
     */ 
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @return  self
     */ 
    public function setPosition($position): static
    {
        $this->position = $position;

        return $this;
    }
}

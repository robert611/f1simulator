<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SeasonRepository")
 */
class Season
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\team", inversedBy="seasons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\Column(type="integer")
     */
    private $car_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="seasons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Race", mappedBy="season", orphanRemoval=true)
     */
    private $races;

    public function __construct()
    {
        $this->races = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?team
    {
        return $this->team;
    }

    public function setTeam(?team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getCarId(): ?int
    {
        return $this->car_id;
    }

    public function setCarId(int $car_id): self
    {
        $this->car_id = $car_id;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return Collection|Race[]
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRaces(Race $races): self
    {
        if (!$this->races->contains($races)) {
            $this->races[] = $races;
            $races->setSeason($this);
        }

        return $this;
    }

    public function removeRaces(Race $races): self
    {
        if ($this->races->contains($races)) {
            $this->races->removeElement($races);
            // set the owning side to null (unless already changed)
            if ($races->getSeason() === $this) {
                $races->setSeason(null);
            }
        }

        return $this;
    }
}

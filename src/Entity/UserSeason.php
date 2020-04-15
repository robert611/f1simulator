<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserSeasonRepository")
 * @UniqueEntity(fields={"name"}, message="Istnieje już liga z taką nazwą")
 */
class UserSeason
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $secret;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\Range(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Minimalna liczba graczy to 2",
     *      maxMessage = "Maksymalna liczba graczy to 20"
     * )
     */
    private $max_players;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userSeasons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSeasonPlayers", mappedBy="season", orphanRemoval=true)
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSeasonRaces", mappedBy="season", orphanRemoval=true)
     */
    private $races;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $started;

    public function __construct()
    {
        $this->userSeasonPlayers = new ArrayCollection();
        $this->userSeasonRaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->max_players;
    }

    public function setMaxPlayers(int $max_players): self
    {
        $this->max_players = $max_players;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|UserSeasonPlayers[]
     */
    public function getPlayers(): ?Collection
    {
        return $this->players;
    }

    public function addPlayer(UserSeasonPlayers $userSeasonPlayer): self
    {
        if (!$this->userSeasonPlayers->contains($userSeasonPlayer)) {
            $this->userSeasonPlayers[] = $userSeasonPlayer;
            $userSeasonPlayer->setSeason($this);
        }

        return $this;
    }

    public function removePlayer(UserSeasonPlayers $userSeasonPlayer): self
    {
        if ($this->userSeasonPlayers->contains($userSeasonPlayer)) {
            $this->userSeasonPlayers->removeElement($userSeasonPlayer);
            // set the owning side to null (unless already changed)
            if ($userSeasonPlayer->getSeason() === $this) {
                $userSeasonPlayer->setSeason(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserSeasonRaces[]
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRace(UserSeasonRaces $userSeasonRace): self
    {
        if (!$this->races->contains($userSeasonRace)) {
            $this->races[] = $userSeasonRace;
            $userSeasonRace->setUserSeason($this);
        }

        return $this;
    }

    public function removeUserSeasonRace(UserSeasonRaces $userSeasonRace): self
    {
        if ($this->userSeasonRaces->contains($userSeasonRace)) {
            $this->userSeasonRaces->removeElement($userSeasonRace);
            // set the owning side to null (unless already changed)
            if ($userSeasonRace->getUserSeason() === $this) {
                $userSeasonRace->setUserSeason(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getStarted(): ?bool
    {
        return $this->started;
    }

    public function setStarted(?bool $started): self
    {
        $this->started = $started;

        return $this;
    }
}

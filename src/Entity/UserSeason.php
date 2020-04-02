<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserSeasonRepository")
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
     * @ORM\Column(type="string", length=255)
     */
    private $secret;

    /**
     * @ORM\Column(type="smallint")
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
    private $userSeasonPlayers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSeasonRaces", mappedBy="userSeason", orphanRemoval=true)
     */
    private $userSeasonRaces;

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
    public function getUserSeasonPlayers(): Collection
    {
        return $this->userSeasonPlayers;
    }

    public function addUserSeasonPlayer(UserSeasonPlayers $userSeasonPlayer): self
    {
        if (!$this->userSeasonPlayers->contains($userSeasonPlayer)) {
            $this->userSeasonPlayers[] = $userSeasonPlayer;
            $userSeasonPlayer->setSeason($this);
        }

        return $this;
    }

    public function removeUserSeasonPlayer(UserSeasonPlayers $userSeasonPlayer): self
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
    public function getUserSeasonRaces(): Collection
    {
        return $this->userSeasonRaces;
    }

    public function addUserSeasonRace(UserSeasonRaces $userSeasonRace): self
    {
        if (!$this->userSeasonRaces->contains($userSeasonRace)) {
            $this->userSeasonRaces[] = $userSeasonRace;
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
}

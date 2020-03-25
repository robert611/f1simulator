<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Model\DriverPoints;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DriverRepository")
 */
class Driver
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $surname;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="car_id")
     * @ORM\JoinColumn(nullable=false)
     */
    public $team;

    /**
     * @ORM\Column(type="integer")
     */
    public $car_id;

    public $points;

    public $position;

    public $isUser;

    public function __construct()
    {
        $this->raceResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
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

    public function getPoints(): int
    {
        return $this->points ? $this->points : 0;
    }

    public function setPoints(string $points)
    {
        $this->points = $points;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(string $position)
    {
        $this->position = $position;

        return $this;
    }
}

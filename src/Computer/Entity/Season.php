<?php

declare(strict_types=1);

namespace Computer\Entity;

use Computer\Repository\SeasonRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Domain\Contract\Model\DriverPodiumsDictionary;
use Domain\Contract\Model\DriverPodiumsDTO;
use Security\Entity\User;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[ORM\Table(name: 'season')]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'driver_id', type: 'integer', nullable: false)]
    private int $driverId;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'seasons')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private User $user;

    #[ORM\Column(name: 'completed', type: 'boolean', nullable: false)]
    private bool $completed;

    #[ORM\Column(name: 'completed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $completedAt;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: Race::class, mappedBy: 'season')]
    private Collection $races;

    public function __construct()
    {
        $this->races = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<Race>
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function getLastRace(): ?Race
    {
        return $this->races->last();
    }

    public function addRaces(Race $races): void
    {
        if (!$this->races->contains($races)) {
            $this->races[] = $races;
            $races->setSeason($this);
        }
    }

    public function removeRaces(Race $races): void
    {
        if ($this->races->contains($races)) {
            $this->races->removeElement($races);
        }
    }

    public function addRace(Race $race): void
    {
        if (!$this->races->contains($race)) {
            $this->races[] = $race;
            $race->setSeason($this);
        }
    }

    public function removeRace(Race $race): void
    {
        if ($this->races->contains($race)) {
            $this->races->removeElement($race);
        }
    }

    public function getDriverId(): int
    {
        return $this->driverId;
    }

    public function setDriverId(int $driverId): void
    {
        $this->driverId = $driverId;
    }

    public static function create(User $user, int $driverId): self
    {
        $season = new self();
        $season->user = $user;
        $season->driverId = $driverId;
        $season->completed = false;
        $season->completedAt = null;
        $season->createdAt = new DateTimeImmutable();
        $season->updatedAt = new DateTimeImmutable();

        return $season;
    }

    public function endSeason(): void
    {
        $this->completed = true;
        $this->completedAt = new DateTimeImmutable();
    }

    public function getDriverPodiumsDTO(): DriverPodiumsDTO
    {
        $races = $this->getRaces();

        $podiumsTable = DriverPodiumsDictionary::getPodiumsTable();

        $driverId = $this->getDriverId();

        foreach ($races as $race) {
            $raceResultCollection = $race->getRaceResults()->filter(function (RaceResult $result) use ($driverId) {
                return $result->getDriverId() === $driverId;
            });

            if ($raceResultCollection->isEmpty()) {
                continue;
            }

            $position = $raceResultCollection->first()->getPosition();

            if ($position >= 1 && $position <= 3) {
                $podiumsTable[$position] += 1;
            }
        }

        return DriverPodiumsDTO::create(
            $podiumsTable[1],
            $podiumsTable[2],
            $podiumsTable[3],
        );
    }
}

<?php

declare(strict_types=1);

namespace Computer\Contract;

use Computer\Entity\Season;
use DateTimeImmutable;
use Security\Entity\User;

final class SeasonDTO
{
    private int $id;
    private int $driverId;
    private User $user;
    private ?int $position;
    private bool $completed;
    private ?DateTimeImmutable $completedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDriverId(): int
    {
        return $this->driverId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
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

    public static function fromEntity(Season $season): SeasonDTO
    {
        $dto = new SeasonDTO();
        $dto->id = $season->getId();
        $dto->driverId = $season->getDriverId();
        $dto->user = $season->getUser();
        $dto->position = $season->getPosition();
        $dto->completed = $season->getCompleted();
        $dto->completedAt = $season->getCompletedAt();
        $dto->createdAt = $season->getCreatedAt();
        $dto->updatedAt = $season->getUpdatedAt();

        return $dto;
    }

    /**
     * @param Season[] $seasons
     *
     * @return SeasonDTO[]
     */
    public static function fromEntityCollection(array $seasons): array
    {
        $result = [];

        foreach ($seasons as $season) {
            $result[] = SeasonDTO::fromEntity($season);
        }

        return $result;
    }
}

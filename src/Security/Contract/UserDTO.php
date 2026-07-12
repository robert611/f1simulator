<?php

declare(strict_types=1);

namespace Security\Contract;

use DateTimeImmutable;
use Security\Entity\User;
use Security\Entity\UserCountry;

final class UserDTO
{
    private int $id;
    private string $username;
    private string $email;
    private array $roles = [];
    private bool $isVerified;
    private UserCountry $country;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRolesAsString(): string
    {
        return implode(', ', $this->roles);
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function getCountry(): UserCountry
    {
        return $this->country;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function fromEntity(User $user): self
    {
        $userDTO = new self();
        $userDTO->id = $user->getId();
        $userDTO->username = $user->getUsername();
        $userDTO->email = $user->getEmail();
        $userDTO->roles = $user->getRoles();
        $userDTO->isVerified = $user->isVerified();
        $userDTO->country = $user->getCountry();
        $userDTO->createdAt = $user->getCreatedAt();
        $userDTO->updatedAt = $user->getUpdatedAt();

        return $userDTO;
    }

    /**
     * @param User[] $users
     *
     * @return UserDTO[]
     */
    public static function fromEntityCollection(array $users): array
    {
        $usersDTO = [];

        foreach ($users as $user) {
            $usersDTO[] = UserDTO::fromEntity($user);
        }

        return $usersDTO;
    }
}

<?php

declare(strict_types=1);

namespace Security\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Security\Repository\UserConfirmationTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserConfirmationTokenRepository::class)]
#[ORM\Table(name: 'user_confirmation_token')]
class UserConfirmationToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'seasons')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private User $user;

    #[ORM\Column(name: 'token', type: 'string', length: 180, unique: true, nullable: false)]
    private string $token;

    #[ORM\Column(name: 'is_valid', type: 'boolean', nullable: false)]
    private bool $isValid;

    #[ORM\Column(name: 'expiry_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $expiryAt;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getExpiryAt(): DateTimeImmutable
    {
        return $this->expiryAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function create(
        User $user,
        string $token,
        DateTimeImmutable $expiryAt,
    ): self {
        $userConfirmationToken = new self();
        $userConfirmationToken->user = $user;
        $userConfirmationToken->token = $token;
        $userConfirmationToken->isValid = true;
        $userConfirmationToken->expiryAt = $expiryAt;
        $userConfirmationToken->createdAt = new DateTimeImmutable();
        $userConfirmationToken->updatedAt = new DateTimeImmutable();

        return $userConfirmationToken;
    }

    public function isValidAndNotExpired(): bool
    {
        if (false === $this->isValid()) {
            return false;
        }

        if ($this->expiryAt < new DateTimeImmutable()) {
            return false;
        }

        return true;
    }

    public function invalidate(): void
    {
        $this->isValid = false;
    }

    public function overwriteCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

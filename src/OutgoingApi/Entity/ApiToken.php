<?php

declare(strict_types=1);

namespace OutgoingApi\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OutgoingApi\Repository\ApiTokenRepository;
use Security\Entity\User;
use SensitiveParameter;

/**
 * Only the SHA-256 hash of the token is stored. The plain token is shown to the user once, right after generation.
 */
#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
#[ORM\Table(name: 'api_token')]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $id;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, unique: true, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(name: 'token_hash', type: 'string', length: 64, unique: true, nullable: false)]
    private string $tokenHash;

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

    public function getTokenHash(): string
    {
        return $this->tokenHash;
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
     * A fast hash is sufficient (and allows an indexed lookup) because the token is long and random,
     * unlike a user-chosen password, so it cannot be brute-forced.
     */
    public static function hash(#[SensitiveParameter] string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    public static function create(User $user, #[SensitiveParameter] string $plainToken): self
    {
        $apiToken = new self();
        $apiToken->user = $user;
        $apiToken->tokenHash = self::hash($plainToken);
        $apiToken->createdAt = new DateTimeImmutable();
        $apiToken->updatedAt = new DateTimeImmutable();

        return $apiToken;
    }

    public function regenerate(#[SensitiveParameter] string $plainToken): void
    {
        $this->tokenHash = self::hash($plainToken);
        $this->updatedAt = new DateTimeImmutable();
    }
}

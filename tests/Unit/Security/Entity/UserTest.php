<?php

declare(strict_types=1);

namespace Tests\Unit\Security\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\User;

class UserTest extends TestCase
{
    #[Test]
    public function user_entity_can_be_created(): void
    {
        // given
        $user = new User();

        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-02 12:00:00');

        // when
        $user->setUsername('john_doe');
        $user->setEmail('john@example.com');
        $user->setPassword('hashed_password');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsVerified(true);
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($updatedAt);

        // then
        self::assertSame('john_doe', $user->getUsername());
        self::assertSame('john@example.com', $user->getEmail());
        self::assertSame('hashed_password', $user->getPassword());
        self::assertTrue($user->isVerified());
        self::assertSame($createdAt, $user->getCreatedAt());
        self::assertSame($updatedAt, $user->getUpdatedAt());

        // and then (ROLE_USER should be added by default)
        self::assertContains('ROLE_ADMIN', $user->getRoles());
        self::assertContains('ROLE_USER', $user->getRoles());
    }
}

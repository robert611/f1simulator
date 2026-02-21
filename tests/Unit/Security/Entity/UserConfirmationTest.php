<?php

declare(strict_types=1);

namespace Tests\Unit\Security\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\User;
use Security\Entity\UserConfirmation;
use Shared\Service\TokenGenerator;

class UserConfirmationTest extends TestCase
{
    #[Test]
    public function user_confirmation_can_be_created(): void
    {
        // given
        $user = new User();
        $token = TokenGenerator::bin2hex(24);
        $expiryAt = new DateTimeImmutable('+1 hour');

        // when
        $confirmation = UserConfirmation::create(
            $user,
            $token,
            $expiryAt
        );

        // then
        self::assertSame($user, $confirmation->getUser());
        self::assertSame($token, $confirmation->getToken());
        self::assertTrue($confirmation->isValid());
        self::assertSame($expiryAt, $confirmation->getExpiryAt());
        self::assertInstanceOf(DateTimeImmutable::class, $confirmation->getCreatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $confirmation->getUpdatedAt());
    }
}

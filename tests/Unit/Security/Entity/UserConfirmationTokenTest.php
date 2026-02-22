<?php

declare(strict_types=1);

namespace Tests\Unit\Security\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\User;
use Security\Entity\UserConfirmationToken;
use Shared\Service\TokenGenerator;

class UserConfirmationTokenTest extends TestCase
{
    #[Test]
    public function user_confirmation_token_can_be_created(): void
    {
        // given
        $user = new User();
        $token = TokenGenerator::bin2hex(24);
        $expiryAt = new DateTimeImmutable('+1 hour');

        // when
        $confirmationToken = UserConfirmationToken::create(
            $user,
            $token,
            $expiryAt
        );

        // then
        self::assertSame($user, $confirmationToken->getUser());
        self::assertSame($token, $confirmationToken->getToken());
        self::assertTrue($confirmationToken->isValid());
        self::assertSame($expiryAt, $confirmationToken->getExpiryAt());
        self::assertInstanceOf(DateTimeImmutable::class, $confirmationToken->getCreatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $confirmationToken->getUpdatedAt());
    }
}

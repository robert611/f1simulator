<?php

declare(strict_types=1);

namespace Tests\Integration\Security\Service;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Security\Service\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

final class UserConfirmationServiceTest extends KernelTestCase
{
    private UserConfirmationService $userConfirmationService;
    private Fixtures $fixtures;

    protected function setUp(): void
    {
        $this->userConfirmationService = self::getContainer()->get(UserConfirmationService::class);
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function user_is_allowed_to_resend_email_without_previous_tokens(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // when
        $result = $this->userConfirmationService->isUserAllowedToResendEmail($user->getId());

        // then
        self::assertTrue($result);
    }

    #[Test]
    public function user_is_allowed_to_resend_email_after_two_minutes(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $token = $this->fixtures->aUserConfirmationToken(
            $user,
            'test-token-123',
            new DateTimeImmutable('+1 hour'),
        );
        $token->overwriteCreatedAt(new DateTimeImmutable('-2 minutes'));

        // when
        $result = $this->userConfirmationService->isUserAllowedToResendEmail($user->getId());

        // then
        self::assertTrue($result);
    }

    #[Test]
    public function test_isUserAllowedToResendEmail_should_return_false_when_user_has_token_created_less_than_minute_ago(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->fixtures->aUserConfirmationToken(
            $user,
            'test-token-456',
            new DateTimeImmutable('+1 hour'),
        );

        // when
        $result = $this->userConfirmationService->isUserAllowedToResendEmail($user->getId());

        // then
        self::assertFalse($result);
    }
}
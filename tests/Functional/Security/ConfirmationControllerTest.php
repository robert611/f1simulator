<?php

declare(strict_types=1);

namespace Tests\Functional\Security;

use DateTimeImmutable;
use Mailer\AsyncCommand\SendEmail;
use Security\Repository\UserConfirmationTokenRepository;
use Security\Repository\UserRepository;
use Shared\Service\TokenGenerator;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfirmationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserRepository $userRepository;
    private UserConfirmationTokenRepository $userConfirmationTokenRepository;
    private InMemoryTransport $inMemoryTransport;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->userConfirmationTokenRepository = self::getContainer()->get(UserConfirmationTokenRepository::class);
        $this->inMemoryTransport = self::getContainer()->get('messenger.transport.async');
    }

    #[Test]
    public function token_must_exist_to_confirm_account(): void
    {
        // given
        $nonExistingToken = 'not_existing_token';

        // when
        $this->client->request('GET', "/confirm-email/$nonExistingToken");

        // then
        self::assertResponseRedirects('/login');

        // and then (warning flash message is set)
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Link potwierdzający jest nieprawidłowy');
    }

    #[Test]
    public function token_must_be_valid_to_confirm_account(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $userConfirmationToken = $this->fixtures->aUserConfirmationToken(
            $user,
            TokenGenerator::bin2hex(24),
            new DateTimeImmutable('-1 hour'),
        );

        // when
        $this->client->request('GET', "/confirm-email/{$userConfirmationToken->getToken()}");

        // then
        self::assertResponseRedirects('/login');

        // and then
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Link potwierdzający jest nieprawidłowy');

        // and then (token will be invalidated)
        $userConfirmationToken = $this->userConfirmationTokenRepository->find($userConfirmationToken->getId());
        self::assertFalse($userConfirmationToken->isValid());
    }

    #[Test]
    public function account_will_be_confirmed_with_valid_token(): void
    {
        // given
        $user = $this->fixtures->anUnverifiedUser();
        $userConfirmationToken = $this->fixtures->aUserConfirmationToken(
            $user,
            TokenGenerator::bin2hex(24),
            new DateTimeImmutable('+1 hour'),
        );

        // when
        $this->client->request('GET', "/confirm-email/{$userConfirmationToken->getToken()}");

        // then
        self::assertResponseRedirects('/login');

        // and then
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Konto zostało potwierdzone, możesz się teraz zalogować');

        // and then
        $user = $this->userRepository->find($user->getId());
        $userConfirmationToken = $this->userConfirmationTokenRepository->find($userConfirmationToken->getId());
        self::assertTrue($user->isVerified());
        self::assertFalse($userConfirmationToken->isValid());
    }

    #[Test]
    public function resend_confirmation_email_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->anUnverifiedUser();

        // when
        $this->client->request('GET', "/resend-confirmation-email/view/{$user->getId()}");

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function email_will_be_resent(): void
    {
        // given
        $user = $this->fixtures->anUnverifiedUser();

        // when
        $this->client->request('GET', "/resend-confirmation-email/{$user->getId()}");

        // then
        self::assertResponseRedirects("/resend-confirmation-email/view/{$user->getId()}");

        // and then
        $newToken = $this->userConfirmationTokenRepository->findOneBy(['user' => $user]);
        self::assertEquals(1, $this->userConfirmationTokenRepository->count());
        self::assertNotNull($newToken);

        // and then
        $sent = $this->inMemoryTransport->getSent();
        /** @var SendEmail $message */
        $message = $sent[0]->getMessage();
        self::assertCount(1, $sent);
        self::assertInstanceOf(SendEmail::class, $message);
    }

    #[Test]
    public function user_must_wait_a_minute_for_another_resend(): void
    {
        // given
        $user = $this->fixtures->anUnverifiedUser();
        $this->fixtures->aUserConfirmationToken(
            $user,
            TokenGenerator::bin2hex(24),
            new DateTimeImmutable('-1 hour'),
        );

        // when
        $this->client->request('GET', "/resend-confirmation-email/{$user->getId()}");

        // then
        self::assertResponseRedirects("/resend-confirmation-email/view/{$user->getId()}");

        // and then
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Musisz poczekać minutę przed ponownym wysłaniem maila.');
        self::assertEquals(1, $this->userConfirmationTokenRepository->count());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use Account\Repository\ChangeEmailConfirmationTokenRepository;
use Mailer\AsyncCommand\SendEmail;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Security\Repository\UserRepository;
use Shared\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class ChangeEmailControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserRepository $userRepository;
    private ChangeEmailConfirmationTokenRepository $tokenRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->tokenRepository = self::getContainer()->get(ChangeEmailConfirmationTokenRepository::class);
    }

    #[Test]
    public function change_email_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/change-email');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Zmiana adresu email');
        self::assertSelectorTextContains('body', 'Zapisz nowy email');
    }

    #[Test]
    public function user_must_provide_current_password_to_change_email(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $crawler = $this->client->request('GET', '/account/change-email');
        $form = $crawler->selectButton('Zapisz nowy email')->form([
            'change_email[currentPassword]' => 'wrong_current_password',
            'change_email[newEmail]' => 'new_email@gmail.com',
        ]);
        $this->client->submit($form);

        // and then
        self::assertSelectorTextContains('body', 'Podane obecne hasło jest nieprawidłowe');

        // and then
        $user = $this->userRepository->find($user->getId());
        self::assertNotEquals('new_email@gmail.com', $user->getEmail());
    }

    #[Test]
    public function user_provided_new_email_must_be_unique(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $crawler = $this->client->request('GET', '/account/change-email');
        $form = $crawler->selectButton('Zapisz nowy email')->form([
            'change_email[currentPassword]' => 'Password1...',
            'change_email[newEmail]' => 'lucky.luck@gmail.com',
        ]);
        $this->client->submit($form);

        // and then
        self::assertSelectorTextContains('body', 'Ten adres e-mail jest już używany.');
    }

    #[Test]
    public function user_will_receive_email_confirming_new_email(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $crawler = $this->client->request('GET', '/account/change-email');
        $form = $crawler->selectButton('Zapisz nowy email')->form([
            'change_email[currentPassword]' => 'Password1...',
            'change_email[newEmail]' => 'new_email@gmail.com',
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects('/account/change-email');

        // and then
        $inMemoryTransport = self::getContainer()->get('messenger.transport.async');
        $sent = $inMemoryTransport->getSent();
        /** @var SendEmail $message */
        $message = $sent[0]->getMessage();
        self::assertCount(1, $sent);
        self::assertInstanceOf(SendEmail::class, $message);
        self::assertEquals(['lucky.luck@gmail.com'], $message->to);
        self::assertEquals('Potwierdź zmianę adresu email', $message->subject);
        self::assertEquals('@mailer/change_email/change_email_pl.html.twig', $message->htmlTemplate);
        self::assertEquals('@mailer/change_email/change_email_pl.txt.twig', $message->plainTemplate);
        self::assertEquals([
            'username' => 'LuckyLuck',
            'newEmail' => 'new_email@gmail.com',
            'changeEmailUrl' => '',
        ], $message->contentParams);

        // and then
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Sprawdź swój obecny adres email w celu potwierdzenia zmiany.');

        // and then
        $user = $this->userRepository->find($user->getId());
        self::assertNotEquals('new_email@gmail.com', $user->getEmail());

        // and then
        $changeEmailConfirmationToken = $this->tokenRepository->findOneBy([]);
        self::assertEquals(1, $this->tokenRepository->count());
        self::assertEquals($user, $changeEmailConfirmationToken->getUser());
        self::assertEquals('new_email@gmail.com', $changeEmailConfirmationToken->getNewEmail());
    }

    #[Test]
    public function confirming_not_existent_token_will_be_handled(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // and given
        $this->client->request('GET', '/account/change-email/confirmation/4NFJS93NFJJ902MSD9J0S');

        // and then
        self::assertResponseRedirects('/home');

        // and then
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Link potwierdzający jest nieprawidłowy lub wygasł.');
    }

    #[Test]
    public function confirm_action_invalidates_token_and_changes_user_email(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // and given
        $token = TokenGenerator::bin2hex(24);
        $confirmationToken = $this->fixtures->aChangeEmailConfirmationToken(
            user: $user,
            newEmail: 'new_email@gmail.com',
            token: $token,
            expiryAt: new DateTimeImmutable('+1 hour'),
        );

        // when
        $this->client->request('GET', "/account/change-email/confirmation/$token");

        // and then
        self::assertResponseRedirects('/home');

        // and then
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Twój adres email został zmieniony.');

        // and then
        self::assertFalse($confirmationToken->isValid());

        // and then
        self::assertEquals('new_email@gmail.com', $user->getEmail());
    }
}

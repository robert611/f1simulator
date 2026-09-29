<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use PHPUnit\Framework\Attributes\Test;
use Security\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class ChangeEmailTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
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
}


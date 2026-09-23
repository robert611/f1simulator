<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Security\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tests\Common\Fixtures;

class AccountControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function only_logged_user_can_access_account_pages(string $method, string $url): void
    {
        // when
        $this->client->request($method, $url);

        // then
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/login');
    }

    #[Test]
    public function index_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/index');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'LuckyLuck');
        self::assertSelectorTextContains('body', 'lucky.luck@gmail.com');
        self::assertSelectorTextContains('body', 'Polska');
        self::assertSelectorTextContains('body', $user->getCreatedAt()->format('d.m.Y H:i'));
    }

    #[Test]
    public function change_password_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/change-password');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Zmiana hasła');
        self::assertSelectorTextContains('body', 'Zapisz nowe hasło');
    }

    #[Test]
    public function password_can_be_changed(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $crawler = $this->client->request('GET', '/account/change-password');
        $form = $crawler->selectButton('Zapisz nowe hasło')->form([
            'change_password[currentPassword]' => 'Password1...',
            'change_password[newPassword][first]' => 'Password1!!!!',
            'change_password[newPassword][second]' => 'Password1!!!!',
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects('/account/change-password');

        // and then
        $this->client->followRedirect();
        self::assertSelectorTextContains('body', 'Twoje hasło zostało zmienione.');

        // and then
        $user = $this->userRepository->find($user->getId());
        self::assertTrue($this->passwordHasher->isPasswordValid($user, 'Password1!!!!'));
    }

    public static function provideUrls(): array
    {
        return [
            ['GET', '/account/index'],
            ['GET', '/account/change-password'],
        ];
    }
}

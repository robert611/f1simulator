<?php

declare(strict_types=1);

namespace Tests\Functional\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Security\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class AdminUserControllerTest extends WebTestCase
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
    #[DataProvider('provideUrls')]
    public function only_admin_can_access_admin_user_endpoints(string $method, string $url): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request($method, $url);

        // then
        self::assertResponseStatusCodeSame(302);
    }

    #[Test]
    public function admin_user_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/admin-user');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function admin_user_page_displays_all_users(): void
    {
        // given
        $admin = $this->fixtures->anAdmin();
        $this->client->loginUser($admin);

        // and given
        $this->fixtures->aCustomUser('edmund', 'edmund@gmail.com');
        $this->fixtures->aCustomUser('fernand', 'fernand@gmail.com');
        $this->fixtures->aCustomUser('kacper', 'kacper@gmail.com');
        $this->fixtures->aCustomUser('maximilian', 'maximilian@gmail.com');

        // when
        $this->client->request('GET', '/admin-user');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'admin@gmail.com');
        self::assertSelectorTextContains('body', 'edmund@gmail.com');
        self::assertSelectorTextContains('body', 'fernand@gmail.com');
        self::assertSelectorTextContains('body', 'kacper@gmail.com');
        self::assertSelectorTextContains('body', 'maximilian@gmail.com');
    }

    #[Test]
    public function admin_user_show_page_can_be_displayed(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', "/admin-user/{$user->getId()}");

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', (string) $user->getId());
        self::assertSelectorTextContains('body', $user->getUsername());
        self::assertSelectorTextContains('body', $user->getEmail());
    }

    #[Test]
    public function admin_user_edit_form_can_be_displayed(): void
    {
        // given
        $admin = $this->fixtures->anAdmin();
        $this->client->loginUser($admin);

        // and given
        $userToEdit = $this->fixtures->aCustomUser('user_to_edit', 'user_to_edit@gmail.com');

        // when
        $this->client->request('GET', "/admin-user/{$userToEdit->getId()}/edit");

        // then
        self::assertResponseIsSuccessful();
        $crawler = $this->client->getCrawler();

        // and then
        self::assertSelectorTextContains('body', 'Edycja użytkownika');
        self::assertSelectorTextContains('body', 'Czy jest zweryfikowany?');
        self::assertSelectorTextContains('body', 'Czy jest zablokowany?');

        // and then
        self::assertSame(
            (string) $userToEdit->isVerified(),
            $crawler->filter('input[name="user_edit[isVerified]"]')->attr('value'),
        );
        self::assertSame(
            (string) $userToEdit->isBlocked(),
            $crawler->filter('input[name="user_edit[isBlocked]"]')->attr('value'),
        );
    }

    #[Test]
    public function admin_user_edition_works(): void
    {
        // given
        $admin = $this->fixtures->anAdmin();
        $this->client->loginUser($admin);

        // and given
        $userToEdit = $this->fixtures->aCustomUser('user_to_edit', 'user_to_edit@gmail.com');
        $userToEdit->setIsVerified(false);
        $userToEdit->setIsBlocked(false);
        $this->userRepository->upgradePassword($userToEdit, $userToEdit->getPassword());

        // when
        $crawler = $this->client->request('GET', "/admin-user/{$userToEdit->getId()}/edit");
        $form = $crawler->selectButton('Zapisz')->form([
            'user_edit[isVerified]' => true,
            'user_edit[isBlocked]' => true,
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/admin-user/{$userToEdit->getId()}/edit");

        // and then
        $updatedUser = $this->userRepository->find($userToEdit->getId());
        self::assertTrue($updatedUser->isVerified());
        self::assertTrue($updatedUser->isBlocked());
    }

    public static function provideUrls(): array
    {
        return [
            ['GET', '/admin-user'],
            ['GET', '/admin-user/1/edit'],
            ['POST', '/admin-user/1/edit'],
        ];
    }
}

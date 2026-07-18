<?php

declare(strict_types=1);

namespace Tests\Functional\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class AdminUserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
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

    public static function provideUrls(): array
    {
        return [
            ['GET', '/admin-user'],
        ];
    }
}

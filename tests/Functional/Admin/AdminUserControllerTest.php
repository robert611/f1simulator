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

    public static function provideUrls(): array
    {
        return [
            ['GET', '/admin-user'],
        ];
    }
}

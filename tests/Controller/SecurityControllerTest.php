<?php

declare(strict_types=1);

namespace Tests\Controller;

use Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[DataProvider('provideUrls')]
    public function testBehaviorInCaseOfUnloggedUser(): void
    {
        $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('provideUrls')]
    public function testBehaviorInCaseOfLoggedUser(string $url): void
    {
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        $this->client->request('GET', $url);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public static function provideUrls(): array
    {
        return [
            ['/login'],
            ['/logout'],
        ];
    }
}

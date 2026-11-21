<?php

declare(strict_types=1);

namespace Tests\Functional\OutgoingApi;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class ApiTokenAuthenticationTest extends WebTestCase
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
    public function request_without_token_is_unauthorized(string $method, string $url): void
    {
        // when
        $this->client->request($method, $url);

        // then
        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('WWW-Authenticate', 'Bearer');
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function request_with_invalid_token_is_unauthorized(string $method, string $url): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->fixtures->anApiToken($user, str_repeat('a', 64));

        // when
        $this->client->request($method, $url, server: [
            'HTTP_AUTHORIZATION' => 'Bearer ' . str_repeat('b', 64),
        ]);

        // then
        self::assertResponseStatusCodeSame(401);
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function request_with_valid_token_is_authorized(string $method, string $url): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->fixtures->anApiToken($user, str_repeat('a', 64));

        // when
        $this->client->request($method, $url, server: [
            'HTTP_AUTHORIZATION' => 'Bearer ' . str_repeat('a', 64),
        ]);

        // then
        self::assertNotEquals(401, $this->client->getResponse()->getStatusCode());
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function blocked_user_token_is_rejected(string $method, string $url): void
    {
        // given
        $user = $this->fixtures->aBlockedUser();
        $this->fixtures->anApiToken($user, str_repeat('a', 64));

        // when
        $this->client->request($method, $url, server: [
            'HTTP_AUTHORIZATION' => 'Bearer ' . str_repeat('a', 64),
        ]);

        // then
        self::assertResponseStatusCodeSame(401);
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function deleted_user_token_is_rejected(string $method, string $url): void
    {
        // given
        $user = $this->fixtures->aDeletedUser();
        $this->fixtures->anApiToken($user, str_repeat('a', 64));

        // when
        $this->client->request($method, $url, server: [
            'HTTP_AUTHORIZATION' => 'Bearer ' . str_repeat('a', 64),
        ]);

        // then
        self::assertResponseStatusCodeSame(401);
    }

    #[Test]
    public function api_documentation_is_public(): void
    {
        // when
        $this->client->request('GET', '/api/doc.json');

        // then
        self::assertResponseIsSuccessful();
    }

    public static function provideUrls(): array
    {
        return [
            ['GET', '/api/seasons'],
            ['GET', '/api/seasons/1000'],
        ];
    }
}

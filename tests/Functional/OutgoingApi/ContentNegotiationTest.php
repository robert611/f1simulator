<?php

declare(strict_types=1);

namespace Tests\Functional\OutgoingApi;

use PHPUnit\Framework\Attributes\Test;
use Shared\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Common\Fixtures;

final class ContentNegotiationTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_returns_json_when_json_is_accepted(): void
    {
        // given
        $plainToken = TokenGenerator::bin2hex(32);
        $this->fixtures->anApiToken($this->fixtures->aUser(), $plainToken);

        // when
        $this->client->request(method: 'GET', uri: '/api/seasons', server: [
            'HTTP_AUTHORIZATION' => "Bearer $plainToken",
            'HTTP_ACCEPT' => 'application/json',
        ]);

        // then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertSame('json', $this->client->getRequest()->getRequestFormat());

        // and then
        self::assertResponseHeaderSame('Vary', 'Accept');
    }

    #[Test]
    public function it_returns_406_when_no_supported_format_is_accepted(): void
    {
        // given
        $plainToken = TokenGenerator::bin2hex(32);
        $this->fixtures->anApiToken($this->fixtures->aUser(), $plainToken);

        // when
        $this->client->request(method: 'GET', uri: '/api/seasons', server: [
            'HTTP_AUTHORIZATION' => "Bearer $plainToken",
            'HTTP_ACCEPT' => 'application/xml',
        ]);

        // then
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_ACCEPTABLE);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
        self::assertResponseHeaderSame('Vary', 'Accept');

        // and then
        self::assertEquals(
            [
                'title' => 'Not Acceptable',
                'status' => Response::HTTP_NOT_ACCEPTABLE,
                'detail' => 'Supported media types: application/json.',
            ],
            json_decode($this->client->getResponse()->getContent(), true),
        );
    }

    #[Test]
    public function it_verifies_token_before_negotiating_format(): void
    {
        // when
        $this->client->request(method: 'GET', uri: '/api/seasons', server: [
            'HTTP_ACCEPT' => 'application/xml',
        ]);

        // then
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertResponseHeaderSame('Vary', 'Accept');
    }

    #[Test]
    public function it_verifies_invalid_token_before_negotiating_format(): void
    {
        // when
        $this->client->request(method: 'GET', uri: '/api/seasons', server: [
            'HTTP_AUTHORIZATION' => 'Bearer invalid-token',
            'HTTP_ACCEPT' => 'application/xml',
        ]);

        // then
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_does_not_negotiate_api_documentation(): void
    {
        // when
        $this->client->request(method: 'GET', uri: '/api/doc', server: [
            'HTTP_ACCEPT' => 'text/html',
        ]);

        // then
        self::assertResponseIsSuccessful();
        self::assertResponseNotHasHeader('Vary');
    }
}

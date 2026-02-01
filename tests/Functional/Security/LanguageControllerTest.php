<?php

declare(strict_types=1);

namespace Tests\Functional\Security;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LanguageControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    #[Test]
    public function it_changes_language_and_redirects_to_referer(): void
    {
        // given
        $this->client->request('GET', '/change-language/pl',
            server: [
                'HTTP_REFERER' => '/some-page',
            ],
        );

        // then
        $response = $this->client->getResponse();
        $session = $this->client->getRequest()->getSession();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/some-page', $response->headers->get('Location'));
        self::assertSame('pl', $session->get('_locale'));
    }

    #[Test]
    public function it_changes_language_and_redirects_to_home_when_no_referer(): void
    {
        // when
        $this->client->request('GET', '/change-language/en');

        // then
        $response = $this->client->getResponse();
        $session = $this->client->getRequest()->getSession();

        self::assertSame(302, $response->getStatusCode());
        self::assertStringContainsString(
            '/',
            $response->headers->get('Location'),
        );
        self::assertSame('en', $session->get('_locale'));
    }
}

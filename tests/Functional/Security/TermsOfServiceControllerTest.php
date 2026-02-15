<?php

declare(strict_types=1);

namespace Tests\Functional\Security;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TermsOfServiceControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    #[Test]
    public function terms_of_service_page_is_successful(): void
    {
        // when
        $this->client->request('GET', '/terms-of-service',);

        // then
        self::assertResponseIsSuccessful();
    }
}

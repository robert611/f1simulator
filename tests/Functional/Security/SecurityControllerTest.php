<?php

declare(strict_types=1);

namespace Tests\Functional\Security;

use PHPUnit\Framework\Attributes\Test;
use Tests\Common\Fixtures;
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

    #[Test]
    public function login_page_is_successful(): void
    {
        // when
        $this->client->request('GET', '/login');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function logged_user_cannot_access_login_page(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/login');

        // then
        self::assertResponseRedirects('/home');
    }

    #[Test]
    public function logging_out_works(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/logout');

        // then
        self::assertResponseRedirects('/');

        // and then
        $this->client->followRedirect();
        $tokenStorage = static::getContainer()->get('security.token_storage');
        self::assertNull($tokenStorage->getToken());
    }

    #[Test]
    public function login_form_works(): void
    {
        // given
        $this->fixtures->aCustomUser('John', 'test@gmail.com');

        // when
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Zaloguj się')->form([
            '_username' => 'John',
            '_password' => 'Password1...',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        // then
        self::assertResponseRedirects('/home');
        $tokenStorage = static::getContainer()->get('security.token_storage');
        self::assertNotNull($tokenStorage->getToken());
    }

    #[Test]
    public function unverified_user_will_be_redirected_to_verification_page(): void
    {
        // given
        $user = $this->fixtures->anUnverifiedUser();

        // when
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Zaloguj się')->form([
            '_username' => 'super_fast_driver',
            '_password' => 'password',
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/resend-confirmation-email/{$user->getId()}");

        // and then
        $tokenStorage = static::getContainer()->get('security.token_storage');
        self::assertNull($tokenStorage->getToken());
    }
}

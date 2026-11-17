<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use OutgoingApi\Entity\ApiToken;
use OutgoingApi\Repository\ApiTokenRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class AccountApiTokenControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private ApiTokenRepository $apiTokenRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->apiTokenRepository = self::getContainer()->get(ApiTokenRepository::class);
    }

    #[Test]
    public function index_page_renders_without_token(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/api-token/index');

        // then
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Nie wygenerowałeś jeszcze tokenu API.');
        self::assertSelectorNotExists('#api-token');
    }

    #[Test]
    public function existing_token_is_not_displayed(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $plainToken = str_repeat('a', 64);
        $this->fixtures->anApiToken($user, $plainToken);
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/api-token/index');

        // then
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#api-token-status');
        self::assertSelectorNotExists('#api-token');
        self::assertStringNotContainsString($plainToken, (string) $this->client->getResponse()->getContent());
    }

    #[Test]
    public function generated_token_is_shown_once_and_stored_as_hash(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $crawler = $this->client->request('GET', '/account/api-token/index');
        $form = $crawler->selectButton('Wygeneruj token')->form();
        $this->client->submit($form);

        // then
        self::assertResponseRedirects('/account/api-token/index');

        // and then the plain token is displayed after redirect
        $crawler = $this->client->followRedirect();
        $plainToken = $crawler->filter('#api-token')->attr('value');
        self::assertSame(64, strlen($plainToken));

        // and then only its hash is stored
        $apiToken = $this->apiTokenRepository->findOneByUser($user);
        self::assertNotNull($apiToken);
        self::assertSame(ApiToken::hash($plainToken), $apiToken->getTokenHash());
        self::assertNotSame($plainToken, $apiToken->getTokenHash());

        // and then it is not displayed again
        $this->client->request('GET', '/account/api-token/index');
        self::assertSelectorNotExists('#api-token');
        self::assertStringNotContainsString($plainToken, (string) $this->client->getResponse()->getContent());
    }

    #[Test]
    public function regenerating_token_replaces_the_old_one(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $oldToken = str_repeat('a', 64);
        $this->fixtures->anApiToken($user, $oldToken);
        $this->client->loginUser($user);

        // when
        $crawler = $this->client->request('GET', '/account/api-token/index');
        $form = $crawler->selectButton('Wygeneruj nowy token')->form();
        $this->client->submit($form);

        // then
        self::assertResponseRedirects('/account/api-token/index');

        // and then
        self::assertNull($this->apiTokenRepository->findOneByPlainToken($oldToken));
        self::assertCount(1, $this->apiTokenRepository->findBy(['user' => $user]));
    }

    #[Test]
    public function generating_token_with_invalid_csrf_token_is_denied(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('POST', '/account/api-token/generate', ['token' => 'invalid']);

        // then
        self::assertResponseStatusCodeSame(403);
        self::assertNull($this->apiTokenRepository->findOneByUser($user));
    }
}

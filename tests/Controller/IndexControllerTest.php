<?php 

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[DataProvider('provideUrls')]
    public function testIndex(string $url): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', $url);

        // then
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('provideUrls')]
    public function testIndexInCaseOfUnloggedUser(string $url): void
    {
        $this->client->request('GET', $url);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testRedirectToHome(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/');

        // then
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public static function provideUrls(): array
    {
        return [
            ['/home'],
            ['/home/RACE'],
            ['/home/DRIVERS'],
            ['/home/QUALIFICATIONS'],
        ];
    }
}

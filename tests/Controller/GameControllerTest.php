<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class GameControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testStartSeason()
    {
        $this->logIn();

        $teamId = self::$container->get('doctrine')->getRepository(Team::class)->findAll()[0]->getId();

        $this->client->request('POST', '/game/season/start', ['team' => $teamId]);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    
    public function testEndSeason()
    {
        $this->logIn();

        $this->client->request('POST', '/game/season/end');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testSimulateRace()
    {
        $this->logIn();

        $this->client->request('POST', '/game/simulate/race');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPagesInCaseOfUnloggedUser($url)
    {
        $this->client->request('GET', $url);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            ['/game/season/start'],
            ['/game/season/end'],
            ['/game/simulate/race']
        ];
    }

    private function logIn()
    {
        $session = self::$container->get('session');
        $entityManager = self::$container->get('doctrine'); 

        $user = $entityManager->getRepository(User::class)->findAll()[0];

        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'guard_context';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new PostAuthenticationGuardToken($user, 'username', ['ROLE_USER']);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class IndexControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider provideUrls
     */
    public function testIndex($url)
    {
        $this->logIn();

        $this->client->request('GET', '/home');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testIndexInCaseOfUnloggedUser()
    {
        $this->client->request('GET', '/home');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testRedirectToHome()
    {
        $this->logIn();

        $this->client->request('GET', '/');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            ['/home'],
            ['/home/race'],
            ['/home/drivers'],
            ['home/qualifications'],
            ['home/asdasdasd']
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
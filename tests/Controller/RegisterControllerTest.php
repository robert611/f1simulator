<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class RegisterControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider provideUrls
     */
    public function testBehaviorInCaseOfUnloggedUser($url)
    {
        $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider provideUrls
     */
    public function testBehaviorInCaseOfLoggedUser($url)
    {
        $this->logIn();

        $this->client->request('GET', $url);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            ['/register']
        ];
    }

   
}
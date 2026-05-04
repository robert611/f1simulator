<?php

declare(strict_types=1);

namespace Tests\Functional\Domain;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class TeamsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_returns_a_list_of_teams(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');
        $charlesLeclerc = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $carlosSainz = $this->fixtures->aDriver('Carlos', 'Sainz', $teamFerrari, 55);
        $maxVerstappen = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);
        $sergioPerez = $this->fixtures->aDriver('Sergio', 'Perez', $teamRedBull, 11);


        // when
        $this->client->request('GET', '/teams');
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertCount(2, $responseData);
        self::assertSame(
            [
                'id' => $teamFerrari->getId(),
                'name' => $teamFerrari->getName(),
                'picture' => $teamFerrari->getPicture(),
                'pictureUrl' => null,
                'drivers' => [
                    [
                        'id' => $charlesLeclerc->getId(),
                        'name' => $charlesLeclerc->getName(),
                        'surname' => $charlesLeclerc->getSurname(),
                        'carNumber' => $charlesLeclerc->getCarNumber(),
                    ],
                    [
                        'id' => $carlosSainz->getId(),
                        'name' => $carlosSainz->getName(),
                        'surname' => $carlosSainz->getSurname(),
                        'carNumber' => $carlosSainz->getCarNumber(),
                    ],
                ],
            ],
            $responseData[0],
        );
        self::assertSame(
            [
                'id' => $teamRedBull->getId(),
                'name' => $teamRedBull->getName(),
                'picture' => $teamRedBull->getPicture(),
                'pictureUrl' => null,
                'drivers' => [
                    [
                        'id' => $maxVerstappen->getId(),
                        'name' => $maxVerstappen->getName(),
                        'surname' => $maxVerstappen->getSurname(),
                        'carNumber' => $maxVerstappen->getCarNumber(),
                    ],
                    [
                        'id' => $sergioPerez->getId(),
                        'name' => $sergioPerez->getName(),
                        'surname' => $sergioPerez->getSurname(),
                        'carNumber' => $sergioPerez->getCarNumber(),
                    ],
                ],
            ],
            $responseData[1],
        );
    }
}

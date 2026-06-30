<?php

declare(strict_types=1);

namespace Tests\Functional\Domain;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class TrackControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_displays_leaflet_map(): void
    {
        // given
        $track = $this->fixtures->aTrackWithCoordinates(
            raceName: 'Dutch Grand Prix',
            name: 'Belgium',
            picture: 'Belgium.png',
            latitude: '-37.849722',
            longitude: '144.968333',
        );

        // when
        $this->client->request('GET', "/track/leaflet-map/{$track->getId()}");

        // then
        self::assertResponseIsSuccessful();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Functional\OutgoingApi\Seasons;

use OutgoingApi\Contract\OutgoingApiDateTimeSerializer;
use PHPUnit\Framework\Attributes\Test;
use Shared\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Common\Fixtures;

final class SeasonsGetControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_returns_single_season(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $plainToken = TokenGenerator::bin2hex(32);
        $this->fixtures->anApiToken($user, $plainToken);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // and given
        $season = $this->fixtures->aSeason($user, $driver);

        // when
        $this->client->request(method: 'GET', uri: "/api/seasons/{$season->getId()}", server: [
            'HTTP_AUTHORIZATION' => "Bearer $plainToken",
        ]);

        // then
        self::assertResponseIsSuccessful();

        // and then
        $responseContent = $this->client->getResponse()->getContent();
        self::assertEquals(
            [
                'id' => $season->getId(),
                'driverId' => $driver->getId(),
                'userId' => $user->getId(),
                'position' => $season->getPosition(),
                'completed' => $season->getCompleted(),
                'completedAt' => $season->getCompletedAt(),
                'createdAt' => OutgoingApiDateTimeSerializer::serialize($season->getCreatedAt()),
                'updatedAt' => OutgoingApiDateTimeSerializer::serialize($season->getUpdatedAt()),
            ],
            json_decode($responseContent, true),
        );
    }

    #[Test]
    public function season_of_other_user_will_return_404(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $otherUser = $this->fixtures->aCustomUser('other_user', 'other_user@gmail.com');

        // and given
        $plainToken = TokenGenerator::bin2hex(32);
        $this->fixtures->anApiToken($user, $plainToken);

        // and given
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');
        $driver = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 33);

        // and given
        $otherUserSeason = $this->fixtures->aSeason($otherUser, $driver);

        // when
        $this->client->request(method: 'GET', uri: "/api/seasons/{$otherUserSeason->getId()}", server: [
            'HTTP_AUTHORIZATION' => "Bearer $plainToken",
        ]);

        // then
        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        // and then
        $responseContent = $this->client->getResponse()->getContent();
        self::assertEquals(
            [
                'title' => 'Season not found',
                'status' => Response::HTTP_NOT_FOUND,
            ],
            json_decode($responseContent, true),
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\Functional\OutgoingApi\Seasons;

use OutgoingApi\Contract\OutgoingApiDateTimeSerializer;
use PHPUnit\Framework\Attributes\Test;
use Shared\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class SeasonsListControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_returns_seasons_list(): void
    {
        // given
        $user1 = $this->fixtures->aUser();
        $user2 = $this->fixtures->aCustomUser('second_user', 'second_user@gmail.com');

        // and given
        $plainToken = TokenGenerator::bin2hex(32);
        $this->fixtures->anApiToken($user1, $plainToken);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // and given
        $season1 = $this->fixtures->aSeason($user1, $driver1);
        $season2 = $this->fixtures->aSeason($user1, $driver1);

        // and given (other user season, should not be in the results)
        $this->fixtures->aSeason($user2, $driver1);

        // when
        $this->client->request(method: 'GET', uri: '/api/seasons', server: [
            'HTTP_AUTHORIZATION' => "Bearer $plainToken",
        ]);

        // then
        self::assertResponseIsSuccessful();

        // and then
        $responseContent = $this->client->getResponse()->getContent();
        self::assertEquals([
            [
                'id' => $season1->getId(),
                'driverId' => $driver1->getId(),
                'userId' => $user1->getId(),
                'position' => $season1->getPosition(),
                'completed' => $season1->getCompleted(),
                'completedAt' => $season1->getCompletedAt(),
                'createdAt' => OutgoingApiDateTimeSerializer::serialize($season1->getCreatedAt()),
                'updatedAt' => OutgoingApiDateTimeSerializer::serialize($season1->getUpdatedAt()),
            ],
            [
                'id' => $season2->getId(),
                'driverId' => $driver1->getId(),
                'userId' => $user1->getId(),
                'position' => $season2->getPosition(),
                'completed' => $season2->getCompleted(),
                'completedAt' => $season2->getCompletedAt(),
                'createdAt' => OutgoingApiDateTimeSerializer::serialize($season1->getCreatedAt()),
                'updatedAt' => OutgoingApiDateTimeSerializer::serialize($season1->getUpdatedAt()),
            ],
        ], json_decode($responseContent, true));
    }
}

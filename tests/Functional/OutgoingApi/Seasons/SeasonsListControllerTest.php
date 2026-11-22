<?php

declare(strict_types=1);

namespace Tests\Functional\OutgoingApi\Seasons;

use DateTimeImmutable;
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
            'HTTP_ACCEPT' => 'application/json',
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


    #[Test]
    public function it_returns_seasons_list_as_xml(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $plainToken = TokenGenerator::bin2hex(32);
        $this->fixtures->anApiToken($user, $plainToken);

        // and given
        $teamAlfaRomeo = $this->fixtures->aTeamWithName('Alfa Romeo');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamAlfaRomeo, 16);

        // and given
        $season1 = $this->fixtures->aSeason($user, $driver);
        $season2 = $this->fixtures->aSeason($user, $driver);

        // when
        $this->client->request(method: 'GET', uri: '/api/seasons', server: [
            'HTTP_AUTHORIZATION' => "Bearer $plainToken",
            'HTTP_ACCEPT' => 'application/xml',
        ]);

        // then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/xml');

        // and then
        $date = static fn (DateTimeImmutable $date): string => OutgoingApiDateTimeSerializer::serialize($date);
        self::assertXmlStringEqualsXmlString(
            <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <seasons>
                <season>
                    <id>{$season1->getId()}</id>
                    <driverId>{$driver->getId()}</driverId>
                    <userId>{$user->getId()}</userId>
                    <position/>
                    <completed>false</completed>
                    <completedAt/>
                    <createdAt>{$date($season1->getCreatedAt())}</createdAt>
                    <updatedAt>{$date($season1->getUpdatedAt())}</updatedAt>
                </season>
                <season>
                    <id>{$season2->getId()}</id>
                    <driverId>{$driver->getId()}</driverId>
                    <userId>{$user->getId()}</userId>
                    <position/>
                    <completed>false</completed>
                    <completedAt/>
                    <createdAt>{$date($season2->getCreatedAt())}</createdAt>
                    <updatedAt>{$date($season2->getUpdatedAt())}</updatedAt>
                </season>
            </seasons>
            XML,
            $this->client->getResponse()->getContent(),
        );
    }

    #[Test]
    public function it_returns_empty_seasons_list_as_xml(): void
    {
        // given
        $plainToken = TokenGenerator::bin2hex(32);
        $this->fixtures->anApiToken($this->fixtures->aUser(), $plainToken);

        // when
        $this->client->request(method: 'GET', uri: '/api/seasons', server: [
            'HTTP_AUTHORIZATION' => "Bearer $plainToken",
            'HTTP_ACCEPT' => 'application/xml',
        ]);

        // then
        self::assertResponseIsSuccessful();
        self::assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?><seasons/>',
            $this->client->getResponse()->getContent(),
        );
    }
}

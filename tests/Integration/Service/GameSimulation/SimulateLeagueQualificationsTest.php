<?php

declare(strict_types=1);

namespace Tests\Integration\Service\GameSimulation;

use Tests\Common\Fixtures;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Service\GameSimulation\SimulateLeagueQualifications;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimulateLeagueQualificationsTest extends KernelTestCase
{
    private Fixtures $fixtures;

    private SimulateLeagueQualifications $simulateLeagueQualifications;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->simulateLeagueQualifications = self::getContainer()->get(SimulateLeagueQualifications::class);
    }

    #[Test]
    public function it_returns_empty_results_when_league_has_no_players(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('owner1', 'owner1@example.com');
        $userSeason = $this->fixtures->aUserSeason('secret', 10, $owner, 'League 1', false, false);

        // and given (Add random driver to make sure it will not be used in any way in the method)
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $this->fixtures->aDriver('John', 'Speed', $team, 99);

        // when
        $results = $this->simulateLeagueQualifications->getLeagueQualificationsResults($userSeason);

        // then
        self::assertEmpty($results->getQualificationResults());
        self::assertEmpty($results->toPlainDriverArray());
    }

    #[Test]
    public function it_checks_if_returned_positions_for_all_players_and_each_driver_are_unique(): void
    {
        // given (Build a league with 3 teams and 6 players)
        $owner = $this->fixtures->aCustomUser('owner2', 'owner2@example.com');
        $userSeason = $this->fixtures->aUserSeason('secret-2', 10, $owner, 'League 2', false, false);

        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');

        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Carlos', 'Sainz', $teamFerrari, 55);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);
        $driver4 = $this->fixtures->aDriver('Sergio', 'Perez', $teamRedBull, 11);
        $driver5 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver6 = $this->fixtures->aDriver('George', 'Russell', $teamMercedes, 63);

        // and given
        $user1 = $this->fixtures->aCustomUser('Mark', 'mark@example.com');
        $user2 = $this->fixtures->aCustomUser('John', 'john@example.com');
        $user3 = $this->fixtures->aCustomUser('Clark', 'clark@example.com');
        $user4 = $this->fixtures->aCustomUser('Melissa', 'melissa@example.com');
        $user5 = $this->fixtures->aCustomUser('Anna', 'anna@example.com');
        $user6 = $this->fixtures->aCustomUser('Caroline', 'caroline@example.com');

        $player1 = $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver1);
        $player2 = $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver2);
        $player3 = $this->fixtures->aUserSeasonPlayer($userSeason, $user3, $driver3);
        $player4 = $this->fixtures->aUserSeasonPlayer($userSeason, $user4, $driver4);
        $player5 = $this->fixtures->aUserSeasonPlayer($userSeason, $user5, $driver5);
        $player6 = $this->fixtures->aUserSeasonPlayer($userSeason, $user6, $driver6);

        $playersIds = [
            $player1->getId(),
            $player2->getId(),
            $player3->getId(),
            $player4->getId(),
            $player5->getId(),
            $player6->getId(),
        ];

        // when
        $collection = $this->simulateLeagueQualifications->getLeagueQualificationsResults($userSeason);

        // then
        self::assertCount(6, $collection->getQualificationResults());

        // and then (Positions are 1...6 and unique)
        self::assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], array_keys($collection->toPlainArray()));

        // and then (Each driver appears exactly once and all are from players)
        $resultPlayerIds = array_map(
            static fn(UserSeasonPlayer $player) => $player->getId(),
            $collection->toPlainArray(),
        );
        self::assertCount(6, array_unique($resultPlayerIds));
        foreach ($resultPlayerIds as $playerId) {
            self::assertTrue(in_array($playerId, $playersIds));
        }
    }
}

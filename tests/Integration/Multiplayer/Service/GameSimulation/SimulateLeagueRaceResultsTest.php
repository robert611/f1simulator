<?php

declare(strict_types=1);

namespace Integration\Multiplayer\Service\GameSimulation;

use Domain\Contract\DTO\DriverDTO;
use Multiplayer\Model\GameSimulation\LeagueQualificationResult;
use Multiplayer\Model\GameSimulation\LeagueQualificationResultsCollection;
use Multiplayer\Service\GameSimulation\SimulateLeagueRaceResults;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class SimulateLeagueRaceResultsTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private SimulateLeagueRaceResults $simulateLeagueRaceResults;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->simulateLeagueRaceResults = self::getContainer()->get(SimulateLeagueRaceResults::class);
    }

    #[Test]
    public function it_simulates_league_race(): void
    {
        // given
        $userSeasonOwner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");
        $user1 = $this->fixtures->aCustomUser('johnXT', 'johnxt@gmail.com');
        $user2 = $this->fixtures->aCustomUser('maria212', 'maria212@gmail.com');

        // and given
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver("John", "Smith", $ferrari, 44);
        $driver2 = $this->fixtures->aDriver("Alex", "Apollo", $ferrari, 45);

        // and given
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $driver3 = $this->fixtures->aDriver("Yuki", "Grieg", $redBull, 46);

        // and given
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            10,
            $userSeasonOwner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $this->fixtures->aUserSeasonPlayer($userSeason, $userSeasonOwner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver2);
        $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver3);

        // when
        $result = $this->simulateLeagueRaceResults->simulateRaceResults($userSeason);

        // then
        self::assertCount(3, $result->getQualificationsResults()->toPlainArray());
        self::assertCount(3, $result->getRaceResults());
    }

    #[Test]
    public function it_returns_empty_league_race_results_when_no_drivers_provided(): void
    {
        // given (empty drivers array and empty qualification results)
        $drivers = [];
        $qualificationResults = LeagueQualificationResultsCollection::create();

        // when
        $results = $this->simulateLeagueRaceResults->getLeagueRaceResults($drivers, $qualificationResults);

        // then
        self::assertEmpty($results);
    }

    #[Test]
    public function it_generates_race_results_for_league_drivers(): void
    {
        // given (Create league setup with players and drivers)
        $owner = $this->fixtures->aCustomUser('owner', 'owner@example.com');
        $userSeason = $this->fixtures->aUserSeason('secret', 10, $owner, 'League 1', false, false);

        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');

        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);

        $user1 = $this->fixtures->aCustomUser('user1', 'user1@example.com');
        $user2 = $this->fixtures->aCustomUser('user2', 'user2@example.com');

        $player1 = $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver1);
        $player2 = $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver2);

        $drivers = [$driver1, $driver2];
        $drivers = DriverDTO::fromEntityCollection($drivers);

        // Create qualification results
        $qualificationResult1 = LeagueQualificationResult::create($player1, DriverDTO::fromEntity($driver1), 1);
        $qualificationResult2 = LeagueQualificationResult::create($player2, DriverDTO::fromEntity($driver2), 2);
        $qualificationResults = LeagueQualificationResultsCollection::create([
            $qualificationResult1,
            $qualificationResult2,
        ]);

        // when
        $results = $this->simulateLeagueRaceResults->getLeagueRaceResults($drivers, $qualificationResults);

        // then
        self::assertCount(2, $results);
        self::assertArrayHasKey(1, $results);
        self::assertArrayHasKey(2, $results);

        // and then (Verify all results contain valid driver IDs)
        self::assertContains($driver1->getId(), $results);
        self::assertContains($driver2->getId(), $results);
    }

    #[Test]
    public function it_ensures_unique_driver_positions_in_race_results(): void
    {
        // given (Create 3 drivers for testing uniqueness)
        $owner = $this->fixtures->aCustomUser('owner', 'owner@example.com');
        $userSeason = $this->fixtures->aUserSeason('secret', 10, $owner, 'League 1', false, false);

        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');

        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);

        $user1 = $this->fixtures->aCustomUser('user1', 'user1@example.com');
        $user2 = $this->fixtures->aCustomUser('user2', 'user2@example.com');
        $user3 = $this->fixtures->aCustomUser('user3', 'user3@example.com');

        $player1 = $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver1);
        $player2 = $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver2);
        $player3 = $this->fixtures->aUserSeasonPlayer($userSeason, $user3, $driver3);

        $drivers = [$driver1, $driver2, $driver3];
        $drivers = DriverDTO::fromEntityCollection($drivers);

        // Create qualification results
        $qualificationResult1 = LeagueQualificationResult::create($player1, DriverDTO::fromEntity($driver1), 1);
        $qualificationResult2 = LeagueQualificationResult::create($player2, DriverDTO::fromEntity($driver2), 2);
        $qualificationResult3 = LeagueQualificationResult::create($player3, DriverDTO::fromEntity($driver3), 3);
        $qualificationResults = LeagueQualificationResultsCollection::create([
            $qualificationResult1,
            $qualificationResult2,
            $qualificationResult3,
        ]);

        // when
        $results = $this->simulateLeagueRaceResults->getLeagueRaceResults($drivers, $qualificationResults);

        // then
        self::assertCount(3, $results);

        // and then (Verify positions are unique)
        $driverIds = array_values($results);
        self::assertCount(3, array_unique($driverIds));

        // and then (Verify positions are sequential)
        $positions = array_keys($results);
        self::assertEqualsCanonicalizing([1, 2, 3], $positions);
    }

    #[Test]
    public function it_handles_single_driver_league_race(): void
    {
        // given (A single driver in league)
        $owner = $this->fixtures->aCustomUser('owner', 'owner@example.com');
        $userSeason = $this->fixtures->aUserSeason('secret', 10, $owner, 'League 1', false, false);

        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        $user = $this->fixtures->aCustomUser('user', 'user@example.com');
        $player = $this->fixtures->aUserSeasonPlayer($userSeason, $user, $driver);

        $drivers = [$driver];
        $drivers = DriverDTO::fromEntityCollection($drivers);

        // Create qualification results
        $qualificationResult = LeagueQualificationResult::create($player, DriverDTO::fromEntity($driver), 1);
        $qualificationResults = LeagueQualificationResultsCollection::create([$qualificationResult]);

        // when
        $results = $this->simulateLeagueRaceResults->getLeagueRaceResults($drivers, $qualificationResults);

        // then
        self::assertCount(1, $results);
        self::assertArrayHasKey(1, $results);
        self::assertEquals($driver->getId(), $results[1]);
    }
}

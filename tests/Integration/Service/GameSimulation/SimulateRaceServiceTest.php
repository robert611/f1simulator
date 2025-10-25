<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\GameSimulation;

use App\Model\Configuration\QualificationAdvantage;
use App\Model\Configuration\TeamsStrength;
use App\Tests\Common\Fixtures;
use Computer\Model\GameSimulation\QualificationResult;
use Computer\Model\GameSimulation\QualificationResultsCollection;
use Domain\Service\GameSimulation\SimulateRaceService;
use Multiplayer\Model\GameSimulation\LeagueQualificationResult;
use Multiplayer\Model\GameSimulation\LeagueQualificationResultsCollection;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimulateRaceServiceTest extends KernelTestCase
{
    private Fixtures $fixtures;

    private SimulateRaceService $simulateRaceService;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->simulateRaceService = self::getContainer()->get(SimulateRaceService::class);
    }

    #[Test]
    public function it_correctly_simulates_first_race_of_the_season(): void
    {
        // given (A season with no previous races)
        $user = $this->fixtures->aCustomUser('user', 'user@example.com');
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 16);
        $season = $this->fixtures->aSeason($user, $driver);

        // and given
        $track1 = $this->fixtures->aTrack('Monaco', 'monaco.png');
        $this->fixtures->aTrack('Silverstone', 'silverstone.png');

        // when
        $this->simulateRaceService->simulateRace($season);

        // then (Verify race was created with first track)
        self::assertCount(1, $season->getRaces());

        self::assertNotNull($season->getLastRace());
        self::assertEquals($track1->getId(), $season->getLastRace()->getTrack()->getId());
        self::assertEquals($season->getId(), $season->getLastRace()->getSeason()->getId());
    }

    #[Test]
    public function it_correctly_simulates_subsequent_race_of_season(): void
    {
        // given
        $user = $this->fixtures->aCustomUser('user', 'user@example.com');
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 16);
        $season = $this->fixtures->aSeason($user, $driver);

        // and given
        $track1 = $this->fixtures->aTrack('Monaco', 'monaco.png');
        $track2 = $this->fixtures->aTrack('Silverstone', 'silverstone.png');

        // and given
        $firstRace = $this->fixtures->aRace($track1, $season);
        $season->addRace($firstRace);

        // when
        $this->simulateRaceService->simulateRace($season);

        // then
        self::assertCount(2, $season->getRaces());

        self::assertNotNull($season->getLastRace());
        self::assertEquals($track2->getId(), $season->getLastRace()->getTrack()->getId());
        self::assertEquals($season->getId(), $season->getLastRace()->getSeason()->getId());
    }

    #[Test]
    public function it_correctly_creates_qualification_results_for_all_drivers(): void
    {
        // given
        $user = $this->fixtures->aCustomUser('user', 'user@example.com');
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 16);
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $driver2 = $this->fixtures->aDriver('Lewis', 'Hamilton', $mercedes, 44);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $redBull, 33);

        // and given
        $this->fixtures->aTrack('Monaco', 'monaco.png');

        // when
        $this->simulateRaceService->simulateRace($season);

        // then
        $race = $season->getRaces()->first();
        $qualifications = $race->getQualifications();

        self::assertCount(3, $qualifications);

        // and then (All drivers have a qualification result)
        $qualificationDrivers = [];
        foreach ($qualifications as $qualification) {
            $qualificationDrivers[] = $qualification->getDriver()->getId();
        }

        self::assertContains($driver1->getId(), $qualificationDrivers);
        self::assertContains($driver2->getId(), $qualificationDrivers);
        self::assertContains($driver3->getId(), $qualificationDrivers);
    }

    #[Test]
    public function it_correctly_creates_race_results_for_all_drivers(): void
    {
        // given
        $user = $this->fixtures->aCustomUser('user', 'user@example.com');
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 16);
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $driver2 = $this->fixtures->aDriver('Lewis', 'Hamilton', $mercedes, 44);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $redBull, 33);

        // and given
        $this->fixtures->aTrack('Monaco', 'monaco.png');

        // when
        $this->simulateRaceService->simulateRace($season);

        // then (Verify race results were created)
        $race = $season->getRaces()->first();
        $raceResults = $race->getRaceResults();

        self::assertCount(3, $raceResults);

        // and then (All drivers have race results)
        $raceResultDrivers = [];
        $positions = [];
        foreach ($raceResults as $raceResult) {
            $raceResultDrivers[] = $raceResult->getDriver()->getId();
            $positions[] = $raceResult->getPosition();
        }

        self::assertContains($driver1->getId(), $raceResultDrivers);
        self::assertContains($driver2->getId(), $raceResultDrivers);
        self::assertContains($driver3->getId(), $raceResultDrivers);

        // and then (Positions are unique and sequential)
        self::assertEqualsCanonicalizing([1, 2, 3], $positions);
    }

    #[Test]
    public function it_uses_correct_track_selection_logic(): void
    {
        // given
        $user = $this->fixtures->aCustomUser('user', 'user@example.com');
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 16);
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('Monaco', 'monaco.png');
        $track2 = $this->fixtures->aTrack('Silverstone', 'silverstone.png');
        $this->fixtures->aTrack('Spa', 'spa.png');

        // when
        $this->simulateRaceService->simulateRace($season);
        $firstRace = $season->getLastRace();

        // and when (Simulate second race)
        $this->simulateRaceService->simulateRace($season);
        $secondRace = $season->getLastRace();

        // then (Verify track selection)
        self::assertEquals($track1->getId(), $firstRace->getTrack()->getId());
        self::assertEquals($track2->getId(), $secondRace->getTrack()->getId());
    }

    #[Test]
    public function it_returns_empty_race_results_when_no_qualification_results_provided(): void
    {
        // given (An empty qualification results collection)
        $qualificationResults = QualificationResultsCollection::create();

        // when
        $results = $this->simulateRaceService->getRaceResults($qualificationResults);

        // then
        self::assertEmpty($results);
    }

    #[Test]
    public function it_generates_race_results_for_all_drivers_in_database(): void
    {
        // given (Create drivers in a database)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');

        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);

        // Create qualification results
        $qualificationResult1 = QualificationResult::create($driver1, 1);
        $qualificationResult2 = QualificationResult::create($driver2, 2);
        $qualificationResult3 = QualificationResult::create($driver3, 3);
        $qualificationResults = QualificationResultsCollection::create([
            $qualificationResult1,
            $qualificationResult2,
            $qualificationResult3,
        ]);

        // when
        $results = $this->simulateRaceService->getRaceResults($qualificationResults);

        // then
        self::assertCount(3, $results);
        self::assertArrayHasKey(1, $results);
        self::assertArrayHasKey(2, $results);
        self::assertArrayHasKey(3, $results);

        // and then (Verify all results contain valid driver IDs)
        self::assertContains($driver1->getId(), $results);
        self::assertContains($driver2->getId(), $results);
        self::assertContains($driver3->getId(), $results);
    }

    #[Test]
    public function it_ensures_unique_driver_positions_in_race_results_for_getRaceResults(): void
    {
        // given (Create 4 drivers for testing uniqueness)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');
        $teamMcLaren = $this->fixtures->aTeamWithName('Mclaren');

        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);
        $driver4 = $this->fixtures->aDriver('Lando', 'Norris', $teamMcLaren, 4);

        // and given
        $qualificationResult1 = QualificationResult::create($driver1, 1);
        $qualificationResult2 = QualificationResult::create($driver2, 2);
        $qualificationResult3 = QualificationResult::create($driver3, 3);
        $qualificationResult4 = QualificationResult::create($driver4, 4);
        $qualificationResults = QualificationResultsCollection::create([
            $qualificationResult1,
            $qualificationResult2,
            $qualificationResult3,
            $qualificationResult4,
        ]);

        // when
        $results = $this->simulateRaceService->getRaceResults($qualificationResults);

        // then
        self::assertCount(4, $results);

        // and then (Verify positions are unique)
        $driverIds = array_values($results);
        self::assertCount(4, array_unique($driverIds));

        // and then (Verify positions are sequential)
        $positions = array_keys($results);
        self::assertEqualsCanonicalizing([1, 2, 3, 4], $positions);
    }

    #[Test]
    public function it_handles_single_driver_race_results(): void
    {
        // given (A single driver in a database)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // and given
        $qualificationResult = QualificationResult::create($driver, 1);
        $qualificationResults = QualificationResultsCollection::create([$qualificationResult]);

        // when
        $results = $this->simulateRaceService->getRaceResults($qualificationResults);

        // then
        self::assertCount(1, $results);
        self::assertArrayHasKey(1, $results);
        self::assertEquals($driver->getId(), $results[1]);
    }

    #[Test]
    public function it_returns_empty_league_race_results_when_no_drivers_provided(): void
    {
        // given (empty drivers array and empty qualification results)
        $drivers = [];
        $qualificationResults = LeagueQualificationResultsCollection::create();

        // when
        $results = $this->simulateRaceService->getLeagueRaceResults($drivers, $qualificationResults);

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

        // Create qualification results
        $qualificationResult1 = LeagueQualificationResult::create($player1, 1);
        $qualificationResult2 = LeagueQualificationResult::create($player2, 2);
        $qualificationResults = LeagueQualificationResultsCollection::create([
            $qualificationResult1,
            $qualificationResult2,
        ]);

        // when
        $results = $this->simulateRaceService->getLeagueRaceResults($drivers, $qualificationResults);

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

        // Create qualification results
        $qualificationResult1 = LeagueQualificationResult::create($player1, 1);
        $qualificationResult2 = LeagueQualificationResult::create($player2, 2);
        $qualificationResult3 = LeagueQualificationResult::create($player3, 3);
        $qualificationResults = LeagueQualificationResultsCollection::create([
            $qualificationResult1,
            $qualificationResult2,
            $qualificationResult3,
        ]);

        // when
        $results = $this->simulateRaceService->getLeagueRaceResults($drivers, $qualificationResults);

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

        // Create qualification results
        $qualificationResult = LeagueQualificationResult::create($player, 1);
        $qualificationResults = LeagueQualificationResultsCollection::create([$qualificationResult]);

        // when
        $results = $this->simulateRaceService->getLeagueRaceResults($drivers, $qualificationResults);

        // then
        self::assertCount(1, $results);
        self::assertArrayHasKey(1, $results);
        self::assertEquals($driver->getId(), $results[1]);
    }

    #[Test]
    public function it_returns_empty_coupons_when_no_qualification_results_provided(): void
    {
        // given (empty qualification results array)
        $qualificationResults = [];

        // when
        $coupons = $this->simulateRaceService->generateCoupons($qualificationResults);

        // then
        self::assertEmpty($coupons);
    }

    #[Test]
    public function it_generates_coupons_based_on_driver_strength_and_qualification_position(): void
    {
        // given (Create teams and drivers with known strengths)
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');

        $driver1 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver2 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);

        // Create a qualification results array: position => driver
        $qualificationResults = [
            1 => $driver1, // Mercedes driver in P1 (the highest strength)
            2 => $driver2, // Ferrari driver in P2
            3 => $driver3, // Red Bull driver in P3
        ];

        // when
        $coupons = $this->simulateRaceService->generateCoupons($qualificationResults);

        // then
        self::assertNotEmpty($coupons);

        // and then (Verify all coupons contain valid driver IDs)
        $expectedDriverIds = [$driver1->getId(), $driver2->getId(), $driver3->getId()];
        foreach ($coupons as $coupon) {
            self::assertTrue(in_array($coupon, $expectedDriverIds));
        }
    }

    #[Test]
    public function it_generates_more_coupons_for_higher_strength_drivers(): void
    {
        // given (Create drivers with different team strengths)
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes'); // Strength: 23
        $teamWilliams = $this->fixtures->aTeamWithName('Williams'); // Strength: 0.6

        $mercedesDriver = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $williamsDriver = $this->fixtures->aDriver('George', 'Russell', $teamWilliams, 63);

        $qualificationResults = [
            1 => $mercedesDriver, // P1 with Mercedes strength
            2 => $williamsDriver, // P2 with Williams strength
        ];

        // when
        $coupons = $this->simulateRaceService->generateCoupons($qualificationResults);

        // then
        $mercedesCoupons = array_filter($coupons, fn($id) => $id === $mercedesDriver->getId());
        $williamsCoupons = array_filter($coupons, fn($id) => $id === $williamsDriver->getId());

        // Mercedes driver should have significantly more coupons due to higher team strength
        self::assertGreaterThan(count($williamsCoupons), count($mercedesCoupons));
    }

    #[Test]
    public function it_respects_multiplier_in_coupon_generation(): void
    {
        // given (Create a single driver for predictable results)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        $qualificationResults = [1 => $driver];

        // Calculate the expected coupon count
        $teamsStrength = TeamsStrength::getTeamsStrength();
        $qualificationAdvantage = QualificationAdvantage::getQualificationResultAdvantage();
        $expectedStrength = ceil($teamsStrength['Ferrari'] + $qualificationAdvantage[1]);
        $expectedCouponCount = (int) ($expectedStrength * $this->simulateRaceService->multiplier);

        // when
        $coupons = $this->simulateRaceService->generateCoupons($qualificationResults);

        // then
        self::assertCount($expectedCouponCount, $coupons);

        // All coupons should be for the same driver
        foreach ($coupons as $coupon) {
            self::assertEquals($driver->getId(), $coupon);
        }
    }

    #[Test]
    public function it_handles_qualification_position_advantages_correctly(): void
    {
        // given (Create two drivers from the same team but different positions)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Carlos', 'Sainz', $teamFerrari, 55);

        $qualificationResults = [
            1 => $driver1, // P1 gets highest qualification advantage
            2 => $driver2, // P2 gets lower qualification advantage
        ];

        // when
        $coupons = $this->simulateRaceService->generateCoupons($qualificationResults);

        // then
        $driver1Coupons = array_filter($coupons, fn($id) => $id === $driver1->getId());
        $driver2Coupons = array_filter($coupons, fn($id) => $id === $driver2->getId());

        // P1 driver should have more coupons due to qualification advantage
        self::assertGreaterThan(count($driver2Coupons), count($driver1Coupons));
    }

    #[Test]
    public function it_generates_coupons_for_all_qualification_positions(): void
    {
        // given (Create drivers for multiple positions)
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');

        $driver1 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver2 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);

        $qualificationResults = [
            1 => $driver1,
            2 => $driver2,
            3 => $driver3,
        ];

        // when
        $coupons = $this->simulateRaceService->generateCoupons($qualificationResults);

        // then
        $driverIds = array_unique($coupons);
        self::assertCount(3, $driverIds);
        self::assertContains($driver1->getId(), $driverIds);
        self::assertContains($driver2->getId(), $driverIds);
        self::assertContains($driver3->getId(), $driverIds);
    }

    #[Test]
    public function it_handles_single_driver_qualification_results(): void
    {
        // given (A single driver in qualification)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        $qualificationResults = [1 => $driver];

        // when
        $coupons = $this->simulateRaceService->generateCoupons($qualificationResults);

        // then
        self::assertNotEmpty($coupons);

        // All coupons should be for the same driver
        foreach ($coupons as $coupon) {
            self::assertEquals($driver->getId(), $coupon);
        }
    }
}

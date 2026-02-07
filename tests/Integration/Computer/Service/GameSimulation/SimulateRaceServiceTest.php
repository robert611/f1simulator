<?php

declare(strict_types=1);

namespace Tests\Integration\Computer\Service\GameSimulation;

use Computer\Model\GameSimulation\QualificationResult;
use Computer\Model\GameSimulation\QualificationResultsCollection;
use Computer\Service\GameSimulation\SimulateRaceService;
use Domain\Contract\DTO\DriverDTO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

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
        self::assertEquals($track1->getId(), $season->getLastRace()->getTrackId());
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
        self::assertEquals($track2->getId(), $season->getLastRace()->getTrackId());
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
            $qualificationDrivers[] = $qualification->getDriverId();
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
            $raceResultDrivers[] = $raceResult->getDriverId();
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
        self::assertEquals($track1->getId(), $firstRace->getTrackId());
        self::assertEquals($track2->getId(), $secondRace->getTrackId());
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

        $driver1 = DriverDTO::fromEntity($driver1);
        $driver2 = DriverDTO::fromEntity($driver2);
        $driver3 = DriverDTO::fromEntity($driver3);

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

        $driver1 = DriverDTO::fromEntity($driver1);
        $driver2 = DriverDTO::fromEntity($driver2);
        $driver3 = DriverDTO::fromEntity($driver3);
        $driver4 = DriverDTO::fromEntity($driver4);

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
        $qualificationResult = QualificationResult::create(DriverDTO::fromEntity($driver), 1);
        $qualificationResults = QualificationResultsCollection::create([$qualificationResult]);

        // when
        $results = $this->simulateRaceService->getRaceResults($qualificationResults);

        // then
        self::assertCount(1, $results);
        self::assertArrayHasKey(1, $results);
        self::assertEquals($driver->getId(), $results[1]);
    }
}

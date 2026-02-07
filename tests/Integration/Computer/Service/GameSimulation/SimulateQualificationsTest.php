<?php

declare(strict_types=1);

namespace Tests\Integration\Computer\Service\GameSimulation;

use Computer\Service\GameSimulation\SimulateQualifications;
use Domain\Contract\DTO\DriverDTO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class SimulateQualificationsTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private SimulateQualifications $simulateQualifications;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->simulateQualifications = self::getContainer()->get(SimulateQualifications::class);
    }

    #[Test]
    public function it_returns_empty_results_when_no_drivers_exist(): void
    {
        // given (no drivers in database)

        // when
        $results = $this->simulateQualifications->getQualificationsResults();

        // then
        self::assertEmpty($results->getQualificationResults());
        self::assertEmpty($results->toPlainArray());
    }

    #[Test]
    public function it_checks_if_returned_positions_for_all_drivers_are_unique(): void
    {
        // given (Build 3 teams with 6 drivers total)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');

        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Carlos', 'Sainz', $teamFerrari, 55);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);
        $driver4 = $this->fixtures->aDriver('Sergio', 'Perez', $teamRedBull, 11);
        $driver5 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver6 = $this->fixtures->aDriver('George', 'Russell', $teamMercedes, 63);

        $driversIds = [
            $driver1->getId(),
            $driver2->getId(),
            $driver3->getId(),
            $driver4->getId(),
            $driver5->getId(),
            $driver6->getId(),
        ];

        // when
        $collection = $this->simulateQualifications->getQualificationsResults();

        // then
        self::assertCount(6, $collection->getQualificationResults());

        // and then (Positions are 1...6 and unique)
        self::assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], array_keys($collection->toPlainArray()));

        // and then (Each driver appears exactly once)
        $resultDriverIds = array_map(
            static fn(DriverDTO $driver) => $driver->getId(),
            $collection->toPlainArray(),
        );
        self::assertCount(6, array_unique($resultDriverIds));
        foreach ($resultDriverIds as $driverId) {
            self::assertTrue(in_array($driverId, $driversIds));
        }
    }

    #[Test]
    public function it_handles_single_driver_correctly(): void
    {
        // given (Build 1 team with 1 driver)
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // when
        $collection = $this->simulateQualifications->getQualificationsResults();

        // then
        self::assertCount(1, $collection->getQualificationResults());
        self::assertEquals(1, $collection->getQualificationResults()[0]->getPosition());
        self::assertEquals($driver->getId(), $collection->getQualificationResults()[0]->getDriver()->getId());
    }
}

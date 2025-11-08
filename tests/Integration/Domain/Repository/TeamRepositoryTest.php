<?php

declare(strict_types=1);

namespace Integration\Domain\Repository;

use Domain\Repository\TeamRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class TeamRepositoryTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private TeamRepository $teamRepository;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->teamRepository = self::getContainer()->get(TeamRepository::class);
    }

    #[Test]
    public function it_checks_if_teams_will_be_returned_based_on_drivers_ids(): void
    {
        // given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $alphaTauri = $this->fixtures->aTeamWithName('Alpha Tauri');

        // and given
        $driver1 = $this->fixtures->aDriver('John', 'Doe', $mercedes, 25);
        $driver2 = $this->fixtures->aDriver('Kayle', 'Smith', $mercedes, 47);
        $this->fixtures->aDriver('Mark', 'Garrick', $redBull, 88);
        $this->fixtures->aDriver('Bran', 'Russell', $redBull, 1);
        $this->fixtures->aDriver('Mike', 'Hamilton', $ferrari, 4);
        $this->fixtures->aDriver('Kevin', 'Leclerc', $ferrari, 12);
        $this->fixtures->aDriver('Jason', 'Alonso', $alphaTauri, 55);
        $driver8 = $this->fixtures->aDriver('Jake', 'Lawson', $alphaTauri, 90);

        // when
        $result = $this->teamRepository->getTeamsByDriversIds([
            $driver1->getId(),
            $driver2->getId(),
            $driver8->getId(),
        ]);

        // then
        self::assertEquals([$mercedes, $alphaTauri], $result);
    }

    #[Test]
    public function it_checks_if_all_teams_will_be_returned_with_drivers(): void
    {
        // given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $alphaTauri = $this->fixtures->aTeamWithName('Alpha Tauri');
        $haas = $this->fixtures->aTeamWithName('Haas');

        // and given
        $this->fixtures->aDriver('John', 'Doe', $mercedes, 25);
        $this->fixtures->aDriver('Kayle', 'Smith', $mercedes, 47);
        $this->fixtures->aDriver('Mark', 'Garrick', $redBull, 88);
        $this->fixtures->aDriver('Bran', 'Russell', $redBull, 1);
        $this->fixtures->aDriver('Mike', 'Hamilton', $ferrari, 4);
        $this->fixtures->aDriver('Kevin', 'Leclerc', $ferrari, 12);
        $this->fixtures->aDriver('Jason', 'Alonso', $alphaTauri, 55);
        $this->fixtures->aDriver('Jake', 'Lawson', $alphaTauri, 90);
        $this->fixtures->aDriver('Tomy', 'Massa', $haas, 9);
        $this->fixtures->aDriver('Carlos', 'Sainz', $haas, 19);

        // when
        $result = $this->teamRepository->getTeamsWithDrivers();

        // then
        self::assertCount(5, $result);
        self::assertEquals($mercedes, $result[0]);
        self::assertEquals($redBull, $result[1]);
        self::assertEquals($ferrari, $result[2]);
        self::assertEquals($alphaTauri, $result[3]);
        self::assertEquals($haas, $result[4]);

        // and then
        self::assertCount(2, $result[0]->getDrivers());
    }
}

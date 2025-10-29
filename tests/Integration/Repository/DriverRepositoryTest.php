<?php

declare(strict_types=1);

namespace Tests\Integration\Repository;

use Domain\Repository\DriverRepository;
use Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DriverRepositoryTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private DriverRepository $driverRepository;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->driverRepository = self::getContainer()->get(DriverRepository::class);
    }

    #[Test]
    public function it_checks_if_drivers_will_be_returned_with_fully_fetched_teams(): void
    {
        // given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $alphaTauri = $this->fixtures->aTeamWithName('Alpha Tauri');

        // and given
        $driver1 = $this->fixtures->aDriver('John', 'Doe', $mercedes, 25);
        $driver2 = $this->fixtures->aDriver('Kayle', 'Smith', $mercedes, 47);
        $driver3 = $this->fixtures->aDriver('Mark', 'Garrick', $redBull, 88);
        $this->fixtures->aDriver('Bran', 'Russell', $redBull, 1);
        $this->fixtures->aDriver('Mike', 'Hamilton', $ferrari, 4);
        $this->fixtures->aDriver('Kevin', 'Leclerc', $ferrari, 12);
        $this->fixtures->aDriver('Jason', 'Alonso', $alphaTauri, 55);
        $driver4 = $this->fixtures->aDriver('Jake', 'Lawson', $alphaTauri, 90);

        // when
        $result = $this->driverRepository->getDriversWithTeams([
            $driver1->getId(),
            $driver2->getId(),
            $driver3->getId(),
            $driver4->getId(),
        ]);

        // then
        self::assertEquals([$driver1, $driver2, $driver3, $driver4], $result);
    }

    #[Test]
    public function it_checks_if_all_drivers_will_be_returned_with_fully_fetched_teams(): void
    {
        // given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $alphaTauri = $this->fixtures->aTeamWithName('Alpha Tauri');

        // and given
        $driver1 = $this->fixtures->aDriver('John', 'Doe', $mercedes, 25);
        $driver2 = $this->fixtures->aDriver('Kayle', 'Smith', $mercedes, 47);
        $driver3 = $this->fixtures->aDriver('Mark', 'Garrick', $redBull, 88);
        $driver4 = $this->fixtures->aDriver('Bran', 'Russell', $redBull, 1);
        $driver5 = $this->fixtures->aDriver('Mike', 'Hamilton', $ferrari, 4);
        $driver6 = $this->fixtures->aDriver('Jake', 'Lawson', $alphaTauri, 90);

        // when
        $result = $this->driverRepository->getDriversWithTeams([
            $driver1->getId(),
            $driver2->getId(),
            $driver3->getId(),
            $driver4->getId(),
            $driver5->getId(),
            $driver6->getId(),
        ]);

        // then
        self::assertEquals([$driver1, $driver2, $driver3, $driver4, $driver5, $driver6], $result);
    }
}

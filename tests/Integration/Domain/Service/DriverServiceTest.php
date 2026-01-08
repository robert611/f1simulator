<?php

declare(strict_types=1);

namespace Integration\Domain\Service;

use Domain\Service\DriverService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class DriverServiceTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private DriverService $driverService;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->driverService = self::getContainer()->get(DriverService::class);
    }

    #[Test]
    public function driver_will_be_updated(): void
    {
        // given
        $williams = $this->fixtures->aTeamWithName('Williams');
        $hass = $this->fixtures->aTeamWithName('Hass');

        // and given
        $driver = $this->fixtures->aDriver('Josh', 'Smith', $williams, 42);

        // when
        $this->driverService->update($driver->getId(), 'Michael', 'Westen', $hass->getId(), 55);

        // then
        self::assertSame('Michael', $driver->getName());
        self::assertSame('Westen', $driver->getSurname());
        self::assertEquals($hass, $driver->getTeam());
        self::assertEquals(55, $driver->getCarNumber());

        // and then
        self::assertEquals($hass->getDrivers()->toArray(), [$driver]);
        self::assertEmpty($williams->getDrivers()->toArray());
    }
}

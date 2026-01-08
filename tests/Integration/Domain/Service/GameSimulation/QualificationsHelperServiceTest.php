<?php

declare(strict_types=1);

namespace Integration\Domain\Service\GameSimulation;

use Domain\Contract\Configuration\TeamsStrength;
use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\GameSimulation\QualificationsHelperService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class QualificationsHelperServiceTest extends KernelTestCase
{
    private Fixtures $fixtures;

    private QualificationsHelperService $qualificationsHelperService;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->qualificationsHelperService = self::getContainer()->get(QualificationsHelperService::class);
    }

    #[Test]
    public function it_checks_if_coupons_will_be_generated(): void
    {
        // given
        $teamsStrength = TeamsStrength::getTeamsStrength();

        // when
        $coupons = $this->qualificationsHelperService->generateCoupons();

        // then
        self::assertIsString($coupons[0]);

        // and then
        $countedValues = array_count_values($coupons);
        self::assertTrue($countedValues['Mercedes'] >= $teamsStrength['Mercedes']);
        self::assertEqualsCanonicalizing(array_keys($teamsStrength), array_keys($countedValues));
    }

    #[Test]
    public function it_returns_true_when_both_drivers_from_team_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Joe', 'Doe', $team, 55);
        $driver2 = $this->fixtures->aDriver('John', 'Done', $team, 30);

        // and given
        $team->addDriver($driver1);
        $team->addDriver($driver2);

        // when
        $result = $this->qualificationsHelperService->checkIfBothDriversFromATeamAlreadyFinished(
            'ferrari',
            DriverDTO::fromEntityCollection([$driver1, $driver2]),
        );

        // then
        self::assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_less_than_two_drivers_from_team_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Joe', 'Doe', $team, 55);
        $driver2 = $this->fixtures->aDriver('John', 'Done', $team, 30);

        // and given
        $team->addDriver($driver1);
        $team->addDriver($driver2);

        // when
        $resultNone = $this->qualificationsHelperService->checkIfBothDriversFromATeamAlreadyFinished(
            $team->getName(),
            [],
        );
        $resultOne = $this->qualificationsHelperService->checkIfBothDriversFromATeamAlreadyFinished(
            $team->getName(),
            DriverDTO::fromEntityCollection([$driver1]),
        );

        // then
        self::assertFalse($resultNone);
        self::assertFalse($resultOne);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_null_when_team_has_no_drivers_in_league(): void
    {
        // given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $driver = $this->fixtures->aDriver('Max', 'Verstappen', $redBull, 33);

        // when
        $result = $this->qualificationsHelperService->drawDriverFromATeam(
            $mercedes->getName(),
            DriverDTO::fromEntityCollection([$driver]),
            [],
        );

        // then
        self::assertNull($result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_unfinished_single_driver(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 16);
        $driverDTO = DriverDTO::fromEntity($driver);

        // when
        $result = $this->qualificationsHelperService->drawDriverFromATeam(
            'Ferrari',
            [$driverDTO],
            [],
        );

        // then
        self::assertSame($driverDTO, $result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_null_when_single_driver_already_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Carlos', 'Sainz', $team, 55);

        // when
        $result = $this->qualificationsHelperService->drawDriverFromATeam(
            'Ferrari',
            DriverDTO::fromEntityCollection([$driver]),
            DriverDTO::fromEntityCollection([$driver]),
        );

        // then
        self::assertNull($result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_one_of_drivers_when_both_unfinished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Mclaren');
        $driver1 = $this->fixtures->aDriver('Lando', 'Norris', $team, 4);
        $driver2 = $this->fixtures->aDriver('Oscar', 'Piastri', $team, 81);

        // when
        $result = $this->qualificationsHelperService->drawDriverFromATeam(
            'McLaren',
            DriverDTO::fromEntityCollection([$driver1, $driver2]),
            [],
        );

        // then
        self::assertTrue(in_array($result->getId(), [$driver1->getId(), $driver2->getId()], true));
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_unfinished_driver_when_only_one_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Aston Martin');
        $driver1 = $this->fixtures->aDriver('Fernando', 'Alonso', $team, 14);
        $driver2 = $this->fixtures->aDriver('Lance', 'Stroll', $team, 18);

        // when
        $result = $this->qualificationsHelperService->drawDriverFromATeam(
            'Aston Martin',
            DriverDTO::fromEntityCollection([$driver1, $driver2]),
            DriverDTO::fromEntityCollection([$driver1]),
        );

        // then
        self::assertEquals(DriverDTO::fromEntity($driver2), $result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_is_case_insensitive_by_team_name(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('AlphaTauri');
        $driver = $this->fixtures->aDriver('Yuki', 'Tsunoda', $team, 22);

        // when
        $result = $this->qualificationsHelperService->drawDriverFromATeam(
            'alphatauri',
            DriverDTO::fromEntityCollection([$driver]),
            [],
        );

        // then
        self::assertEquals(DriverDTO::fromEntity($driver), $result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_null_when_both_team_drivers_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Williams');
        $driver1 = $this->fixtures->aDriver('Alex', 'Albon', $team, 23);
        $driver2 = $this->fixtures->aDriver('Logan', 'Sargeant', $team, 2);

        // when
        $result = $this->qualificationsHelperService->drawDriverFromATeam(
            'Williams',
            DriverDTO::fromEntityCollection([$driver1, $driver2]),
            DriverDTO::fromEntityCollection([$driver1, $driver2]),
        );

        // then
        self::assertNull($result);
    }
}

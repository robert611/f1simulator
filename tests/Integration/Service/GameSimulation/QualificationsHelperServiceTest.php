<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\GameSimulation;

use App\Model\Configuration\TeamsStrength;
use App\Service\GameSimulation\QualificationsHelperService;
use App\Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
        self::assertIsArray($coupons);
        self::assertIsString($coupons[0]);

        // and then
        $countedValues = array_count_values($coupons);
        self::assertTrue($countedValues['mercedes'] >= $teamsStrength['mercedes']);
        self::assertEqualsCanonicalizing(array_keys($teamsStrength), array_keys($countedValues));
    }

    #[Test]
    public function it_returns_true_when_both_drivers_from_team_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('ferrari');
        $driver1 = $this->fixtures->aDriver('Joe', 'Doe', $team, 55);
        $driver2 = $this->fixtures->aDriver('John', 'Done', $team,30);

        // and given
        $team->addDriver($driver1);
        $team->addDriver($driver2);

        // when
        $result = $this->qualificationsHelperService->checkIfBothDriversFromATeamAlreadyFinished(
            'ferrari',
            [$driver1, $driver2],
        );

        // then
        self::assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_less_than_two_drivers_from_team_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('ferrari');
        $driver1 = $this->fixtures->aDriver('Joe', 'Doe', $team, 55);
        $driver2 = $this->fixtures->aDriver('John', 'Done', $team,30);

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
            [$driver1]
        );

        // then
        self::assertFalse($resultNone);
        self::assertFalse($resultOne);
    }
}

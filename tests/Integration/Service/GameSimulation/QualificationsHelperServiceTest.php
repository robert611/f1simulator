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
    }
}
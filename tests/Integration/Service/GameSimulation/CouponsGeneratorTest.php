<?php

declare(strict_types=1);

namespace Tests\Integration\Service\GameSimulation;

use Domain\Model\Configuration\QualificationAdvantage;
use Domain\Model\Configuration\TeamsStrength;
use Domain\Service\GameSimulation\CouponsGenerator;
use PHPUnit\Framework\Attributes\Test;
use Tests\Common\Fixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CouponsGeneratorTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private CouponsGenerator $couponsGenerator;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->couponsGenerator = self::getContainer()->get(CouponsGenerator::class);
    }

    #[Test]
    public function it_returns_empty_coupons_when_no_qualification_results_provided(): void
    {
        // given (empty qualification results array)
        $qualificationResults = [];

        // when
        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults);

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
        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults);

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
        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults);

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
        $expectedCouponCount = (int) ($expectedStrength * $this->couponsGenerator->multiplier);

        // when
        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults);

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
        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults);

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
        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults);

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
        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults);

        // then
        self::assertNotEmpty($coupons);

        // All coupons should be for the same driver
        foreach ($coupons as $coupon) {
            self::assertEquals($driver->getId(), $coupon);
        }
    }
}

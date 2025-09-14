<?php 

declare(strict_types=1);

namespace App\Tests\Integration\Service\Classification;

use App\Entity\Driver;
use App\Entity\Qualification;
use App\Model\Configuration\RaceScoringSystem;
use App\Model\DriversClassification;
use App\Service\Classification\ClassificationType;
use App\Service\Classification\SeasonClassifications;
use App\Tests\Common\Fixtures;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeasonClassificationsTest extends KernelTestCase 
{
    private Fixtures $fixtures;
    private SeasonClassifications $seasonClassifications;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->seasonClassifications = self::getContainer()->get(SeasonClassifications::class);
    }

    #[Test]
    public function it_checks_if_race_classification_will_be_returned_based_on_type(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team, 55);
        $driver2 = $this->fixtures->aDriver('Mike', 'Ross', $team, 80);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $this->fixtures->aRaceResult(5, $race1, $driver1);
        $this->fixtures->aRaceResult(9, $race1, $driver2);

        $classification = $this->seasonClassifications->getClassificationBasedOnType(
            $season,
            ClassificationType::RACE,
            $race1->getId(),
        );

        $this->assertCount(2, $classification);
        $this->assertIsArray($classification);
    }

    #[Test]
    public function it_checks_if_race_qualifications_will_be_returned_based_on_type(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team, 55);
        $driver2 = $this->fixtures->aDriver('Mike', 'Ross', $team, 80);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $this->fixtures->aRaceResult(5, $race1, $driver1);
        $this->fixtures->aRaceResult(9, $race1, $driver2);
        $this->fixtures->aQualification($driver1, $race1, 1);
        $this->fixtures->aQualification($driver2, $race1, 2);

        $classification = $this->seasonClassifications->getClassificationBasedOnType(
            $season,
            ClassificationType::QUALIFICATIONS,
            $race1->getId(),
        );

        $this->assertInstanceOf(Collection::class, $classification);
        $this->assertEquals(2, $classification->count());
    }

    #[Test]
    public function it_checks_if_drivers_classification_will_be_returned_based_on_type(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team, 55);
        $driver2 = $this->fixtures->aDriver('Mike', 'Ross', $team, 80);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $this->fixtures->aRaceResult(5, $race1, $driver1);
        $this->fixtures->aRaceResult(9, $race1, $driver2);

        $classification = $this->seasonClassifications->getClassificationBasedOnType(
            $season,
            ClassificationType::DRIVERS,
            $race1->getId(),
        );

        $this->assertInstanceOf(DriversClassification::class, $classification);
        $this->assertCount(2, $classification->getDriversRaceResults());
    }

    #[Test]
    public function it_checks_if_get_race_classification_returns_correct_results(): void
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType('race');
        $raceScoringSystem = RaceScoringSystem::getRaceScoringSystem();

        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->getPosition(), range(1, 20)));
            $this->assertEquals($result->getPoints(), $raceScoringSystem[$result->getPosition()]);
        }
    }

    #[Test]
    public function it_checks_if_get_qualifications_classification_returns_correct_results(): void
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType('qualifications');
      
        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->getPosition(), range(1, 20)));
            $this->assertTrue($result instanceof Qualification);
        }
    }

    #[Test]
    public function it_checks_if_get_drivers_classification_return_correct_results(): void
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType('drivers');
        $raceScoringSystem = RaceScoringSystem::getRaceScoringSystem();

        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->position, range(1, 20)));
            $this->assertEquals($result->getPoints(), 6 * $raceScoringSystem[$result->getPosition()]);
        }
    }

    public static function provideClassificationTypes(): array
    {
        return [
            [ClassificationType::RACE],
            [ClassificationType::QUALIFICATIONS],
            [ClassificationType::DRIVERS],
        ];
    }
}

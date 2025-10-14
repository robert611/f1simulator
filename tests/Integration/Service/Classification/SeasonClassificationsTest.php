<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Classification;

use App\Entity\Qualification;
use App\Model\DriversClassification;
use App\Service\Classification\ClassificationType;
use App\Service\Classification\SeasonClassifications;
use App\Tests\Common\Fixtures;
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

        // and given
        $team1 = $this->fixtures->aTeamWithName('Ferrari');
        $team2 = $this->fixtures->aTeamWithName('Mercedes');

        // and given
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team1, 55);
        $driver2 = $this->fixtures->aDriver('Mike', 'Ross', $team1, 80);
        $driver3 = $this->fixtures->aDriver('John', 'Speed', $team2, 23);
        $driver4 = $this->fixtures->aDriver('Mike', 'Ross', $team2, 37);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);
        $this->fixtures->aRaceResult(5, $race1, $driver1);
        $this->fixtures->aRaceResult(9, $race1, $driver2);
        $this->fixtures->aRaceResult(1, $race2, $driver3);
        $this->fixtures->aRaceResult(3, $race2, $driver4);

        // when
        $classification = $this->seasonClassifications->getClassificationBasedOnType(
            $season,
            ClassificationType::RACE,
            $race1->getId(),
        );

        // then
        $this->assertInstanceOf(DriversClassification::class, $classification);
        $this->assertCount(2, $classification->getDriversRaceResults());
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

        // when
        $classification = $this->seasonClassifications->getClassificationBasedOnType(
            $season,
            ClassificationType::QUALIFICATIONS,
            $race1->getId(),
        );

        // then
        $this->assertIsArray($classification);
        $this->assertCount(2, $classification);
        $this->assertInstanceOf(Qualification::class, $classification[0]);
        $this->assertInstanceOf(Qualification::class, $classification[1]);
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

        // when
        $classification = $this->seasonClassifications->getClassificationBasedOnType(
            $season,
            ClassificationType::DRIVERS,
            $race1->getId(),
        );

        // then
        $this->assertInstanceOf(DriversClassification::class, $classification);
        $this->assertCount(2, $classification->getDriversRaceResults());
    }

    #[Test]
    public function it_checks_if_default_drivers_classification_will_be_returned(): void
    {
        // and given
        $team1 = $this->fixtures->aTeamWithName('Ferrari');
        $team2 = $this->fixtures->aTeamWithName('Mercedes');

        // and given
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team1, 55);
        $driver2 = $this->fixtures->aDriver('Mike', 'Ross', $team1, 80);
        $driver3 = $this->fixtures->aDriver('John', 'Speed', $team2, 23);
        $driver4 = $this->fixtures->aDriver('Mike', 'Ross', $team2, 37);

        // when
        $classification = $this->seasonClassifications->getDefaultDriversClassification();

        // then
        $this->assertInstanceOf(DriversClassification::class, $classification);
        $this->assertCount(4, $classification->getDriversRaceResults());

        // and then
        $this->assertEquals($driver1->getId(), $classification->getDriversRaceResults()[0]->getDriver()->getId());
        $this->assertEquals(1, $classification->getDriversRaceResults()[0]->getPosition());
        $this->assertEquals(0, $classification->getDriversRaceResults()[0]->getPoints());

        $this->assertEquals($driver2->getId(), $classification->getDriversRaceResults()[1]->getDriver()->getId());
        $this->assertEquals(2, $classification->getDriversRaceResults()[1]->getPosition());
        $this->assertEquals(0, $classification->getDriversRaceResults()[1]->getPoints());

        $this->assertEquals($driver3->getId(), $classification->getDriversRaceResults()[2]->getDriver()->getId());
        $this->assertEquals(3, $classification->getDriversRaceResults()[2]->getPosition());
        $this->assertEquals(0, $classification->getDriversRaceResults()[2]->getPoints());

        $this->assertEquals($driver4->getId(), $classification->getDriversRaceResults()[3]->getDriver()->getId());
        $this->assertEquals(4, $classification->getDriversRaceResults()[3]->getPosition());
        $this->assertEquals(0, $classification->getDriversRaceResults()[3]->getPoints());
    }

    #[Test]
    public function it_checks_if_race_classification_returns_correct_results(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $team1 = $this->fixtures->aTeamWithName('Ferrari');
        $team2 = $this->fixtures->aTeamWithName('Mercedes');
        $team3 = $this->fixtures->aTeamWithName('Haas');
        $team4 = $this->fixtures->aTeamWithName('Mclaren');

        // and given
        $driver1 = $this->fixtures->aDriver('Lewis', 'Hamilton', $team1, 33);
        $driver2 = $this->fixtures->aDriver('Yuki', 'Spider', $team1, 5);
        $driver3 = $this->fixtures->aDriver('Mike', 'Ross', $team2, 9);
        $driver4 = $this->fixtures->aDriver('Michael', 'Smith', $team2, 24);
        $driver5 = $this->fixtures->aDriver('Greg', 'House', $team3, 31);
        $driver6 = $this->fixtures->aDriver('John', 'Marcus', $team3, 50);
        $driver7 = $this->fixtures->aDriver('Thomas', 'Jackson', $team4, 63);
        $driver8 = $this->fixtures->aDriver('Taylor', 'Spears', $team4, 25);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);

        // and given
        $this->fixtures->aRaceResult(1, $race1, $driver8);
        $this->fixtures->aRaceResult(2, $race1, $driver7);
        $this->fixtures->aRaceResult(3, $race1, $driver6);
        $this->fixtures->aRaceResult(4, $race1, $driver1);
        $this->fixtures->aRaceResult(5, $race1, $driver2);
        $this->fixtures->aRaceResult(6, $race1, $driver3);
        $this->fixtures->aRaceResult(7, $race1, $driver4);
        $this->fixtures->aRaceResult(8, $race1, $driver5);

        // and given (This results should not be taken into account, as it's a second race)
        $this->fixtures->aRaceResult(8, $race2, $driver1);
        $this->fixtures->aRaceResult(7, $race2, $driver2);
        $this->fixtures->aRaceResult(6, $race2, $driver3);
        $this->fixtures->aRaceResult(5, $race2, $driver4);
        $this->fixtures->aRaceResult(4, $race2, $driver5);
        $this->fixtures->aRaceResult(3, $race2, $driver6);
        $this->fixtures->aRaceResult(2, $race2, $driver7);
        $this->fixtures->aRaceResult(1, $race2, $driver8);

        // when
        $classification = $this->seasonClassifications->getRaceClassification($season, $race1->getId());

        // then
        self::assertCount(8, $classification->getDriversRaceResults());

        self::assertEquals($driver8, $classification->getDriversRaceResults()[0]->getDriver());
        self::assertEquals(1, $classification->getDriversRaceResults()[0]->getPosition());
        self::assertEquals(25, $classification->getDriversRaceResults()[0]->getPoints());

        self::assertEquals($driver7, $classification->getDriversRaceResults()[1]->getDriver());
        self::assertEquals(2, $classification->getDriversRaceResults()[1]->getPosition());
        self::assertEquals(18, $classification->getDriversRaceResults()[1]->getPoints());

        self::assertEquals($driver6, $classification->getDriversRaceResults()[2]->getDriver());
        self::assertEquals(3, $classification->getDriversRaceResults()[2]->getPosition());
        self::assertEquals(15, $classification->getDriversRaceResults()[2]->getPoints());

        self::assertEquals($driver1, $classification->getDriversRaceResults()[3]->getDriver());
        self::assertEquals(4, $classification->getDriversRaceResults()[3]->getPosition());
        self::assertEquals(12, $classification->getDriversRaceResults()[3]->getPoints());

        self::assertEquals($driver2, $classification->getDriversRaceResults()[4]->getDriver());
        self::assertEquals(5, $classification->getDriversRaceResults()[4]->getPosition());
        self::assertEquals(10, $classification->getDriversRaceResults()[4]->getPoints());

        self::assertEquals($driver3, $classification->getDriversRaceResults()[5]->getDriver());
        self::assertEquals(6, $classification->getDriversRaceResults()[5]->getPosition());
        self::assertEquals(8, $classification->getDriversRaceResults()[5]->getPoints());

        self::assertEquals($driver4, $classification->getDriversRaceResults()[6]->getDriver());
        self::assertEquals(7, $classification->getDriversRaceResults()[6]->getPosition());
        self::assertEquals(6, $classification->getDriversRaceResults()[6]->getPoints());

        self::assertEquals($driver5, $classification->getDriversRaceResults()[7]->getDriver());
        self::assertEquals(8, $classification->getDriversRaceResults()[7]->getPosition());
        self::assertEquals(4, $classification->getDriversRaceResults()[7]->getPoints());
    }

    #[Test]
    public function it_checks_if_qualifications_classification_returns_correct_results(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $team1 = $this->fixtures->aTeamWithName('Ferrari');
        $team2 = $this->fixtures->aTeamWithName('Mercedes');
        $team3 = $this->fixtures->aTeamWithName('Haas');
        $team4 = $this->fixtures->aTeamWithName('Mclaren');

        // and given
        $driver1 = $this->fixtures->aDriver('Lewis', 'Hamilton', $team1, 33);
        $driver2 = $this->fixtures->aDriver('Yuki', 'Spider', $team1, 5);
        $driver3 = $this->fixtures->aDriver('Mike', 'Ross', $team2, 9);
        $driver4 = $this->fixtures->aDriver('Michael', 'Smith', $team2, 24);
        $driver5 = $this->fixtures->aDriver('Greg', 'House', $team3, 31);
        $driver6 = $this->fixtures->aDriver('John', 'Marcus', $team3, 50);
        $driver7 = $this->fixtures->aDriver('Thomas', 'Jackson', $team4, 63);
        $driver8 = $this->fixtures->aDriver('Taylor', 'Spears', $team4, 25);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);

        // and given
        $this->fixtures->aQualification($driver1, $race1, 1);
        $this->fixtures->aQualification($driver2, $race1, 2);
        $this->fixtures->aQualification($driver3, $race1, 3);
        $this->fixtures->aQualification($driver4, $race1, 4);
        $this->fixtures->aQualification($driver5, $race1, 5);
        $this->fixtures->aQualification($driver6, $race1, 6);
        $this->fixtures->aQualification($driver7, $race1, 7);
        $this->fixtures->aQualification($driver8, $race1, 8);

        // and given
        $this->fixtures->aQualification($driver1, $race2, 8);
        $this->fixtures->aQualification($driver2, $race2, 7);
        $this->fixtures->aQualification($driver3, $race2, 6);
        $this->fixtures->aQualification($driver4, $race2, 5);
        $this->fixtures->aQualification($driver5, $race2, 4);
        $this->fixtures->aQualification($driver6, $race2, 3);
        $this->fixtures->aQualification($driver7, $race2, 2);
        $this->fixtures->aQualification($driver8, $race2, 1);

        // when
        $classification = $this->seasonClassifications->getQualificationsClassification($season, $race2->getId());

        // then
        self::assertEquals(8, count($classification));
        self::assertEquals($driver8, $classification[0]->getDriver());
        self::assertEquals($driver7, $classification[1]->getDriver());
        self::assertEquals($driver6, $classification[2]->getDriver());
        self::assertEquals($driver5, $classification[3]->getDriver());
        self::assertEquals($driver4, $classification[4]->getDriver());
        self::assertEquals($driver3, $classification[5]->getDriver());
        self::assertEquals($driver2, $classification[6]->getDriver());
        self::assertEquals($driver1, $classification[7]->getDriver());
    }

    #[Test]
    public function it_checks_if_drivers_classification_returns_correct_results(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $team1 = $this->fixtures->aTeamWithName('Ferrari');
        $team2 = $this->fixtures->aTeamWithName('Mercedes');
        $team3 = $this->fixtures->aTeamWithName('Haas');
        $team4 = $this->fixtures->aTeamWithName('Mclaren');

        // and given
        $driver1 = $this->fixtures->aDriver('Lewis', 'Hamilton', $team1, 33);
        $driver2 = $this->fixtures->aDriver('Yuki', 'Spider', $team1, 5);
        $driver3 = $this->fixtures->aDriver('Mike', 'Ross', $team2, 9);
        $driver4 = $this->fixtures->aDriver('Michael', 'Smith', $team2, 24);
        $driver5 = $this->fixtures->aDriver('Greg', 'House', $team3, 31);
        $driver6 = $this->fixtures->aDriver('John', 'Marcus', $team3, 50);
        $driver7 = $this->fixtures->aDriver('Thomas', 'Jackson', $team4, 63);
        $driver8 = $this->fixtures->aDriver('Taylor', 'Spears', $team4, 25);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);

        // and given
        $this->fixtures->aRaceResult(1, $race1, $driver1);
        $this->fixtures->aRaceResult(2, $race1, $driver2);
        $this->fixtures->aRaceResult(3, $race1, $driver3);
        $this->fixtures->aRaceResult(4, $race1, $driver4);
        $this->fixtures->aRaceResult(5, $race1, $driver5);
        $this->fixtures->aRaceResult(6, $race1, $driver6);
        $this->fixtures->aRaceResult(7, $race1, $driver7);
        $this->fixtures->aRaceResult(8, $race1, $driver8);

        // and given
        $this->fixtures->aRaceResult(8, $race2, $driver1);
        $this->fixtures->aRaceResult(7, $race2, $driver2);
        $this->fixtures->aRaceResult(6, $race2, $driver3);
        $this->fixtures->aRaceResult(5, $race2, $driver4);
        $this->fixtures->aRaceResult(4, $race2, $driver5);
        $this->fixtures->aRaceResult(3, $race2, $driver6);
        $this->fixtures->aRaceResult(2, $race2, $driver7);
        $this->fixtures->aRaceResult(1, $race2, $driver8);

        // when
        $classification = $this->seasonClassifications->getDriversClassification($season);

        // then
        self::assertCount(8, $classification->getDriversRaceResults());

        self::assertEquals($driver1, $classification->getDriversRaceResults()[0]->getDriver());
        self::assertEquals(1, $classification->getDriversRaceResults()[0]->getPosition());
        self::assertEquals(29, $classification->getDriversRaceResults()[0]->getPoints());

        self::assertEquals($driver8, $classification->getDriversRaceResults()[1]->getDriver());
        self::assertEquals(2, $classification->getDriversRaceResults()[1]->getPosition());
        self::assertEquals(29, $classification->getDriversRaceResults()[1]->getPoints());

        self::assertEquals($driver2, $classification->getDriversRaceResults()[2]->getDriver());
        self::assertEquals(3, $classification->getDriversRaceResults()[2]->getPosition());
        self::assertEquals(24, $classification->getDriversRaceResults()[2]->getPoints());

        self::assertEquals($driver7, $classification->getDriversRaceResults()[3]->getDriver());
        self::assertEquals(4, $classification->getDriversRaceResults()[3]->getPosition());
        self::assertEquals(24, $classification->getDriversRaceResults()[3]->getPoints());

        self::assertEquals($driver3, $classification->getDriversRaceResults()[4]->getDriver());
        self::assertEquals(5, $classification->getDriversRaceResults()[4]->getPosition());
        self::assertEquals(23, $classification->getDriversRaceResults()[4]->getPoints());

        self::assertEquals($driver6, $classification->getDriversRaceResults()[5]->getDriver());
        self::assertEquals(6, $classification->getDriversRaceResults()[5]->getPosition());
        self::assertEquals(23, $classification->getDriversRaceResults()[5]->getPoints());

        self::assertEquals($driver4, $classification->getDriversRaceResults()[6]->getDriver());
        self::assertEquals(7, $classification->getDriversRaceResults()[6]->getPosition());
        self::assertEquals(22, $classification->getDriversRaceResults()[6]->getPoints());

        self::assertEquals($driver5, $classification->getDriversRaceResults()[7]->getDriver());
        self::assertEquals(8, $classification->getDriversRaceResults()[7]->getPosition());
        self::assertEquals(22, $classification->getDriversRaceResults()[7]->getPoints());
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

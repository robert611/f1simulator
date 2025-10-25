<?php

declare(strict_types=1);

namespace Tests\Integration\Service\Classification;

use Tests\Common\Fixtures;
use Computer\Service\SeasonTeamsClassification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeasonTeamsClassificationTest extends KernelTestCase
{
    private SeasonTeamsClassification $seasonTeamsClassification;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->seasonTeamsClassification = self::getContainer()->get(SeasonTeamsClassification::class);
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    public function test_if_can_get_teams_classification(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $williams = $this->fixtures->aTeamWithName("Williams");
        $alphaTauri = $this->fixtures->aTeamWithName("Alpha tauri");
        $renault = $this->fixtures->aTeamWithName("Renault");

        // and given
        $kubica = $this->fixtures->aDriver('Robert', 'Kubica', $williams, 88);
        $russell = $this->fixtures->aDriver('George', 'Russell', $williams, 50);
        $alphaTauriDriver = $this->fixtures->aDriver('Pierre', 'Gasly', $alphaTauri, 100);
        $renaultDriver = $this->fixtures->aDriver('Fernando', 'Alonso', $renault, 40);

        // and given
        $season = $this->fixtures->aSeason($user, $kubica);

        // and given
        $track = $this->fixtures->aTrack("Monaco Grand Prix", "monaco.png");
        $race = $this->fixtures->aRace($track, $season);

        // and given (Williams drivers results)
        $this->fixtures->aRaceResult(1, $race, $kubica);
        $this->fixtures->aRaceResult(3, $race, $russell);

        // and given (Alpha Tauri drivers results)
        $this->fixtures->aRaceResult(2, $race, $alphaTauriDriver);

        // and given (Renault drivers results)
        $this->fixtures->aRaceResult(4, $race, $renaultDriver);

        // when
        $classification = $this->seasonTeamsClassification->getClassification($user->getId());

        // then
        self::assertEquals(3, count($classification->getTeamsSeasonResults()));

        // and then
        $firstPlaceTeam = $classification->getTeamsSeasonResults()[0];
        self::assertEquals($williams, $firstPlaceTeam->getTeam());
        self::assertEquals(1, $firstPlaceTeam->getPosition());
        self::assertEquals(40, $firstPlaceTeam->getPoints());

        // and then
        $secondPlaceTeam = $classification->getTeamsSeasonResults()[1];
        self::assertEquals($alphaTauri, $secondPlaceTeam->getTeam());
        self::assertEquals(2, $secondPlaceTeam->getPosition());
        self::assertEquals(18, $secondPlaceTeam->getPoints());

        // and then
        $thirdPlaceTeam = $classification->getTeamsSeasonResults()[2];
        self::assertEquals($renault, $thirdPlaceTeam->getTeam());
        self::assertEquals(3, $thirdPlaceTeam->getPosition());
        self::assertEquals(12, $thirdPlaceTeam->getPoints());
    }
}

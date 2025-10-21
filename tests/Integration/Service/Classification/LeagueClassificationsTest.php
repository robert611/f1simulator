<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Classification;

use App\Service\Classification\ClassificationType;
use App\Tests\Common\Fixtures;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Multiplayer\Entity\UserSeasonQualification;
use Multiplayer\Entity\UserSeasonRaceResult;
use Multiplayer\Service\LeagueClassifications;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LeagueClassificationsTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private LeagueClassifications $leagueClassifications;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->leagueClassifications = self::getContainer()->get(LeagueClassifications::class);
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    /**
     * @throws ORMException
     */
    #[Test]
    public function it_checks_if_players_positions_can_be_recalculated(): void
    {
        // given
        $userSeasonOwner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");
        $user1 = $this->fixtures->aCustomUser('johnXT', 'johnxt@gmail.com');
        $user2 = $this->fixtures->aCustomUser('maria212', 'maria212@gmail.com');
        $user3 = $this->fixtures->aCustomUser('greg455', 'greg455@gmail.com');
        $user4 = $this->fixtures->aCustomUser('mikey354', 'mikey354@gmail.com');

        // and given
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver("John", "Smith", $ferrari, 44);
        $driver2 = $this->fixtures->aDriver("Alex", "Apollo", $ferrari, 45);

        // and given
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $driver3 = $this->fixtures->aDriver("Yuki", "Grieg", $redBull, 46);
        $driver4 = $this->fixtures->aDriver("Michael", "Connor", $redBull, 47);

        // and given
        $hass = $this->fixtures->aTeamWithName('Hass');
        $driver5 = $this->fixtures->aDriver("Fernando", "Alonso", $hass, 48);

        // and given
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            10,
            $userSeasonOwner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $userSeasonPlayer1 = $this->fixtures->aUserSeasonPlayer($userSeason, $userSeasonOwner, $driver1);
        $userSeasonPlayer2 = $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver2);
        $userSeasonPlayer3 = $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver3);
        $userSeasonPlayer4 = $this->fixtures->aUserSeasonPlayer($userSeason, $user3, $driver4);
        $userSeasonPlayer5 = $this->fixtures->aUserSeasonPlayer($userSeason, $user4, $driver5);

        $userSeasonPlayer1->assignClassificationProperties(50, 1);
        $userSeasonPlayer2->assignClassificationProperties(17, 2);
        $userSeasonPlayer3->assignClassificationProperties(84, 4);
        $userSeasonPlayer4->assignClassificationProperties(2, 3);
        $userSeasonPlayer5->assignClassificationProperties(17, 5);

        // when
        $this->leagueClassifications->recalculatePlayersPositions($userSeason);

        // then
        $this->entityManager->refresh($userSeasonPlayer1);
        $this->entityManager->refresh($userSeasonPlayer2);
        $this->entityManager->refresh($userSeasonPlayer3);
        $this->entityManager->refresh($userSeasonPlayer4);

        self::assertEquals(2, $userSeasonPlayer1->getPosition());
        self::assertEquals(3, $userSeasonPlayer2->getPosition());
        self::assertEquals(1, $userSeasonPlayer3->getPosition());
        self::assertEquals(5, $userSeasonPlayer4->getPosition());
        self::assertEquals(4, $userSeasonPlayer5->getPosition());
    }

    #[Test]
    public function it_returns_players_classification_for_players_type(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('owner', 'owner@example.com');
        $user1 = $this->fixtures->aCustomUser('user1', 'user1@example.com');
        $user2 = $this->fixtures->aCustomUser('user2', 'user2@example.com');

        $team = $this->fixtures->aTeamWithName('Alpha Tauri');
        $driver1 = $this->fixtures->aDriver('John', 'Hamilton', $team, 11);
        $driver2 = $this->fixtures->aDriver('Clark', 'Magnussen', $team, 12);

        $userSeason = $this->fixtures->aUserSeason('League First', 10, $owner, 'Test League', false, true);

        $player1 = $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver1);
        $player2 = $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver2);

        $player1->assignClassificationProperties(25, 1);
        $player2->assignClassificationProperties(10, 2);

        // when
        $result = $this->leagueClassifications->getClassificationBasedOnType(
            $userSeason,
            ClassificationType::PLAYERS,
            null,
        );

        // then
        self::assertIsArray($result);
        self::assertCount(2, $result);
        self::assertEquals($player1, $result[0]);
        self::assertEquals($player2, $result[1]);
    }

    #[Test]
    public function it_returns_race_classification_for_race_type(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('owner', 'owner@example.com');
        $user1 = $this->fixtures->aCustomUser('user1', 'user1@example.com');
        $user2 = $this->fixtures->aCustomUser('user2', 'user2@example.com');

        $team = $this->fixtures->aTeamWithName('Alpha Tauri');
        $driver1 = $this->fixtures->aDriver('Mike', 'Ross', $team, 21);
        $driver2 = $this->fixtures->aDriver('John', 'MacQuire', $team, 22);

        $userSeason = $this->fixtures->aUserSeason('League 2', 10, $owner, 'Race League', false, true);
        $player1 = $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver1);
        $player2 = $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver2);

        $track = $this->fixtures->aTrack('Monza', 'monza.png');
        $race = $this->fixtures->aUserSeasonRace($track, $userSeason);

        $this->fixtures->aUserSeasonRaceResult(1, 25, $race, $player1);
        $this->fixtures->aUserSeasonRaceResult(2, 18, $race, $player2);

        // when
        $result = $this->leagueClassifications->getClassificationBasedOnType(
            $userSeason,
            ClassificationType::RACE,
            $race->getId(),
        );

        // then
        self::assertInstanceOf(Collection::class, $result);
        self::assertContainsOnlyInstancesOf(UserSeasonRaceResult::class, $result->toArray());
        self::assertEquals($player1, $result->toArray()[0]->getPlayer());
        self::assertEquals($player2, $result->toArray()[1]->getPlayer());
    }

    #[Test]
    public function it_returns_qualifications_for_qualifications_type(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('owner', 'owner@example.com');
        $user1 = $this->fixtures->aCustomUser('user1', 'user1@example.com');
        $user2 = $this->fixtures->aCustomUser('user2', 'user2@example.com');

        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Mikael', 'Smith', $team, 31);
        $driver2 = $this->fixtures->aDriver('Mark', 'Lock', $team, 32);

        $league = $this->fixtures->aUserSeason('League 3', 10, $owner, 'Qual League', false, true);
        $player1 = $this->fixtures->aUserSeasonPlayer($league, $user1, $driver1);
        $player2 = $this->fixtures->aUserSeasonPlayer($league, $user2, $driver2);

        $track = $this->fixtures->aTrack('Spa', 'spa.png');
        $race = $this->fixtures->aUserSeasonRace($track, $league);

        $qualification1 = $this->fixtures->aUserSeasonQualification($player1, $race, 1);
        $qualification2 = $this->fixtures->aUserSeasonQualification($player2, $race, 2);

        $this->entityManager->persist($qualification1);
        $this->entityManager->persist($qualification2);
        $this->entityManager->flush();

        // when
        $result = $this->leagueClassifications->getClassificationBasedOnType(
            $league,
            ClassificationType::QUALIFICATIONS,
            $race->getId(),
        );

        // then
        self::assertInstanceOf(Collection::class, $result);
        self::assertContainsOnlyInstancesOf(UserSeasonQualification::class, $result->toArray());
        self::assertEquals($player1, $result->toArray()[0]->getPlayer());
        self::assertEquals($player2, $result->toArray()[1]->getPlayer());
    }
}

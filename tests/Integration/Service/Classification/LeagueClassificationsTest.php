<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Classification;

use App\Entity\UserSeasonPlayer;
use App\Entity\UserSeasonQualification;
use App\Entity\UserSeasonRaceResult;
use App\Service\Classification\LeagueClassifications;
use App\Tests\Common\Fixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $ferrari = $this->fixtures->aTeamWithName('ferrari');
        $driver1 = $this->fixtures->aDriver("John", "Smith", $ferrari, 44);
        $driver2 = $this->fixtures->aDriver("Alex", "Apollo", $ferrari, 45);

        // and given
        $redBull = $this->fixtures->aTeamWithName('red bull');
        $driver3 = $this->fixtures->aDriver("Yuki", "Grieg", $redBull, 46);
        $driver4 = $this->fixtures->aDriver("Michael", "Connor", $redBull, 47);

        // and given
        $hass = $this->fixtures->aTeamWithName('hass');
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

    #[DataProvider('provideClassificationTypes')]
    #[Test]
    public function it_checks_if_can_get_classification_based_on_type(string $type): void
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType($type);

        $this->assertTrue(is_array($classification) || is_object($classification));
    }

    public function test_if_can_get_race_classification(): void
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType('race');

        foreach ($classification as $key => $result) {
            $this->assertTrue($result instanceof UserSeasonRaceResult);
            $this->assertTrue($result->getPoints() > 0);
            $this->assertTrue($result->getPosition() == ($key + 1));
        }
    }

    public function test_if_can_get_players_classification(): void
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType('players');

        $this->assertTrue($classification[0] instanceof UserSeasonPlayer);
        $this->assertTrue($classification[0]->getPoints() > 0);
    }

    public function test_if_can_get_qualifications_classification(): void
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType('qualifications');

        $this->assertTrue($classification[0] instanceof UserSeasonQualification);
    }

    public static function provideClassificationTypes(): array
    {
        return [
            ['race'],
            ['players'],
            ['qualifications'],
            ['notExistingOne']
        ];
    }
}

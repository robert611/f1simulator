<?php

declare(strict_types=1);

namespace Tests\Integration\Multiplayer\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Multiplayer\Repository\UserSeasonRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\FixedClock;
use Tests\Common\Fixtures;

class UserSeasonRepositoryTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private UserSeasonRepository $userSeasonRepository;
    private EntityManagerInterface $entityManager;
    private FixedClock $fixedClock;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userSeasonRepository = self::getContainer()->get(UserSeasonRepository::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->fixedClock = self::getContainer()->get(FixedClock::class);
    }

    #[Test]
    public function it_checks_if_user_seasons_will_be_returned(): void
    {
        // given
        $user1 = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");
        $user2 = $this->fixtures->aCustomUser('johnXT', 'johnxt@gmail.com');
        $user3 = $this->fixtures->aCustomUser('maria212', 'maria212@gmail.com');

        // and given
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $alfaRomeo = $this->fixtures->aTeamWithName('Alfa Romeo');
        $driver1 = $this->fixtures->aDriver("John", "Smith", $ferrari, 44);
        $driver2 = $this->fixtures->aDriver("Alex", "Apollo", $ferrari, 45);
        $driver3 = $this->fixtures->aDriver("Filip", "Masa", $alfaRomeo, 9);

        // and given
        $userSeason1 = $this->fixtures->aUserSeason(
            "J783NMS092C",
            10,
            $user1,
            "Liga szybkich kierowców 1",
            false,
            false,
        );
        $userSeason2 = $this->fixtures->aUserSeason(
            "J78332S012C",
            10,
            $user2,
            "Liga szybkich kierowców 2",
            false,
            false,
        );
        $userSeason3 = $this->fixtures->aUserSeason(
            "J713KLOS012C",
            10,
            $user3,
            "Liga szybkich kierowców 3",
            false,
            false,
        );

        // and given
        $this->fixtures->aUserSeasonPlayer($userSeason1, $user1, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason1, $user3, $driver3);
        $this->fixtures->aUserSeasonPlayer($userSeason2, $user2, $driver2);
        $this->fixtures->aUserSeasonPlayer($userSeason3, $user3, $driver3);
        $this->fixtures->aUserSeasonPlayer($userSeason3, $user1, $driver1);


        // when
        $result = $this->userSeasonRepository->getUserSeasons($user1->getId());

        // then
        self::assertEquals([$userSeason1, $userSeason3], $result);
    }

    #[Test]
    public function last_12_months_seasons_played_will_be_returned(): void
    {
        // given
        $user1 = $this->fixtures->aCustomUser("frank789", "frank789@gmail.com");
        $user2 = $this->fixtures->aCustomUser('ross9241', 'ross9241@gmail.com');
        $user3 = $this->fixtures->aCustomUser('chandler831156', 'chandler831156@gmail.com');

        // and given
        $userSeason1 = $this->fixtures->aUserSeason(
            "KD83NMS092C",
            10,
            $user1,
            "Liga szybkich kierowców 1",
            false,
            true,
        );
        $userSeason2 = $this->fixtures->aUserSeason(
            "FK8332S012C",
            10,
            $user2,
            "Liga szybkich kierowców 2",
            false,
            true,
        );
        $userSeason3 = $this->fixtures->aUserSeason(
            "J713KLOS012C",
            10,
            $user3,
            "Liga szybkich kierowców 3",
            false,
            true,
        );
        $userSeason4 = $this->fixtures->aUserSeason(
            "C71OKLFS014C",
            10,
            $user3,
            "Liga szybkich kierowców 4",
            false,
            true,
        );
        $this->fixtures->aUserSeason(
            "RL13KIO5S01DC",
            10,
            $user3,
            "Liga szybkich kierowców 5",
            false,
            true,
        );

        // and given
        $userSeason1->end(new DateTimeImmutable('2025-09-10 10:00:00'));
        $userSeason2->end(new DateTimeImmutable('2025-04-10 12:30:00'));
        $userSeason3->end(new DateTimeImmutable('2024-12-03 17:50:00'));
        $userSeason4->end(new DateTimeImmutable('2024-10-21 17:50:00'));

        // and given
        $this->entityManager->flush();

        // and given
        $this->fixedClock->setNow('2025-10-10 10:00:00');

        // when
        $result = $this->userSeasonRepository->getLast12MonthsSeasonsPlayed();

        // then
        self::assertCount(3, $result);
        self::assertSame(['month' => 12, 'year' => 2024, 'seasonsPlayed' => 1], $result[0]);
        self::assertSame(['month' => 4, 'year' => 2025, 'seasonsPlayed' => 1], $result[1]);
        self::assertSame(['month' => 9, 'year' => 2025, 'seasonsPlayed' => 1], $result[2]);
    }
}

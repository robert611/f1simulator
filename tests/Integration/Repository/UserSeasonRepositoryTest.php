<?php

declare(strict_types=1);

namespace Tests\Integration\Repository;

use Multiplayer\Repository\UserSeasonRepository;
use Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserSeasonRepositoryTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private UserSeasonRepository $userSeasonRepository;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userSeasonRepository = self::getContainer()->get(UserSeasonRepository::class);
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
}

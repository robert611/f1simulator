<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Domain\Entity\Driver;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Security\Entity\User;

class UserSeasonPlayersFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $players = $this->getPlayers();

        foreach ($players as $key => $data) {
            $season = $this->getReference('userSeason.' . $data['season_id'], UserSeason::class);
            $user = $this->getReference('user.' . $data['user_id'], User::class);
            $driver = $this->getReference('driver.' . $data['driver_id'], Driver::class);

            $player = UserSeasonPlayer::create($season, $user, $driver->getId());

            $manager->persist($player);
            $manager->flush();

            $this->addReference('league_player.' . ($key + 1), $player);
        }
    }

    public function getDependencies(): array
    {
        return array(
            DriverFixtures::class,
            UserSeasonFixtures::class
        );
    }

    public function getPlayers(): array
    {
        return [
            /* There should be an owner of the league also added as a player */
            ['season_id' => 1, 'user_id' => 1, 'driver_id' => 2],
            ['season_id' => 1, 'user_id' => 5, 'driver_id' => 1],
            ['season_id' => 1, 'user_id' => 2, 'driver_id' => 10],
            ['season_id' => 1, 'user_id' => 3, 'driver_id' => 15],
            ['season_id' => 1, 'user_id' => 4, 'driver_id' => 20],

            ['season_id' => 2, 'user_id' => 2, 'driver_id' => 1],
            ['season_id' => 2, 'user_id' => 1, 'driver_id' => 2],
            ['season_id' => 2, 'user_id' => 4, 'driver_id' => 4],
            ['season_id' => 2, 'user_id' => 3, 'driver_id' => 6],

            ['season_id' => 3, 'user_id' => 3, 'driver_id' => 1],
            ['season_id' => 3, 'user_id' => 5, 'driver_id' => 19],
            ['season_id' => 3, 'user_id' => 4, 'driver_id' => 9]
        ];
    }
}

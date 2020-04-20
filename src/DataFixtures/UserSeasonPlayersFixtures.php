<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\UserSeasonPlayers;
use App\DataFixtures\DriverFixtures;
use App\DataFixtures\UserSeasonFixtures;

class UserSeasonPlayersFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $players = $this->getPlayers(); 

        foreach ($players as $key => $data) {
            $player = new UserSeasonPlayers();

            $player->setSeason($this->getReference('userSeason.' . $data['season_id']));
            $player->setUser($this->getReference('user.' . $data['user_id']));
            $player->setDriver($this->getReference('driver.' . $data['driver_id']));

            $manager->persist($player);
            $manager->flush();

            $this->addReference('league_player.' . ($key + 1), $player);
        }
    }

    public function getDependencies()
    {
        return array(
            DriverFixtures::class,
            UserSeasonFixtures::class
        );
    }

    public function getPlayers()
    {
        return [
            ['season_id' => 1, 'user_id' => 5, 'driver_id' => 1],
            ['season_id' => 1, 'user_id' => 2, 'driver_id' => 10],
            ['season_id' => 1, 'user_id' => 3, 'driver_id' => 15],
            ['season_id' => 1, 'user_id' => 4, 'driver_id' => 20],

            ['season_id' => 2, 'user_id' => 1, 'driver_id' => 2],
            ['season_id' => 2, 'user_id' => 4, 'driver_id' => 4],
            ['season_id' => 2, 'user_id' => 3, 'driver_id' => 6],

            ['season_id' => 3, 'user_id' => 5, 'driver_id' => 19],
            ['season_id' => 3, 'user_id' => 4, 'driver_id' => 9]
        ];
    }
}

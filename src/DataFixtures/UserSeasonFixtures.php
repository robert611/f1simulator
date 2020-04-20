<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\UserSeason;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserSeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $seasons = $this->getSeasons();

        foreach ($seasons as $key => $data) {
            $userSeason = new UserSeason();

            $userSeason->setOwner($this->getReference('user.' . $data['owner_id']));
            $userSeason->setSecret($data['secret']);
            $userSeason->setMaxPlayers($data['max_players']);
            $userSeason->setName($data['name']);
            $userSeason->setCompleted($data['completed']);
            $userSeason->setStarted($data['started']);

            $manager->persist($userSeason);
            $manager->flush();

            $this->addReference('userSeason.' . ($key + 1), $userSeason);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }

    public function getSeasons()    
    {
        return [
            ['owner_id' => 1, 'secret' => 'FH42H78XO1S1', 'max_players' => 15, 'name' => 'Liga Brunatnych kapust', 'completed' => 1, 'started' => 1],
            ['owner_id' => 2, 'secret' => 'HJRTY1B5X99A', 'max_players' => 20, 'name' => 'Rocket League', 'completed' => 0, 'started' => 1],
            ['owner_id' => 3, 'secret' => 'B32X8FOP01XX', 'max_players' => 2, 'name' => 'Liga Jabłek Brzoskwiniowych', 'completed' => 0, 'started' => 0]
        ];
    }
}

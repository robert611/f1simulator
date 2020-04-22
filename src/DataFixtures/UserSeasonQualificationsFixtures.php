<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\UserSeasonQualifications;

class UserSeasonQualificationsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $qualifications = $this->getQualifications();

        foreach ($qualifications as $key => $data) {
            $qualification = new UserSeasonQualifications();

            $qualification->setPlayer($this->getReference('league_player.' . $data['player_id']));
            $qualification->setRace($this->getReference('league_race.' . $data['race_id']));
            $qualification->setPosition($data['position']);

            $manager->persist($qualification);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return array(
            UserSeasonRacesFixtures::class
        );
    }

    private function getQualifications()
    {
        return [
            // Season id = 1, players = 5
            ['player_id' => '1', 'race_id' => '1', 'position' => 1],
            ['player_id' => '2', 'race_id' => '1', 'position' => 2],
            ['player_id' => '3', 'race_id' => '1', 'position' => 3],
            ['player_id' => '4', 'race_id' => '1', 'position' => 4],
            ['player_id' => '5', 'race_id' => '1', 'position' => 5],


            ['player_id' => '1', 'race_id' => '2', 'position' => 1],
            ['player_id' => '2', 'race_id' => '2', 'position' => 2],
            ['player_id' => '3', 'race_id' => '2', 'position' => 3],
            ['player_id' => '4', 'race_id' => '2', 'position' => 4],
            ['player_id' => '5', 'race_id' => '2', 'position' => 5],

            ['player_id' => '1', 'race_id' => '3', 'position' => 1],
            ['player_id' => '2', 'race_id' => '3', 'position' => 2],
            ['player_id' => '3', 'race_id' => '3', 'position' => 3],
            ['player_id' => '4', 'race_id' => '3', 'position' => 4],
            ['player_id' => '5', 'race_id' => '3', 'position' => 5],

            ['player_id' => '1', 'race_id' => '4', 'position' => 1],
            ['player_id' => '2', 'race_id' => '4', 'position' => 2],
            ['player_id' => '3', 'race_id' => '4', 'position' => 3],
            ['player_id' => '4', 'race_id' => '4', 'position' => 4],
            ['player_id' => '5', 'race_id' => '4', 'position' => 5],

            ['player_id' => '1', 'race_id' => '5', 'position' => 1],
            ['player_id' => '2', 'race_id' => '5', 'position' => 2],
            ['player_id' => '3', 'race_id' => '5', 'position' => 3],
            ['player_id' => '4', 'race_id' => '5', 'position' => 4],
            ['player_id' => '5', 'race_id' => '5', 'position' => 5],

            ['player_id' => '1', 'race_id' => '6', 'position' => 1],
            ['player_id' => '2', 'race_id' => '6', 'position' => 2],
            ['player_id' => '3', 'race_id' => '6', 'position' => 3],
            ['player_id' => '4', 'race_id' => '6', 'position' => 4],
            ['player_id' => '5', 'race_id' => '6', 'position' => 5],

            // Season id = 2, players = 4, races = 4
            ['player_id' => '6', 'race_id' => '7', 'position' => 1],
            ['player_id' => '7', 'race_id' => '7', 'position' => 2],
            ['player_id' => '8', 'race_id' => '7', 'position' => 3],
            ['player_id' => '9', 'race_id' => '7', 'position' => 4],

            ['player_id' => '6', 'race_id' => '8', 'position' => 1],
            ['player_id' => '7', 'race_id' => '8', 'position' => 2],
            ['player_id' => '8', 'race_id' => '8', 'position' => 3],
            ['player_id' => '9', 'race_id' => '8', 'position' => 4],

            ['player_id' => '6', 'race_id' => '9', 'position' => 1],
            ['player_id' => '7', 'race_id' => '9', 'position' => 2],
            ['player_id' => '8', 'race_id' => '9', 'position' => 3],
            ['player_id' => '9', 'race_id' => '9', 'position' => 4],

            ['player_id' => '6', 'race_id' => '10', 'position' => 1],
            ['player_id' => '7', 'race_id' => '10', 'position' => 2],
            ['player_id' => '8', 'race_id' => '10', 'position' => 3],
            ['player_id' => '9', 'race_id' => '10', 'position' => 4],
        ];
    }
}

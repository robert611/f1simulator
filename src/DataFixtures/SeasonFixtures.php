<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Season;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\TeamFixtures;
use App\DataFixtures\UserFixtures;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $seasons = $this->getSeasons();

        foreach ($seasons as $key => $seasonData) {
            $season = new Season();

            $season->setTeam($this->getReference('team.' . $seasonData['team_id']));
            $season->setUser($this->getReference(UserFixtures::USER_REFERENCE));
            $season->setCarId($seasonData['car_id']);
            $season->setCompleted($seasonData['completed']);

            $manager->persist($season);
            $manager->flush();

            $this->addReference('season.'. ($key + 1), $season);
        }
      
    }

    public function getDependencies()
    {
        return array(
            TeamFixtures::class,
            UserFixtures::class,
        );
    }

    public function getSeasons()
    {
        return [
            ['team_id' => 1, 'user_id' => 1, 'car_id' => 2, 'completed' => 1],
            ['team_id' => 4, 'user_id' => 2, 'car_id' => 8, 'completed' => 0],
        ];   
    }
}

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

        foreach ($seasons as $key => $data) {
            $season = new Season();

            $season->setUser($this->getReference('user.' .$data['user_id']));
            $season->setDriver($this->getReference('driver.' . $data['driver_id']));
            $season->setCompleted($data['completed']);

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
            ['user_id' => 1, 'driver_id' => 2, 'completed' => 1],
            ['user_id' => 2, 'driver_id' => 8, 'completed' => 0],
        ];   
    }
}

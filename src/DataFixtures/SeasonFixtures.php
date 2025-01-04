<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Season;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
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

    public function getDependencies(): array
    {
        return array(
            TeamFixtures::class,
            UserFixtures::class,
        );
    }

    public function getSeasons(): array
    {
        return [
            ['user_id' => 1, 'driver_id' => 2, 'completed' => 1],
            ['user_id' => 2, 'driver_id' => 8, 'completed' => 0],
        ];   
    }
}

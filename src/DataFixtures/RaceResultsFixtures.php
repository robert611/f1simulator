<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\DataFixtures\RaceFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\RaceResults;

class RaceResultsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 6; $i++) {

            for ($j = 1; $j <= 20; $j++) {
                $raceResults = new RaceResults();

                $raceResults->setRace($this->getReference('race.' . $i));
                $raceResults->setDriver($this->getReference('driver.' . $j));
                $raceResults->setPosition($j);

                $manager->persist($raceResults);
                $manager->flush(); 
            }
        }
    }

    public function getDependencies()
    {
        return array(
            RaceFixtures::class
        );
    }
}

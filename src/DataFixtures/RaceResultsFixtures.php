<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\RaceResult;

class RaceResultsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 6; $i++) {

            for ($j = 1; $j <= 20; $j++) {
                $raceResults = new RaceResult();

                $raceResults->setRace($this->getReference('race.' . $i));
                $raceResults->setDriver($this->getReference('driver.' . $j));
                $raceResults->setPosition($j);

                $manager->persist($raceResults);
                $manager->flush(); 
            }
        }
    }

    public function getDependencies(): array
    {
        return array(
            RaceFixtures::class
        );
    }
}

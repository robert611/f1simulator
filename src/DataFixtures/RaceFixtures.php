<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\DataFixtures\SeasonFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Race;

class RaceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 6; $i++) {
            $race = new Race();

            $race->setTrack($this->getReference("track." . $i));
            $race->setSeason($this->getReference("season.1"));

            $manager->persist($race);
            $manager->flush();

            $this->addReference('race.' . $i, $race);
        }
    }

    public function getDependencies()
    {
        return array(
            SeasonFixtures::class
        );
    }
}

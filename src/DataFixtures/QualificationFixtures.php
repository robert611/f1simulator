<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Qualification;
use App\DataFixtures\TeamFixtures;

class QualificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 6; $i++) {

            for ($j = 1; $j <= 20; $j++) {
                $qualification = new Qualification();

                $qualification->setRace($this->getReference('race.' . $i));
                $qualification->setDriver($this->getReference('driver.' . $j));
                $qualification->setPosition($j);

                $manager->persist($qualification);
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

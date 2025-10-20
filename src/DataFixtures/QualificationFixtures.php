<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Domain\Entity\Driver;
use App\Entity\Qualification;
use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class QualificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 6; $i++) {
            for ($j = 1; $j <= 20; $j++) {
                $qualification = new Qualification();

                $qualification->setRace($this->getReference('race.' . $i, Race::class));
                $qualification->setDriver($this->getReference('driver.' . $j, Driver::class));
                $qualification->setPosition($j);

                $manager->persist($qualification);
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

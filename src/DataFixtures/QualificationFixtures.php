<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Computer\Entity\Qualification;
use Computer\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Domain\Entity\Driver;

class QualificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 6; $i++) {
            for ($j = 1; $j <= 20; $j++) {
                /** @var Driver $driver */
                $driver = $this->getReference('driver.' . $j, Driver::class);

                $qualification = new Qualification();
                $qualification->setRace($this->getReference('race.' . $i, Race::class));
                $qualification->setDriverId($driver->getId());
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

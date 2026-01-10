<?php

declare(strict_types=1);

namespace DataFixtures;

use Computer\Entity\Race;
use Computer\Entity\RaceResult;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Domain\Entity\Driver;

class RaceResultsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 6; $i++) {
            for ($j = 1; $j <= 20; $j++) {
                /** @var Driver $driver */
                $driver = $this->getReference('driver.' . $j, Driver::class);

                $raceResults = new RaceResult();
                $raceResults->setRace($this->getReference('race.' . $i, Race::class));
                $raceResults->setDriverId($driver->getId());
                $raceResults->setPosition($j);

                $manager->persist($raceResults);
                $manager->flush();
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            RaceFixtures::class,
        ];
    }
}

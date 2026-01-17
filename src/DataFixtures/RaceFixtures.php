<?php

declare(strict_types=1);

namespace DataFixtures;

use Computer\Entity\Race;
use Computer\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Domain\Entity\Track;

class RaceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 6; $i++) {
            $track = $this->getReference("track." . $i, Track::class);
            $season = $this->getReference("season.1", Season::class);

            $race = Race::create($track->getId(), $season);

            $manager->persist($race);
            $manager->flush();

            $this->addReference('race.' . $i, $race);
        }
    }

    public function getDependencies(): array
    {
        return array(
            SeasonFixtures::class
        );
    }
}

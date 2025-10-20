<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Domain\Entity\Track;
use App\Entity\Race;
use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RaceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 6; $i++) {
            $track = $this->getReference("track." . $i, Track::class);
            $season = $this->getReference("season.1", Season::class);

            $race = Race::create($track, $season);

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

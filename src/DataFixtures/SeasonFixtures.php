<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Computer\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Domain\Entity\Driver;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $seasons = $this->getSeasons();

        foreach ($seasons as $key => $data) {
            $user = $this->getReference('user.' . $data['user_id'], User::class);
            $driver = $this->getReference('driver.' . $data['driver_id'], Driver::class);

            $season = Season::create($user, $driver);

            if ($data['completed']) {
                $season->endSeason();
            }

            $manager->persist($season);
            $manager->flush();

            $this->addReference('season.' . ($key + 1), $season);
        }
    }

    public function getDependencies(): array
    {
        return [
            TeamFixtures::class,
            UserFixtures::class,
        ];
    }

    public function getSeasons(): array
    {
        return [
            ['user_id' => 1, 'driver_id' => 2, 'completed' => true],
            ['user_id' => 2, 'driver_id' => 8, 'completed' => false],
        ];
    }
}

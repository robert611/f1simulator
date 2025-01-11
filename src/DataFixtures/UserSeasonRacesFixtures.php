<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\UserSeasonRace;
use App\DataFixtures\UserSeasonFixtures;
use App\DataFixtures\UserSeasonPlayersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserSeasonRacesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $races = $this->getRaces(); 

        foreach ($races as $key => $data) {
            $race = new UserSeasonRace();

            $race->setTrack($this->getReference('track.' . $data['track_id']));
            $race->setSeason($this->getReference('userSeason.' . $data['season_id']));
            
            $manager->persist($race);
            $manager->flush();

            $this->addReference('league_race.' . ($key + 1), $race);
        }
    }

    public function getDependencies(): array
    {
        return array(
            UserSeasonFixtures::class,
            UserSeasonPlayersFixtures::class
        );
    }

    public function getRaces(): array
    {
        return [
            ['track_id' => 1, 'season_id' => 1],
            ['track_id' => 2, 'season_id' => 1],
            ['track_id' => 3, 'season_id' => 1],
            ['track_id' => 4, 'season_id' => 1],
            ['track_id' => 5, 'season_id' => 1],
            ['track_id' => 6, 'season_id' => 1],
            ['track_id' => 1, 'season_id' => 2],
            ['track_id' => 2, 'season_id' => 2],
            ['track_id' => 3, 'season_id' => 2],
            ['track_id' => 4, 'season_id' => 2],
        ];
    }
}

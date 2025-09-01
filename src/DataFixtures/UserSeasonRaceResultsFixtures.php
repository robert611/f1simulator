<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\UserSeasonPlayer;
use App\Entity\UserSeasonRace;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\UserSeasonRaceResult;

class UserSeasonRaceResultsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $raceResults = $this->getRaceResults();

        foreach ($raceResults as $data) {
            $userSeasonPlayer = $this->getReference('league_player.' . $data['player_id'], UserSeasonPlayer::class);
            $userSeasonRace = $this->getReference('league_race.' . $data['race_id'], UserSeasonRace::class);

            $userSeasonRaceResult = UserSeasonRaceResult::create(
                $data['position'],
                $data['points'],
                $userSeasonRace,
                $userSeasonPlayer,
            );

            $manager->persist($userSeasonRaceResult);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return array(
            UserSeasonRacesFixtures::class
        );
    }

    private function getRaceResults(): array
    {
        return [
            // Season id = 1, players = 5
            ['player_id' => '1', 'race_id' => '1', 'position' => 1, 'points' => 25],
            ['player_id' => '2', 'race_id' => '1', 'position' => 2, 'points' => 18],
            ['player_id' => '3', 'race_id' => '1', 'position' => 3, 'points' => 15],
            ['player_id' => '4', 'race_id' => '1', 'position' => 4, 'points' => 12],
            ['player_id' => '5', 'race_id' => '1', 'position' => 5, 'points' => 10],

            ['player_id' => '1', 'race_id' => '2', 'position' => 1, 'points' => 25],
            ['player_id' => '2', 'race_id' => '2', 'position' => 2, 'points' => 18],
            ['player_id' => '3', 'race_id' => '2', 'position' => 3, 'points' => 15],
            ['player_id' => '4', 'race_id' => '2', 'position' => 4, 'points' => 12],
            ['player_id' => '5', 'race_id' => '2', 'position' => 5, 'points' => 10],

            ['player_id' => '1', 'race_id' => '3', 'position' => 1, 'points' => 25],
            ['player_id' => '2', 'race_id' => '3', 'position' => 2, 'points' => 18],
            ['player_id' => '3', 'race_id' => '3', 'position' => 3, 'points' => 15],
            ['player_id' => '4', 'race_id' => '3', 'position' => 4, 'points' => 12],
            ['player_id' => '5', 'race_id' => '3', 'position' => 5, 'points' => 10],

            ['player_id' => '1', 'race_id' => '4', 'position' => 1, 'points' => 25],
            ['player_id' => '2', 'race_id' => '4', 'position' => 2, 'points' => 18],
            ['player_id' => '3', 'race_id' => '4', 'position' => 3, 'points' => 15],
            ['player_id' => '4', 'race_id' => '4', 'position' => 4, 'points' => 12],
            ['player_id' => '5', 'race_id' => '4', 'position' => 5, 'points' => 10],

            ['player_id' => '1', 'race_id' => '5', 'position' => 1, 'points' => 25],
            ['player_id' => '2', 'race_id' => '5', 'position' => 2, 'points' => 18],
            ['player_id' => '3', 'race_id' => '5', 'position' => 3, 'points' => 15],
            ['player_id' => '4', 'race_id' => '5', 'position' => 4, 'points' => 12],
            ['player_id' => '5', 'race_id' => '5', 'position' => 5, 'points' => 10],

            ['player_id' => '1', 'race_id' => '6', 'position' => 1, 'points' => 25],
            ['player_id' => '2', 'race_id' => '6', 'position' => 2, 'points' => 18],
            ['player_id' => '3', 'race_id' => '6', 'position' => 3, 'points' => 15],
            ['player_id' => '4', 'race_id' => '6', 'position' => 4, 'points' => 12],
            ['player_id' => '5', 'race_id' => '6', 'position' => 5, 'points' => 10],

            // Season id = 2, players = 4, races = 4
            ['player_id' => '6', 'race_id' => '7', 'position' => 1, 'points' => 25],
            ['player_id' => '7', 'race_id' => '7', 'position' => 2, 'points' => 18],
            ['player_id' => '8', 'race_id' => '7', 'position' => 3, 'points' => 15],
            ['player_id' => '9', 'race_id' => '7', 'position' => 4, 'points' => 12],

            ['player_id' => '6', 'race_id' => '8', 'position' => 1, 'points' => 25],
            ['player_id' => '7', 'race_id' => '8', 'position' => 2, 'points' => 18],
            ['player_id' => '8', 'race_id' => '8', 'position' => 3, 'points' => 15],
            ['player_id' => '9', 'race_id' => '8', 'position' => 4, 'points' => 12],

            ['player_id' => '6', 'race_id' => '9', 'position' => 1, 'points' => 25],
            ['player_id' => '7', 'race_id' => '9', 'position' => 2, 'points' => 18],
            ['player_id' => '8', 'race_id' => '9', 'position' => 3, 'points' => 15],
            ['player_id' => '9', 'race_id' => '9', 'position' => 4, 'points' => 12],

            ['player_id' => '6', 'race_id' => '10', 'position' => 1, 'points' => 25],
            ['player_id' => '7', 'race_id' => '10', 'position' => 2, 'points' => 18],
            ['player_id' => '8', 'race_id' => '10', 'position' => 3, 'points' => 15],
            ['player_id' => '9', 'race_id' => '10', 'position' => 4, 'points' => 12],
        ];
    }
}

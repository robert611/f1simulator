<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Multiplayer\Entity\UserSeason;

class UserSeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $seasons = $this->getSeasons();

        foreach ($seasons as $key => $data) {
            $owner = $this->getReference('user.' . $data['owner_id'], User::class);

            $userSeason = UserSeason::create(
                $data['secret'],
                $data['max_players'],
                $owner,
                $data['name'],
                $data['completed'],
                $data['started'],
            );

            $manager->persist($userSeason);
            $manager->flush();

            $this->addReference('userSeason.' . ($key + 1), $userSeason);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            UserFixtures::class
        );
    }

    public function getSeasons(): array
    {
        // phpcs:disable
        return [
            ['owner_id' => 1, 'secret' => 'FH42H78XO1S1', 'max_players' => 15, 'name' => 'Liga Brunatnych kapust', 'completed' => true, 'started' => true],
            ['owner_id' => 2, 'secret' => 'HJRTY1B5X99A', 'max_players' => 20, 'name' => 'Rocket League', 'completed' => false, 'started' => true],
            ['owner_id' => 3, 'secret' => 'B32X8FOP01XX', 'max_players' => 2, 'name' => 'Liga Jabłek Brzoskwiniowych', 'completed' => false, 'started' => false],
        ];
        // phpcs:enable
    }
}

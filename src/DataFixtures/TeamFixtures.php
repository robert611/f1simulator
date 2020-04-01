<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Team;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $teams = $this->getTeams();

        foreach ($teams as $key => $teamData) {
            $team = new Team();

            $team->setName($teamData['name']);
            $team->setPicture($teamData['picture']);
            
            $manager->persist($team);
            $manager->flush();

            $this->addReference('team.'. ($key + 1), $team);
        }
    }

    public function getTeams()
    {
        return [
            ['name' => 'Ferrari', 'picture' => 'ferrari.png'],
            ['name' => 'Alfa Romeo', 'picture' => 'alfaromeo.png'],
            ['name' => 'Haas', 'picture' => 'haas.png'],
            ['name' => 'Mclaren', 'picture' => 'mclaren.png'],
            ['name' => 'Mercedes', 'picture' => 'mercedes.png'],
            ['name' => 'Racing Point', 'picture' => 'racingpoint.png'],
            ['name' => 'Red Bull', 'picture' => 'redbull.png'],
            ['name' => 'Renault', 'picture' => 'renault.png'],
            ['name' => 'Toro Rosso', 'picture' => 'tororosso.png'],
            ['name' => 'Williams', 'picture' => 'williams.png']
        ];
    }
}

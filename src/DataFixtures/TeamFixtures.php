<?php

declare(strict_types=1);

namespace DataFixtures;

use Domain\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    public const FERRARI = 'ferrari';
    public const ALFA_ROMEO = 'alfa_romeo';
    public const HAAS = 'hass';
    public const MCLAREN = 'mclaren';
    public const MERCEDES = 'mercedes';
    public const RACING_POINT = 'racing_point';
    public const RED_BULL = 'red_bull';
    public const RENAULT = 'renualt';
    public const TORO_ROSSO = 'toro_rosso';
    public const WILLIAMS = 'williams';

    public function load(ObjectManager $manager): void
    {
        $teams = $this->getTeams();

        foreach ($teams as $data) {
            $team = Team::create($data['name'], $data['picture']);

            $manager->persist($team);
            $manager->flush();

            $this->addReference($data['reference'], $team);
        }
    }

    public function getTeams(): array
    {
        return [
            ['name' => 'Ferrari', 'picture' => 'ferrari.png', 'reference' => self::FERRARI],
            ['name' => 'Alfa Romeo', 'picture' => 'alfaromeo.png', 'reference' => self::ALFA_ROMEO],
            ['name' => 'Haas', 'picture' => 'haas.png', 'reference' => self::HAAS],
            ['name' => 'Mclaren', 'picture' => 'mclaren.png', 'reference' => self::MCLAREN],
            ['name' => 'Mercedes', 'picture' => 'mercedes.png', 'reference' => self::MERCEDES],
            ['name' => 'Racing Point', 'picture' => 'racingpoint.png', 'reference' => self::RACING_POINT],
            ['name' => 'Red Bull', 'picture' => 'redbull.png', 'reference' => self::RED_BULL],
            ['name' => 'Renault', 'picture' => 'renault.png', 'reference' => self::RENAULT],
            ['name' => 'Toro Rosso', 'picture' => 'tororosso.png', 'reference' => self::TORO_ROSSO],
            ['name' => 'Williams', 'picture' => 'williams.png', 'reference' => self::WILLIAMS],
        ];
    }
}

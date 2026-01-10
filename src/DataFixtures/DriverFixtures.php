<?php

declare(strict_types=1);

namespace DataFixtures;

use Domain\Entity\Driver;
use Domain\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DriverFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $drivers = $this->getDrivers();

        foreach ($drivers as $key => $data) {
            $team = $this->getReference($data['team'], Team::class);

            $driver = Driver::create($data['name'], $data['surname'], $team, $data['car_number']);

            $manager->persist($driver);
            $manager->flush();

            $this->addReference('driver.' . ($key + 1), $driver);
        }
    }

    public function getDependencies(): array
    {
        return array(
            TeamFixtures::class
        );
    }

    public function getDrivers(): array
    {
        return [
            ['team' => TeamFixtures::FERRARI, 'name' => 'Charles', 'surname' => 'Leclerc', 'car_number' => 1],
            ['team' => TeamFixtures::FERRARI, 'name' => 'Sebastian', 'surname' => 'Vettel', 'car_number' => 2],
            ['team' => TeamFixtures::ALFA_ROMEO, 'name' => 'Kimi', 'surname' => 'Raikonnen', 'car_number' => 3],
            ['team' => TeamFixtures::ALFA_ROMEO, 'name' => 'Antonio', 'surname' => 'Giovinazzi', 'car_number' => 4],
            ['team' => TeamFixtures::HAAS, 'name' => 'Kevin', 'surname' => 'Magnussen', 'car_number' => 5],
            ['team' => TeamFixtures::HAAS, 'name' => 'Romain', 'surname' => 'Grosjean', 'car_number' => 6],
            ['team' => TeamFixtures::MCLAREN, 'name' => 'Carlos', 'surname' => 'Sainz', 'car_number' => 7],
            ['team' => TeamFixtures::MCLAREN, 'name' => 'Lando', 'surname' => 'Norris', 'car_number' => 8],
            ['team' => TeamFixtures::MERCEDES, 'name' => 'Lewis', 'surname' => 'Hamilton', 'car_number' => 9],
            ['team' => TeamFixtures::MERCEDES, 'name' => 'Valteri', 'surname' => 'Bottas', 'car_number' => 10],
            ['team' => TeamFixtures::RACING_POINT, 'name' => 'Sergio', 'surname' => 'Perez', 'car_number' => 11],
            ['team' => TeamFixtures::RACING_POINT, 'name' => 'Lance', 'surname' => 'Stroll', 'car_number' => 12],
            ['team' => TeamFixtures::RED_BULL, 'name' => 'Alexander', 'surname' => 'Albon', 'car_number' => 13],
            ['team' => TeamFixtures::RED_BULL, 'name' => 'Max', 'surname' => 'Verstappen', 'car_number' => 14],
            ['team' => TeamFixtures::RENAULT, 'name' => 'Daniel', 'surname' => 'Riccardo', 'car_number' => 15],
            ['team' => TeamFixtures::RENAULT, 'name' => 'Nico', 'surname' => 'Hulkenberg', 'car_number' => 16],
            ['team' => TeamFixtures::TORO_ROSSO, 'name' => 'Danil', 'surname' => 'Kvyat', 'car_number' => 17],
            ['team' => TeamFixtures::TORO_ROSSO, 'name' => 'Pierre', 'surname' => 'Gasly', 'car_number' => 18],
            ['team' => TeamFixtures::WILLIAMS, 'name' => 'George', 'surname' => 'Russell', 'car_number' => 19],
            ['team' => TeamFixtures::WILLIAMS, 'name' => 'Nicolas', 'surname' => 'Latifi', 'car_number' => 20]
        ];
    }
}

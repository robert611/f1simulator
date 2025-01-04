<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Driver;
use App\Entity\Team;
use Doctrine\Persistence\ObjectManager;

class DriverFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $drivers = $this->getDrivers();

        $teamRepository = $manager
            ->getRepository(Team::class);

        foreach ($drivers as $key => $data) {
            $driver = new Driver();

            $driver->setTeam($this->getReference('team.' . $data['team_id']));
            $driver->setName($data['name']);
            $driver->setSurname($data['surname']);
            $driver->setCarId($data['car_id']);
           
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
            ['team_id' => 1, 'name' => 'Charles', 'surname' => 'Leclerc', 'car_id' => 1],
            ['team_id' => 1, 'name' => 'Sebastian', 'surname' => 'Vettel', 'car_id' => 2],
            ['team_id' => 2, 'name' => 'Kimi', 'surname' => 'Raikonnen', 'car_id' => 3],
            ['team_id' => 2, 'name' => 'Antonio', 'surname' => 'Giovinazzi', 'car_id' => 4],
            ['team_id' => 3, 'name' => 'Kevin', 'surname' => 'Magnussen', 'car_id' => 5],
            ['team_id' => 3, 'name' => 'Romain', 'surname' => 'Grosjean', 'car_id' => 6],
            ['team_id' => 4, 'name' => 'Carlos', 'surname' => 'Sainz', 'car_id' => 7],
            ['team_id' => 4, 'name' => 'Lando', 'surname' => 'Norris', 'car_id' => 8],
            ['team_id' => 5, 'name' => 'Lewis', 'surname' => 'Hamilton', 'car_id' => 9],
            ['team_id' => 5, 'name' => 'Valteri', 'surname' => 'Bottas', 'car_id' => 10],
            ['team_id' => 6, 'name' => 'Sergio', 'surname' => 'Perez', 'car_id' => 11],
            ['team_id' => 6, 'name' => 'Lance', 'surname' => 'Stroll', 'car_id' => 12],
            ['team_id' => 7, 'name' => 'Alexander', 'surname' => 'Albon', 'car_id' => 13],
            ['team_id' => 7, 'name' => 'Max', 'surname' => 'Verstappen', 'car_id' => 14],
            ['team_id' => 8, 'name' => 'Daniel', 'surname' => 'Riccardo', 'car_id' => 15],
            ['team_id' => 8, 'name' => 'Nico', 'surname' => 'Hulkenberg', 'car_id' => 16],
            ['team_id' => 9, 'name' => 'Danil', 'surname' => 'Kvyat', 'car_id' => 17],
            ['team_id' => 9, 'name' => 'Pierre', 'surname' => 'Gasly', 'car_id' => 18],
            ['team_id' => 10, 'name' => 'George', 'surname' => 'Russell', 'car_id' => 19],
            ['team_id' => 10, 'name' => 'Nicolas', 'surname' => 'Latifi', 'car_id' => 20]
        ];
    }
}

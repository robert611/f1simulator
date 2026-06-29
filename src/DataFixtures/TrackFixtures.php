<?php

declare(strict_types=1);

namespace DataFixtures;

use Domain\Entity\Track;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrackFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tracks = $this->getTracks();

        foreach ($tracks as $key => $data) {
            $track = Track::create(
                $data['race_name'],
                $data['name'],
                $data['picture'],
                $data['latitude'],
                $data['longitude'],
            );

            $manager->persist($track);
            $manager->flush();

            $this->addReference('track.' . ($key + 1), $track);
        }
    }

    public function getTracks(): array
    {
        return [
            [
                'race_name' => 'Australian Grand Prix',
                'name' => 'Albert Park Grand Prix Circuit',
                'picture' => 'australia.png',
                'latitude' => '-37.849722',
                'longitude' => '144.968333',
            ],
            [
                'race_name' => 'Bahrain Grand Prix',
                'name' => 'Bahrain International Circuit',
                'picture' => 'bahrain.png',
                'latitude' => '26.0325',
                'longitude' => '50.510556',
            ],
            [
                'race_name' => 'China Grand Prix',
                'name' => 'Shanghai International Circuit',
                'picture' => 'chinese.png',
                'latitude' => '31.338889',
                'longitude' => '121.219722',
            ],
            [
                'race_name' => 'Azerbaijan Grand Prix',
                'name' => 'Baku City Circuit',
                'picture' => 'Azerbaijan.png',
                'latitude' => '40.3725',
                'longitude' => '49.853333',
            ],
            [
                'race_name' => 'Spain Grand Prix',
                'name' => 'Circuit de Barcelona-Catalunya',
                'picture' => 'spanish.png',
                'latitude' => '40.465278',
                'longitude' => '-3.615278',
            ],
            [
                'race_name' => 'Monaco Grand Prix',
                'name' => 'Circuit de Monaco',
                'picture' => 'monaco.png',
                'latitude' => '43.734722',
                'longitude' => '7.420556',
            ],
        ];
    }
}

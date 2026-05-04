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
                'name' => "Australian Grand Prix",
                'picture' => "australia.png",
                'latitude' => '-37.849722',
                'longitude' => '144.968333',
            ],
            [
                'name' => "Bahrain Grand Prix",
                'picture' => "bahrain.png",
                'latitude' => '26.0325',
                'longitude' => '50.510556',
            ],
            [
                'name' => "China Grand Prix",
                'picture' => "chinese.png",
                'latitude' => '31.338889',
                'longitude' => '121.219722',
            ],
            [
                'name' => "Azerbaijan Grand Prix",
                'picture' => "azerbaijan.png",
                'latitude' => '40.3725',
                'longitude' => '49.853333',
            ],
            [
                'name' => "Spain Grand Prix",
                'picture' => "spanish.png",
                'latitude' => '40.465278',
                'longitude' => '-3.615278',
            ],
            [
                'name' => "Monaco Grand Prix",
                'picture' => "monaco.png",
                'latitude' => '43.734722',
                'longitude' => '7.420556',
            ],
        ];
    }
}

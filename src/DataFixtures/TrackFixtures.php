<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Track;

class TrackFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tracks = $this->getTracks();

        foreach ($tracks as $key => $trackData) {
            $track = new Track();

            $track->setName($trackData['name']);
            $track->setPicture($trackData['picture']);

            $manager->persist($track);
            $manager->flush();

            $this->addReference('track.'. ($key + 1), $track);
        }
        
    }

    public function getTracks()
    {
        return [
            ['name' => "Australian Grand Prix", 'picture' => "australia.png"], 
            ['name' => "Bahrain Grand Prix", 'picture' => "bahrain.png"], 
            ['name' => "China Grand Prix", 'picture' => "chinese.png"], 
            ['name' => "Azerbaijan Grand Prix", 'picture' => "azerbaijan.png"], 
            ['name' => "Spain Grand Prix", 'picture' => "spanish.png"], 
            ['name' => "Monaco Grand Prix", 'picture' => "monaco.png"]
        ];
    }
}

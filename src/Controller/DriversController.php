<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Driver;
use App\Entity\Season;
use Symfony\Component\HttpFoundation\Response;
use App\Model\DriverPoints;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class DriversController extends AbstractController
{
    /**
     * @Route("/drivers", name="drivers")
     */
    public function index()
    {
        $drivers = $this->getDoctrine()->getRepository(Driver::class)->findAll();

        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['user' => $this->getUser(), 'completed' => 0]);

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($drivers as &$driver) {
            if ($season) {
                $points = (new DriverPoints($this->getDoctrine()))->getDriverPoints($driver->getId(), $season);
                $driver->setPoints($points);
                
                if ($driver->getCarId() == $season->getCarId()) {
                    $driver->isUser = true;
                    $driver->setName($this->getUser()->getUsername());
                    $driver->setSurname('');
                }
            } else {
                $driver->setPoints(0);
            }
        }

        $drivers = $this->setDriversPositions($drivers);

        return new Response($this->serializeDrivers($drivers));
    }

    /**
     * @Route("/drivers/last/race/results", name="drivers_last_race_results")
     */
    public function getLastRaceResults()
    {
        $drivers = $this->getDoctrine()->getRepository(Driver::class)->findAll();

        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['user' => $this->getUser(), 'completed' => 0]);

        $lastRace = $season->getRaces()[count($season->getRaces()) - 1];

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($drivers as &$driver) {
            $points = (new DriverPoints($this->getDoctrine()))->getDriverPointsByRace($driver->getId(), $lastRace);

            $driver->setPoints($points);
            
            if ($driver->getCarId() == $season->getCarId()) {
                $driver->isUser = true;
                $driver->setName($this->getUser()->getUsername());
                $driver->setSurname('');
            }
        }

        $drivers = $this->setDriversPositions($drivers);

        return new Response($this->serializeDrivers($drivers));
    }

    public function setDriversPositions($drivers)
    {
        /* Sort drivers according to got points */
        usort ($drivers, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });

        foreach ($drivers as $key => &$driver) {
            $driver->setPosition($key + 1);
        }

        return $drivers;
    }

    public function serializeDrivers($drivers)
    {
        /* Serialize drivers objects so object related to them also will be in json format */
        $encoders = [new JsonEncoder()];

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];

        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($drivers, 'json');
        
        return $jsonContent;
    }
}

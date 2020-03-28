<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Track;
use App\Entity\Qualification;
use App\Entity\Driver;
use App\Entity\RaceResults;
use App\Model\DriverPoints;
use App\Model\DriverPodiums;
use App\Model\SeasonClassifications;

class IndexController extends AbstractController
{
    /**
     * @Route("/home/{classificationType}", name="app_index")
     */
    public function index($classificationType = 'race')
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['user' => $this->getUser(), 'completed' => 0]);

        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $raceResultsRepository = $this->getDoctrine()->getRepository(RaceResults::class);
      
        if ($season) {
            $driver = $this->getDoctrine()->getRepository(Driver::class)->findOneBy(['car_id' => $season->getCarId()]);
           
            $season->setUserPoints((new DriverPoints($raceResultsRepository))->getDriverPoints($driver->getId(), $season));
           
            $track = $trackRepository->find(count($season->getRaces()) + 1);
            $lastRace = $season->getRaces()[count($season->getRaces()) - 1];

            $lastRace ? $lastRace : $classificationType  = 'drivers';

            $driverPodiums = (new DriverPodiums($this->getDoctrine()))->getDriverPodiums($driver, $season);
        } else {
            $classificationType = 'drivers';
        }

        $numberOfRacesInSeason = count($trackRepository->findAll());

        /* Get classification */
        $drivers = $this->getDoctrine()->getRepository(Driver::class)->findAll();
        $qualificationRepository = $this->getDoctrine()->getRepository(Qualification::class);
     
        $classification = (new SeasonClassifications($drivers, $season, $qualificationRepository, $raceResultsRepository))->getClassificationBasedOnType($classificationType);
    
        return $this->render('index.html.twig', [
            'season' => $season,
            'track' => isset($track) ? $track : null,
            'lastRace' => isset($lastRace) ? $lastRace : null,
            'classification' => $classification,
            'driverPodiums' => isset($driverPodiums) ? $driverPodiums : null,
            'classificationType' => $classificationType,
            'numberOfRacesInSeason' => $numberOfRacesInSeason
        ]);
    }

    /**
     * @Route("/", name="app_redirect_to_route")
     */
    public function redirectToHome()
    {
        return $this->redirectToRoute('app_index');
    }

   
}

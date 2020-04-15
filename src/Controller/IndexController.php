<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Track;
use App\Entity\Team;
use App\Entity\Qualification;
use App\Entity\Driver;
use App\Entity\RaceResults;
use App\Model\DriverStatistics\DriverPoints;
use App\Model\DriverStatistics\DriverPodiums;
use App\Model\Classification\SeasonClassifications;
use App\Model\Classification\SeasonTeamsClassification;

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
       
        if ($season) {
            $driver = $this->getDoctrine()->getRepository(Driver::class)->findOneBy(['car_id' => $season->getCarId()]);
           
            $season->setUserPoints((new DriverPoints())->getDriverPoints($driver, $season));
          
            $track = $season->getRaces()->last() ? $trackRepository->find($season->getRaces()->last()->getTrack()->getId() + 1) : $trackRepository->findAll()[0];
            $lastRace = $season->getRaces()->last();

            $lastRace ? $lastRace : $classificationType  = 'drivers';

            $driverPodiums = (new DriverPodiums())->getDriverPodiums($driver, $season);
        } else {
            $classificationType = 'drivers';
        }

        $numberOfRacesInSeason = count($trackRepository->findAll());

        /* Get classification ['Last Race', 'Qualifications' , 'General Drivers Classification'] */
        $drivers = $this->getDoctrine()->getRepository(Driver::class)->findAll();
        $qualificationRepository = $this->getDoctrine()->getRepository(Qualification::class);
     
        $classification = (new SeasonClassifications($drivers, $season, $qualificationRepository))->getClassificationBasedOnType($classificationType);
        
        /* Teams Classification/Ranking */
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        $teamsClassification = (new SeasonTeamsClassification)->getClassification($teams, $season);

        return $this->render('index.html.twig', [
            'season' => $season,
            'track' => isset($track) ? $track : null,
            'lastRace' => isset($lastRace) ? $lastRace : null,
            'classification' => $classification,
            'driverPodiums' => isset($driverPodiums) ? $driverPodiums : null,
            'classificationType' => $classificationType,
            'numberOfRacesInSeason' => $numberOfRacesInSeason,
            'teamsClassification' => $teamsClassification
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

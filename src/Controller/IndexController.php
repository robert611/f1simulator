<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Track;
use App\Entity\Team;
use App\Entity\Driver;
use App\Entity\Race;
use App\Model\DriverStatistics\DriverPoints;
use App\Model\DriverStatistics\DriverPodiums;
use App\Model\Classification\SeasonClassifications;
use App\Model\Classification\SeasonTeamsClassification;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    /**
     * @Route("/home/{classificationType}", name="app_index")
     */
    public function index($classificationType = 'drivers', Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['user' => $this->getUser(), 'completed' => 0]);

        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
       
        if ($season) {
            $driver = $season->getDriver();
           
            $season->setUserPoints((new DriverPoints())->getDriverPoints($driver, $season));
          
            $track = $season->getRaces()->last() ? $trackRepository->find($season->getRaces()->last()->getTrack()->getId() + 1) : $trackRepository->findAll()[0];
            $season->getRaces()->last() ? null : $classificationType  = 'drivers';

            $driverPodiums = (new DriverPodiums())->getDriverPodiums($driver, $season);
        } else {
            $classificationType = 'drivers';
        }

        $numberOfRacesInSeason = count($trackRepository->findAll());

        /* Get classification ['Last Race', 'Qualifications' , 'General Drivers Classification'] */
        $drivers = $this->getDoctrine()->getRepository(Driver::class)->findAll();
     
        $classification = (new SeasonClassifications($drivers, $season, $request->query->get('race_id')))->getClassificationBasedOnType($classificationType);
        
        $raceName = $request->query->has('race_id') ? $this->getDoctrine()->getRepository(Race::class)->find($request->query->get('race_id'))->getTrack()->getName() : null;

        /* Teams Classification|Ranking */
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        $teamsClassification = (new SeasonTeamsClassification)->getClassification($teams, $season);

        return $this->render('index.html.twig', [
            'season' => $season,
            'track' => isset($track) ? $track : null,
            'raceName' => $raceName,
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

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Track;
use App\Entity\Driver;
use App\Model\DriverPoints;
use App\Model\DriverPodiums;

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
           
            $season->setUserPoints((new DriverPoints($this->getDoctrine()))->getDriverPoints($driver->getId(), $season));
           
            $track = $trackRepository->find(count($season->getRaces()) + 1);
            $lastRace = $season->getRaces()[count($season->getRaces()) - 1];

            $driverPodiums = (new DriverPodiums($this->getDoctrine()))->getDriverPodiums($driver, $season);
        }

        $numberOfRacesInSeason = count($trackRepository->findAll());

        if (!isset($lastRace)) {
            $classificationType = 'drivers';
            $lastRace = null;
        }
    
        return $this->render('index.html.twig', [
            'season' => $season,
            'track' => isset($track) ? $track : null,
            'lastRace' => $lastRace,
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

<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Season;
use App\Entity\Track;
use App\Entity\Team;
use App\Entity\Driver;
use App\Entity\Race;
use App\Model\DriverStatistics\DriverPoints;
use App\Model\DriverStatistics\DriverPodiums;
use App\Model\Classification\SeasonClassifications;
use App\Model\Classification\SeasonTeamsClassification;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/home/{classificationType}', name: 'app_index', methods: ['GET'])]
    public function index($classificationType = 'drivers', Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $season = $this->entityManager->getRepository(Season::class)->findOneBy(['user' => $this->getUser(), 'completed' => 0]);

        $trackRepository = $this->entityManager->getRepository(Track::class);
       
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
        $drivers = $this->entityManager->getRepository(Driver::class)->findAll();
     
        $classification = (new SeasonClassifications($drivers, $season, $request->query->get('race_id')))->getClassificationBasedOnType($classificationType);
        
        $raceName = $request->query->has('race_id') ? $this->entityManager->getRepository(Race::class)->find($request->query->get('race_id'))->getTrack()->getName() : null;

        /* Teams Classification|Ranking */
        $teams = $this->entityManager->getRepository(Team::class)->findAll();
        $teamsClassification = (new SeasonTeamsClassification)->getClassification($teams, $season);

        return $this->render('index.html.twig', [
            'season' => $season,
            'track' => $track ?? null,
            'raceName' => $raceName,
            'classification' => $classification,
            'driverPodiums' => $driverPodiums ?? null,
            'classificationType' => $classificationType,
            'numberOfRacesInSeason' => $numberOfRacesInSeason,
            'teamsClassification' => $teamsClassification
        ]);
    }

    #[Route('/', name: 'app_redirect_to_route', methods: ['GET'])]
    public function redirectToHome(): RedirectResponse
    {
        return $this->redirectToRoute('app_index');
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SeasonRepository;
use App\Service\Classification\ClassificationType;
use App\Service\CurrentDriverSeasonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Track;
use App\Entity\Team;
use App\Entity\Driver;
use App\Entity\Race;
use App\Service\DriverStatistics\DriverPoints;
use App\Service\DriverStatistics\DriverPodiumsService;
use App\Service\Classification\SeasonClassifications;
use App\Service\Classification\SeasonTeamsClassification;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SeasonRepository $seasonRepository,
        private readonly CurrentDriverSeasonService $currentDriverSeasonService,
    ) {
    }

    #[Route('/home/{classificationType}', name: 'app_index', methods: ['GET'])]
    public function index(Request $request, ClassificationType $classificationType = ClassificationType::DRIVERS): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $season = $this->seasonRepository->findOneBy(['user' => $this->getUser(), 'completed' => 0]);

        $trackRepository = $this->entityManager->getRepository(Track::class);
       
        if ($season) {
            $driver = $season->getDriver();
           
            $season->setUserPoints((new DriverPoints())->getDriverPoints($driver, $season));
          
            $track = $season->getRaces()->last() ? $trackRepository->find($season->getRaces()->last()->getTrack()->getId() + 1) : $trackRepository->findAll()[0];
            $season->getRaces()->last() ? null : $classificationType = ClassificationType::DRIVERS;

            $driverPodiums = (new DriverPodiumsService())->getDriverPodiums($driver, $season);
        } else {
            $classificationType = ClassificationType::DRIVERS;
        }

        $numberOfRacesInSeason = count($trackRepository->findAll());

        /* Get classification ['Last Race', 'Qualifications' , 'General Drivers Classification'] */
        $drivers = $this->entityManager->getRepository(Driver::class)->findAll();
     
        $classification = (new SeasonClassifications($drivers, $season, $request->query->get('race_id')))->getClassificationBasedOnType($classificationType);
        
        $raceName = $request->query->has('race_id') ? $this->entityManager->getRepository(Race::class)->find($request->query->get('race_id'))->getTrack()->getName() : null;

        /* Teams Classification|Ranking */
        $teams = $this->entityManager->getRepository(Team::class)->findAll();
        $teamsClassification = (new SeasonTeamsClassification)->getClassification($teams, $season);

        $currentDriverSeason = $this->currentDriverSeasonService->buildCurrentDriverSeasonData(
            $this->getUser()->getId(),
            $classificationType,
            $request->query->get('race_id'),
        );

        return $this->render('index.html.twig', [
            'raceName' => $raceName,
            'classification' => $classification,
            'driverPodiums' => $driverPodiums ?? null,
            'classificationType' => $classificationType,
            'numberOfRacesInSeason' => $numberOfRacesInSeason,
            'teamsClassification' => $teamsClassification,
            'currentDriverSeason' => $currentDriverSeason,
        ]);
    }

    #[Route('/', name: 'app_redirect_to_route', methods: ['GET'])]
    public function redirectToHome(): RedirectResponse
    {
        return $this->redirectToRoute('app_index');
    }
}

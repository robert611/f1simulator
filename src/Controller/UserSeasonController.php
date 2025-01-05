<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\Qualification;
use App\Entity\Track;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayers;
use App\Form\UserSeasonType;
use App\Model\Classification\LeagueClassifications;
use App\Model\Classification\LeagueTeamsClassification;
use App\Model\DrawDriverToReplace;
use App\Model\DriverStatistics\FillLeaguePlayerData;
use App\Model\SecretGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/multiplayer')]
class UserSeasonController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'multiplayer_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $userSeason = new UserSeason();

        /* This is create league form | It is not a good idea to place it in this method */
        $form = $this->createForm(UserSeasonType::class, $userSeason, [
            'action' => $this->generateUrl('multiplayer_index'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userSeasonRepository = $this->entityManager->getRepository(UserSeason::class);

            if (count($userSeasonRepository->findBy(['owner' => $this->getUser()])) >= 3) {
                $this->addFlash('warning', 'W jednym momencie możesz mieć maksymalnie trzy nieukończone ligi');
                return $this->redirectToRoute('multiplayer_index');
            }

            $userSeason = $form->getData();

            $userSeason->setOwner($this->getUser());
            $userSeason->setSecret((new SecretGenerator)->getSecret());
            $userSeason->setCompleted(0);

            $drivers = $this->entityManager->getRepository(Driver::class)->findAll();

            $player = new UserSeasonPlayers();
            $player->setUser($this->getUser());
            $player->setDriver((new DrawDriverToReplace)->getDriverToReplaceInUserLeague($drivers, $userSeason->getPlayers()));
            $player->setSeason($userSeason);
    
            $this->entityManager->persist($userSeason);
            $this->entityManager->persist($player);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Liga została stworzona');
            return $this->redirectToRoute('multiplayer_index');
        }

        $leagueRepository = $this->entityManager->getRepository(UserSeason::class);
        $userLeagues = $leagueRepository->findBy(['owner' => $this->getUser()]);
        $leagues = array();

        foreach ($this->getUser()->getUserSeasonPlayers() as $player) {
            $leagues[] = $player->getSeason();
        }
        
        return $this->render('league/index.html.twig', [
            'form' => $form->createView(),
            'userLeagues' => $userLeagues,
            'leagues' => $leagues
        ]);
    }

    #[Route('/{id}/show/{classificationType}', name: 'multiplayer_show_season', methods: ['GET'])]
    public function showSeason(UserSeason $season, $classificationType = 'players', Request $request): Response
    {
        $this->denyAccessUnlessGranted('league_show_season', $season);

        $player = $this->entityManager->getRepository(UserSeasonPlayers::class)->findOneBy(['season' => $season, 'user' => $this->getUser()]);
        $player = (new FillLeaguePlayerData($player, $season))->getPlayer();

        $trackRepository = $this->entityManager->getRepository(Track::class);
        $numberOfRacesInSeason = count($trackRepository->findAll());

        /* If there is no more races, false will be return */
        $track = $season->getRaces()->last() ? $trackRepository->find($season->getRaces()->last()->getTrack()->getId() + 1) : $trackRepository->findAll()[0];
        
        /* If there is no played races then drivers classification will be displayed */
        $season->getRaces()->last() ? null : $classificationType = 'drivers';

        $qualificationRepository = $this->entityManager->getRepository(Qualification::class);
        $classification = (new LeagueClassifications($season, $request->query->get('race_id')))->getClassificationBasedOnType($classificationType);

        $teamsClassification = (new LeagueTeamsClassification)->getClassification($season);
        
        return $this->render('league/show_league.html.twig', [
            'league' => $season,
            'player' => $player,
            'numberOfRacesInSeason' => $numberOfRacesInSeason,
            'track' => $track,
            'classificationType' => $classificationType,
            'classification' => $classification,
            'teamsClassification' => $teamsClassification
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\Qualification;
use App\Entity\Track;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Form\UserSeasonType;
use App\Security\LeagueVoter;
use App\Service\Classification\LeagueClassifications;
use App\Service\Classification\LeagueTeamsClassification;
use App\Service\DrawDriverToReplace;
use App\Service\SecretGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/multiplayer')]
class UserSeasonController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LeagueTeamsClassification $leagueTeamsClassification,
    ) {
    }

    #[Route('/', name: 'multiplayer_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $userSeason = new UserSeason();

        /* This is league creation form | It is not a good idea to place it in this method */
        $form = $this->createForm(UserSeasonType::class, $userSeason, [
            'action' => $this->generateUrl('multiplayer_index'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userSeasonRepository = $this->entityManager->getRepository(UserSeason::class);

            if ($userSeasonRepository->count(['owner' => $this->getUser()]) >= 3) {
                $this->addFlash('warning', 'W jednym momencie możesz mieć maksymalnie trzy nieukończone ligi');

                return $this->redirectToRoute('multiplayer_index');
            }

            /** @var UserSeason $userSeason */
            $userSeason = $form->getData();

            $userSeason->setOwner($this->getUser());
            $userSeason->setSecret((new SecretGenerator)->getSecret());
            $userSeason->setCompleted(0);
            $userSeason->setStarted(false);

            $drivers = $this->entityManager->getRepository(Driver::class)->findAll();

            $player = new UserSeasonPlayer();
            $player->setUser($this->getUser());
            $player->setDriver((new DrawDriverToReplace)->getDriverToReplaceInUserLeague($drivers, $userSeason));
            $player->setSeason($userSeason);
    
            $this->entityManager->persist($userSeason);
            $this->entityManager->persist($player);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Liga została stworzona');

            return $this->redirectToRoute('multiplayer_index');
        }

        $leagueRepository = $this->entityManager->getRepository(UserSeason::class);
        $userLeagues = $leagueRepository->findBy(['owner' => $this->getUser()]);
        $leagues = [];

        foreach ($this->getUser()->getUserSeasonPlayers() as $player) {
            $leagues[] = $player->getSeason();
        }
        
        return $this->render('league/index.html.twig', [
            'form' => $form->createView(),
            'userLeagues' => $userLeagues,
            'leagues' => $leagues,
        ]);
    }

    #[Route('/{id}/show/{classificationType}', name: 'multiplayer_show_season', methods: ['GET'])]
    public function showSeason(Request $request, UserSeason $season, $classificationType = 'players'): Response
    {
        $this->denyAccessUnlessGranted(LeagueVoter::SHOW_SEASON, $season);

        $player = $this->entityManager->getRepository(UserSeasonPlayer::class)->findOneBy(['season' => $season, 'user' => $this->getUser()]);

        $trackRepository = $this->entityManager->getRepository(Track::class);
        $numberOfRacesInSeason = count($trackRepository->findAll());

        /* If there is no more races, false will be return */
        $track = $season->getRaces()->last() ? $trackRepository->find($season->getRaces()->last()->getTrack()->getId() + 1) : $trackRepository->findAll()[0];
        
        /* If there is no played races then drivers classification will be displayed */
        $season->getRaces()->last() ? null : $classificationType = 'drivers';

        $qualificationRepository = $this->entityManager->getRepository(Qualification::class);
        $classification = (new LeagueClassifications($season, $request->query->get('race_id')))->getClassificationBasedOnType($classificationType);

        $teamsClassification = $this->leagueTeamsClassification->getClassification($season);
        
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

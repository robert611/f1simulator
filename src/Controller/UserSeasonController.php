<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserSeasonType;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayers;
use App\Entity\Driver;
use App\Entity\Track;
use App\Entity\Qualification;
use App\Model\SecretGenerator;
use App\Model\DrawDriverToReplace;
use App\Model\DriverStatistics\FillLeaguePlayerData;
use App\Model\Classification\LeagueClassifications;
use App\Model\Classification\LeagueTeamsClassification;

/**
 * @Route("/multiplayer")
 */
class UserSeasonController extends AbstractController
{
    /**
     * @Route("/", name="multiplayer_index")
     */
    public function index(Request $request)
    {
        $userSeason = new UserSeason();

        /* This is create league form | It is not a good idea to place it in this method */
        $form = $this->createForm(UserSeasonType::class, $userSeason, [
            'action' => $this->generateUrl('multiplayer_index'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userSeasonRepository = $this->getDoctrine()->getRepository(UserSeason::class);

            if (count($userSeasonRepository->findBy(['owner' => $this->getUser()])) >= 3) {
                $this->addFlash('warning', 'W jednym momencie możesz mieć maksymalnie trzy nieukończone ligi');
                return $this->redirectToRoute('multiplayer_index');
            }

            $userSeason = $form->getData();

            $userSeason->setOwner($this->getUser());
            $userSeason->setSecret((new SecretGenerator)->getSecret());
            $userSeason->setCompleted(0);

            $drivers = $this->getDoctrine()->getRepository(Driver::class)->findAll();

            $player = new UserSeasonPlayers();
            $player->setUser($this->getUser());
            $player->setDriver((new DrawDriverToReplace)->getDriverToReplaceInUserLeague($drivers, $userSeason->getPlayers()));
            $player->setSeason($userSeason);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userSeason);
            $entityManager->persist($player);
            $entityManager->flush();
            
            $this->addFlash('success', 'Liga została stworzona');
            return $this->redirectToRoute('multiplayer_index');
        }

        $leagueRepository = $this->getDoctrine()->getRepository(UserSeason::class);
        $userLeagues = $leagueRepository->findBy(['owner' => $this->getUser()]);
        $leagues = array();

        foreach ($this->getUser()->getUserSeasonPlayers() as $season) {
            $leagues[] = $season->getSeason();
        }

        return $this->render('league/index.html.twig', [
            'form' => $form->createView(),
            'userLeagues' => $userLeagues,
            'leagues' => $leagues
        ]);
    }

    /**
     * @Route("/{id}/show/{classificationType}", name="multiplayer_show_season")
     */
    public function showSeason(UserSeason $season, $classificationType = 'players', Request $request)
    {
        $this->denyAccessUnlessGranted('league_show_season', $season);

        $player = $this->getDoctrine()->getRepository(UserSeasonPlayers::class)->findOneBy(['season' => $season, 'user' => $this->getUser()]);
        $player = (new FillLeaguePlayerData($player, $season))->getPlayer();

        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $numberOfRacesInSeason = count($trackRepository->findAll());

        /* If there is no more races, false will be return */
        $track = $season->getRaces()->last() ? $trackRepository->find($season->getRaces()->last()->getTrack()->getId() + 1) : $trackRepository->findAll()[0];
        
        /* If there is no played races then drivers classification will be displayed */
        $season->getRaces()->last() ? null : $classificationType = 'drivers';

        $qualificationRepository = $this->getDoctrine()->getRepository(Qualification::class);
        $classification = (new LeagueClassifications($season, $request->query->get('race_id')))->getClassificationBasedOnType($classificationType);

        $teamsClassification = (new LeagueTeamsClassification)->getClassification($season->getPlayers(), $season);
        
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

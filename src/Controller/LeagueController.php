<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayers;
use App\Entity\UserSeasonRaces;
use App\Entity\UserSeasonQualifications;
use App\Entity\UserSeasonRaceResults;
use App\Entity\Driver;
use App\Entity\Track;
use App\Model\DrawDriverToReplace;
use App\Model\GameSimulation\SimulateLeagueRace;

/**
* @Route("/league")
*/
class LeagueController extends AbstractController
{
    /**
     * @Route("/join", name="league_join")
     */
    public function join(Request $request)
    {
        $secret = $request->request->get('league-secret');
        $league = $this->getDoctrine()->getRepository(UserSeason::class)->findOneBy(['secret' => $secret]);

        $this->denyAccessUnlessGranted('league_join', $league);

        $drivers = $this->getDoctrine()->getRepository(Driver::class)->findAll();

        $player = new UserSeasonPlayers();
        $player->setUser($this->getUser());
        $player->setDriver((new DrawDriverToReplace)->getDriverToReplaceInUserLeague($drivers,  $league->getPlayers()));
        $player->setSeason($league);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($player);
        $entityManager->flush();

        return $this->redirectToRoute('multiplayer_index');
    }

    /**
     * @Route("/{id}/start", name="league_start")
     */
    public function startLeague(UserSeason $season)
    {
        $this->denyAccessUnlessGranted('league_start', $season);

        $season->setStarted(1);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($season);
        $entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    /**
     * @Route("/{id}/end", name="league_end")
     */
    public function endLeague(UserSeason $season)
    {
        $this->denyAccessUnlessGranted('league_end', $season);

        $season->setCompleted(1);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($season);
        $entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    /**
     * @Route("/{id}/simulate/race", name="league_simulate_race")
     */
    public function simulateRace(UserSeason $season)
    {
        $this->denyAccessUnlessGranted('league_simulate_race', $season);

        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $entityManager = $this->getDoctrine()->getManager();

        $lastRace = $season->getRaces()->last();
        $track = $lastRace ? $trackRepository->find($lastRace->getTrack()->getId() + 1) : $trackRepository->findAll()[0];

        /* Save race in the database */
        $race = new UserSeasonRaces();

        $race->setTrack($track);
        $race->setSeason($season);

        $entityManager->persist($race);
        $entityManager->flush();

        [$qualificationsResults, $raceResults] = (new SimulateLeagueRace)->getRaceResults($season->getPlayers());

        /* Save qualifications results in database, element index is equivalent to its position */
        foreach ($qualificationsResults as $position => $player) {
            $qualification = new UserSeasonQualifications();

            $qualification->setRace($race);
            $qualification->setPlayer($player);
            $qualification->setPosition($position);

            $entityManager->persist($qualification);

            $entityManager->flush();
        }
        
        /* Save race results in database */
        foreach ($raceResults as $position => $player) {
            $raceResult = new UserSeasonRaceResults();

            $raceResult->setRace($race);
            $raceResult->setPlayer($player);
            $raceResult->setPosition($position);

            $entityManager->persist($raceResult);

            $entityManager->flush();
        }

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }
}

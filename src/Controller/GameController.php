<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Driver;
use App\Entity\Team;
use App\Entity\Race;
use App\Entity\Track;
use App\Entity\RaceResults;
use Symfony\Component\HttpFoundation\Request;
use App\Model\SimulateRace;
use App\Model\SimulateQualifications;
use Symfony\Component\HttpFoundation\Session\Session;
USE App\Entity\Qualification;

class GameController extends AbstractController
{
    /**
     * @Route("/game/season/start", name="game_season_start")
     */
    public function startSeason(Request $request)
    {
        $team = $this->getDoctrine()->getRepository(Team::class)->find($request->get('team'));

        $entityManager = $this->getDoctrine()->getManager();

        $season = new Season();

        $season->setTeam($team);
        $season->setUser($this->getUser());
        $season->setCarId($this->getDriverToReplace($team->getId())->getCarId());
        $season->setCompleted(0);

        $entityManager->persist($season);

        $entityManager->flush();

        return $this->redirectToRoute('app_index');
    }

    /**
     * @Route("/game/season/end", name="game_season_end")
     */
    public function endSeason()
    {
        $seasonRepository = $this->getDoctrine()->getRepository(Season::class);
        $season = $seasonRepository->findOneBy(['user' => $this->getUser()->getId(), 'completed' => 0]);
        
        if (!$season) {
            $this->addFlash('error', 'Nie możesz zakończyć sezonu, bez jego rozpoczęcia.');
            return $this->redirectToRoute('app_index');
        }

        /* If number of finished races is equal to number of all tracks than season should be end */
        if (count($season->getRaces()) == count($this->getDoctrine()->getRepository(Track::class)->findAll())) {

            $season->setCompleted(1);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($season);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_index');
    }

    /**
     * @Route("/game/simulate/race", name="game_simulate_race")
     */
    public function simulateRace(Session $session)
    {
        $seasonRepository = $this->getDoctrine()->getRepository(Season::class);
        $trackRepository = $this->getDoctrine()->getRepository(Track::class);
        $driverRepository = $this->getDoctrine()->getRepository(Driver::class);

        /* First find a season to which race belongs */
        $season = $seasonRepository->findOneBy(['user' => $this->getUser()->getId(), 'completed' => 0]);

        if (!$season) {
            $this->addFlash('error', 'Nie możesz symulować wyścigu, bez rozpoczęcia sezonu.');
            return $this->redirectToRoute('app_index');
        }

        /* Save race in database */
        $race = new Race();

        $entityManager = $this->getDoctrine()->getManager();

        $lastRace = $season->getRaces()->last();
        $track = $lastRace ? $trackRepository->find($lastRace->getTrack()->getId() + 1) : $trackRepository->findAll()[0];

        if (count($season->getRaces()) == count($trackRepository->findAll())) {
            $session->getFlashBag()->add('error', 'Wystąpił problem z rozegraniem wyścigu, ze względu bezpieczeństwa danych twój sezon został zakończony.');

            $season->setCompleted(1);

            $entityManager->persist($season);
            $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        $race->setTrack($track);
        $race->setSeason($season);

        $entityManager->persist($race);
        $entityManager->flush();

        $qualificationsResults = (new SimulateQualifications)->getQualificationsResults($driverRepository->findAll());
        
        $results = (new SimulateRace)->getRaceResults($driverRepository->findAll(), $qualificationsResults);


        /* Save qualifications results in database */
        foreach ($qualificationsResults as $position => $driver) {
            $qualification = new Qualification();

            $qualification->setRace($race);
            $qualification->setDriver($driver);
            $qualification->setPosition($position);

            $entityManager->persist($qualification);

            $entityManager->flush();
        }
        
        /* Save race results in database */
        foreach ($results as $position => $driverId) {
            $raceResult = new RaceResults();

            $raceResult->setRace($race);
            $raceResult->setDriver($driverRepository->find($driverId));
            $raceResult->setPosition($position);

            $entityManager->persist($raceResult);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_index');
    }

    /* This function takes id of the team which user choosed and draws one of the driver of that team to replace him with user */
    public function getDriverToReplace($teamId): object
    {
        $drivers = $this->getDoctrine()->getRepository(Driver::class)->findBy(['team' => $teamId]);

        $random = rand(0, (count($drivers) - 1));

        return $drivers[$random];
    }
}

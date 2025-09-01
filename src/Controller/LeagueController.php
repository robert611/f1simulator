<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Configuration\RaceScoringSystem;
use App\Repository\TrackRepository;
use App\Repository\UserSeasonRepository;
use App\Security\LeagueVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Entity\UserSeasonRace;
use App\Entity\UserSeasonQualification;
use App\Entity\UserSeasonRaceResult;
use App\Entity\Driver;
use App\Service\DrawDriverToReplace;
use App\Service\GameSimulation\SimulateLeagueRace;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/league')]
class LeagueController extends BaseController
{
    public function __construct(
        private readonly UserSeasonRepository $userSeasonRepository,
        private readonly TrackRepository $trackRepository,
        private readonly SimulateLeagueRace $simulateLeagueRace,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/join', name: 'league_join', methods: ['GET', 'POST'])]
    public function join(Request $request): RedirectResponse
    {
        $secret = $request->request->get('league-secret');
        $league = $this->userSeasonRepository->findOneBy(['secret' => $secret]);

        $this->denyAccessUnlessGranted(LeagueVoter::JOIN, $league);

        $drivers = $this->entityManager->getRepository(Driver::class)->findAll();

        $driver = (new DrawDriverToReplace())->getDriverToReplaceInUserLeague($drivers, $league);

        $player = UserSeasonPlayer::create($league, $this->getUser(), $driver);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_index');
    }

    #[Route('/{id}/start', name: 'league_start', methods: ['GET'])]
    public function startLeague(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::START, $season);

        $season->setStarted(true);

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    #[Route('/{id}/end', name: 'league_end', methods: ['GET'])]
    public function endLeague(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::END, $season);

        $season->setCompleted(true);

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    #[Route('/{id}/simulate/race', name: 'league_simulate_race', methods: ['GET'])]
    public function simulateRace(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::SIMULATE_RACE, $season);

        $lastRace = $season->getRaces()->last();
        $track = $lastRace
            ? $this->trackRepository->getNextTrack($lastRace->getTrack()->getId())
            : $this->trackRepository->getFirstTrack();

        /* Save race in the database */
        $race = new UserSeasonRace();

        $race->setTrack($track);
        $race->setSeason($season);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        [$qualificationsResults, $raceResults] = $this->simulateLeagueRace->getRaceResults($season->getPlayers());

        foreach ($qualificationsResults as $position => $player) {
            $qualification = new UserSeasonQualification();
            $qualification->setRace($race);
            $qualification->setPlayer($player);
            $qualification->setPosition($position);

            $this->entityManager->persist($qualification);
            $this->entityManager->flush();
        }

        /** @var UserSeasonPlayer $player */
        foreach ($raceResults as $position => $player) {
            $points = RaceScoringSystem::getPositionScore($position);

            $raceResult = UserSeasonRaceResult::create($position, $points, $race, $player);
            $player->addPoints($points);

            $this->entityManager->persist($raceResult);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }
}

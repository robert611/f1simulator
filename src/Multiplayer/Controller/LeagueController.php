<?php

declare(strict_types=1);

namespace Multiplayer\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\Configuration\RaceScoringSystem;
use Domain\Entity\Driver;
use Domain\Repository\TrackRepository;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Entity\UserSeasonQualification;
use Multiplayer\Entity\UserSeasonRace;
use Multiplayer\Entity\UserSeasonRaceResult;
use Multiplayer\Repository\UserSeasonRepository;
use Multiplayer\Security\LeagueVoter;
use Multiplayer\Service\DrawDriverToReplace;
use Multiplayer\Service\GameSimulation\SimulateLeagueRace;
use Multiplayer\Service\LeagueClassifications;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/league')]
class LeagueController extends BaseController
{
    public function __construct(
        private readonly UserSeasonRepository $userSeasonRepository,
        private readonly TrackRepository $trackRepository,
        private readonly SimulateLeagueRace $simulateLeagueRace,
        private readonly LeagueClassifications $leagueClassifications,
        private readonly DrawDriverToReplace $drawDriverToReplace,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/join', name: 'league_join', methods: ['GET', 'POST'])]
    public function join(Request $request): RedirectResponse
    {
        $secret = $request->request->get('league-secret');
        $league = $this->userSeasonRepository->findOneBy(['secret' => $secret]);

        $this->denyAccessUnlessGranted(LeagueVoter::JOIN, $league);

        $driver = $this->drawDriverToReplace->getDriverToReplaceInUserLeague($league);

        $driver = $this->entityManager->getReference(Driver::class, $driver->getId());

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
    public function simulateRace(UserSeason $userSeason): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::SIMULATE_RACE, $userSeason);

        /** @var UserSeasonRace $lastRace */
        $lastRace = $userSeason->getRaces()->last();
        $track = $lastRace
            ? $this->trackRepository->getNextTrack($lastRace->getTrackId())
            : $this->trackRepository->getFirstTrack();

        /* Save race in the database */
        $race = new UserSeasonRace();

        $race->setTrackId($track->getId());
        $race->setSeason($userSeason);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        $leagueRaceResultsDTO = $this->simulateLeagueRace->simulateRaceResults($userSeason);

        $qualificationsResults = $leagueRaceResultsDTO->getQualificationsResults();

        foreach ($qualificationsResults->getQualificationResults() as $result) {
            $qualification = new UserSeasonQualification();
            $qualification->setRace($race);
            $qualification->setPlayer($result->getUserSeasonPlayer());
            $qualification->setPosition($result->getPosition());

            $this->entityManager->persist($qualification);
            $this->entityManager->flush();
        }

        $raceResults = $leagueRaceResultsDTO->getRaceResults();

        /** @var UserSeasonPlayer $player */
        foreach ($raceResults as $position => $player) {
            $points = RaceScoringSystem::getPositionScore($position);

            $raceResult = UserSeasonRaceResult::create($position, $points, $race, $player);
            $player->addPoints($points);

            $this->entityManager->persist($raceResult);
            $this->entityManager->flush();
        }

        $this->leagueClassifications->recalculatePlayersPositions($userSeason);

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $userSeason->getId()]);
    }
}

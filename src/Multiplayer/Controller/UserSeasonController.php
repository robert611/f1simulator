<?php

declare(strict_types=1);

namespace Multiplayer\Controller;

use App\Service\Classification\ClassificationType;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Repository\TrackRepository;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Form\UserSeasonType;
use Multiplayer\Security\DrawDriverToReplace;
use Multiplayer\Security\LeagueVoter;
use Multiplayer\Security\SecretGenerator;
use Multiplayer\Service\LeagueClassifications;
use Multiplayer\Service\LeagueTeamsClassification;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/multiplayer')]
class UserSeasonController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LeagueTeamsClassification $leagueTeamsClassification,
        private readonly LeagueClassifications $leagueClassification,
        private readonly DrawDriverToReplace $drawDriverToReplace,
        private readonly TrackRepository $trackRepository,
        private readonly SecretGenerator $secretGenerator,
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
            $userSeason->setSecret($this->secretGenerator->getLeagueUniqueSecret());
            $userSeason->setCompleted(false);
            $userSeason->setStarted(false);

            $player = new UserSeasonPlayer();
            $player->setUser($this->getUser());
            $player->setDriver($this->drawDriverToReplace->getDriverToReplaceInUserLeague($userSeason));
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
    public function showSeason(
        Request $request,
        UserSeason $season,
        ClassificationType $classificationType = ClassificationType::PLAYERS,
    ): Response {
        $this->denyAccessUnlessGranted(LeagueVoter::SHOW_SEASON, $season);

        $player = $this->entityManager->getRepository(UserSeasonPlayer::class)->findOneBy([
            'season' => $season,
            'user' => $this->getUser(),
        ]);

        $numberOfRacesInSeason = $this->trackRepository->count();

        if ($season->getRaces()->count()) {
            $track = $this->trackRepository->getNextTrack($season->getRaces()->last()->getTrack()->getId());
        } else {
            $track = $this->trackRepository->getFirstTrack();
        }

        if ($season->getRaces()->count() === 0) {
            $classificationType = ClassificationType::DRIVERS;
        }

        $raceId = $request->query->get('race_id');

        if (is_numeric($raceId)) {
            $raceId = (int) $raceId;
        }

        $classification = $this->leagueClassification->getClassificationBasedOnType(
            $season,
            $classificationType,
            $raceId,
        );

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

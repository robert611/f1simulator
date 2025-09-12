<?php 

declare(strict_types=1);

namespace App\Security;

use App\Entity\UserSeason;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\TrackRepository;
use Symfony\Component\HttpFoundation\Request;

class LeagueVoter extends Voter
{
    public const START = 'START';
    public const END = 'END';
    public const JOIN = 'JOIN';
    public const SHOW_SEASON = 'SHOW_SEASON';
    public const SIMULATE_RACE = 'SIMULATE_RACE';

    public function __construct(
        private readonly TrackRepository $trackRepository,
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::START, self::JOIN, self::SIMULATE_RACE, self::END, self::SHOW_SEASON])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $league = $subject;

        switch ($attribute) {
            case self::START:
                return $this->canStart($league, $user);
            case self::END:
                return $this->canEnd($league, $user);
            case self::JOIN:
                return $this->canJoin($league, $user);
            case self::SHOW_SEASON:
                return $this->canShow($league, $user);
            case self::SIMULATE_RACE:
                return $this->canSimulateRace($league, $user);
        }

        throw new LogicException('There is no such action as: ' . $attribute . ' defined in league voter');
    }

    private function canStart(UserSeason $league, User $user): bool
    {
        if (false === $this->isALeagueOwner($league, $user)) {
            return false;
        }

        $enoughPlayers = $league->getPlayers()->count() >= UserSeason::MINIMUM_PLAYERS;

        if (false === $enoughPlayers) {
            (new Session())->getFlashBag()->add('warning', 'Do rozpoczęcia ligi potrzebujesz przynajmniej dwóch użytkowników.');
        }

        return $enoughPlayers;
    }

    private function canEnd(UserSeason $league, User $user): bool
    {
        return $this->isALeagueOwner($league, $user);
    }

    public function canShow(UserSeason $league, User $user): bool
    {
        /* This function checks if race, which results should be displayed in a page belongs to that league */
        if (!$this->doesRaceBelongToLeague($league)) {
            return false;
        }

        /* To see given league, $user has to be on of the players */
        $isPlayer = $league->getPlayers()->filter(function ($player) use ($user) {
            return $player->getUser() == $user;
        });

        return $isPlayer->count() != 0;
    }

    private function canJoin(?UserSeason $league, User $user): bool
    {
        if (!$this->leagueExists($league)) {
            return false;
        }

        if ($this->isUserInTheLeague($league, $user)) {
            return false;
        }

        if ($this->maxPlayersReached($league)) {
            return false;
        }

        return true;
    }

    private function canSimulateRace(UserSeason $league, User $user): bool
    {
        if (!$this->isALeagueOwner($league, $user)) {
            return false;
        }

        if ($league->getRaces()->count() >= $this->trackRepository->count()) {
            (new Session())->getFlashBag()->add('warning', 'Wszystkie wyścigi zostały już rozegrane.');

            return false;
        }

        return true;
    }

    private function isALeagueOwner(UserSeason $league, User $user): bool
    {
        if ($user !== $league->getOwner()) {
            (new Sessio())->getFlashBag()->add('warning', 'Nie możesz wykonać tej operacji, ponieważ nie jesteś założycielem tej ligi.');

            return false;
        }

        return true;
    }

    private function leagueExists(?UserSeason $league): bool
    {
        if (null === $league) {
            (new Session())->getFlashBag()->add('warning', 'Nie istnieje liga o takim kluczu.');

            return false;
        }
        
        return true;
    }

    private function isUserInTheLeague(UserSeason $league, User $user): bool
    {
        $alreadyIn = $league->getPlayers()->filter(function ($player) use ($user) {
            return $player->getUser() === $user;
        });

        $access = $alreadyIn->count() > 0;

        if ($access) {
            (new Session())->getFlashBag()->add('warning', 'Należysz już do: '. $league->getName());
        }

        return $access;
    }

    private function maxPlayersReached(UserSeason $league): bool
    {
        $reached = count($league->getPlayers()) >= $league->getMaxPlayers();

        if ($reached) {
            (new Session())->getFlashBag()->add('warning', 'Ta liga osiągnęła swoją maksymalną liczbę graczy');
        }
        
        return $reached;
    }

    private function doesRaceBelongToLeague(UserSeason $league): bool
    {
        $request = Request::createFromGlobals();

        // @todo to jest skomplikowany kod, podatny na błędy, żeby tak głęboko w voterze korzystać z request
        $raceId = $request->query->get('race_id');

        /* In this case, user does not display race results, therefore it does not matter */
        if ($raceId === null) {
            return true;
        }

        $belongs = false;

        $league->getRaces()->map(function ($race) use (&$belongs, $raceId) {
            if ($race->getId() === (int) $raceId) {
                $belongs = true;
            }
        });

        return $belongs;
    }
}

<?php 

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
    const START = 'league_start';
    const END = 'league_end';
    const JOIN = 'league_join';
    const SHOW = 'league_show_season';
    const SIMULATE_RACE = 'league_simulate_race';

    private TrackRepository $trackRepository;

    public function __construct(TrackRepository $trackRepository)
    {
        $this->trackRepository = $trackRepository;
    }

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::START, self::JOIN, self::SIMULATE_RACE, self::END, self::SHOW])) {
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
            case self::SHOW:
                return $this->canShow($league, $user);    
            case self::SIMULATE_RACE:
                return $this->canSimulateRace($league, $user);
        }

        throw new LogicException('There is no such action as: ' . $attribute . ' defined in league voter');
    }

    private function canStart(UserSeason $league, User $user): bool
    {
        $access = $this->isLeagueOwner($league, $user);

        $enoughPlayers = $league->getPlayers()->count() >= 2;

        if (!$enoughPlayers) {
            (new Session)->getFlashBag()->add('warning', 'Do rozpoczęcia ligi potrzebujesz przynajmniej dwóch użytkowników.');
        }

        /* So user has to be an owner and there has to be at least two players in the league */
        return $access AND $enoughPlayers;
    }

    private function canEnd(UserSeason $league, User $user): bool
    {
        return $this->isLeagueOwner($league, $user);
    }

    public function canShow(UserSeason $league, User $user): bool
    {
        /* This function checks if race, which results should be displayed in a page belongs to that league */
        if(!$this->doesRaceBelongToLeague($league)) {
            return false;
        }

        /* To see given league, $user has to be on of the players */
        $isPlayer = $league->getPlayers()->filter(function($player) use ($user) {
            return $player->getUser() == $user;
        });

        return $isPlayer->count() != 0;
    }

    private function canJoin(?UserSeason $league, User $user): bool
    {
        if (!$this->leagueExists($league, $user)) {
            return false;
        }

        if ($this->userInLeague($league, $user)) {
            return false;
        }

        if ($this->maxPlayersReached($league)) {
            return false;
        }

        return true;
    }

    private function canSimulateRace(UserSeason $league, User $user): bool
    {
        if (!$this->isLeagueOwner($league, $user)) {
            return false;
        }

        if ($league->getRaces()->count() >= count($this->trackRepository->findAll())) {
            (new Session)->getFlashBag()->add('warning', 'Wszystkie wyścigi zostały już rozegrane.');
            return false;
        }

        return true;
    }

    private function isLeagueOwner(UserSeason $league, User $user): bool
    {
        $access = $user === $league->getOwner();

        if (!$access) {
            (new Session)->getFlashBag()->add('warning', 'Nie możesz wykonać tej operacji, ponieważ nie jesteś założycielem tej ligi.');
        }

        return $access;
    }

    private function leagueExists(?UserSeason $league, User $user): bool
    {
        $access = $league ? true : false;

        if (!$access) {
            (new Session)->getFlashBag()->add('warning', 'Nie istnieje liga o takim kluczu.');
        }
        
        return $access;
    }

    private function userInLeague(UserSeason $league, User $user): bool
    {
        $alreadyIn = $league->getPlayers()->filter(function ($player) use ($user) {
            return $player->getUser() === $user;
        });

        $access = $alreadyIn->count() > 0;

        if ($access) (new Session)->getFlashBag()->add('warning', 'Należysz już do: '. $league->getName());
    
        return $access;
    }

    private function maxPlayersReached(UserSeason $league): bool
    {
        $reached = count($league->getPlayers()) >= $league->getMaxPlayers();

        if ($reached) {
            (new Session)->getFlashBag()->add('warning', 'Ta liga osiągneła swoją maksymalną liczbę graczy');
        }
        
        return $reached;
    }

    private function doesRaceBelongToLeague(UserSeason $league): bool
    {
        $request = Request::createFromGlobals();

        $raceId = $request->query->get('race_id');
        $belongs = false;

        /* In this case, user does not display race results, therefore it does not matter */
        if ($raceId === null) {
            return true;
        }
        
        $league->getRaces()->map(function($race) use (&$belongs, $raceId) {
            $race->getId() == $raceId ? $belongs = true : null; 
        });

        return $belongs;
    }
}
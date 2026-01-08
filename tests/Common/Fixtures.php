<?php

declare(strict_types=1);

namespace Tests\Common;

use Computer\Entity\Qualification;
use Computer\Entity\Race;
use Computer\Entity\RaceResult;
use Computer\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Entity\Driver;
use Domain\Entity\Team;
use Domain\Entity\Track;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Entity\UserSeasonQualification;
use Multiplayer\Entity\UserSeasonRace;
use Multiplayer\Entity\UserSeasonRaceResult;
use Security\Entity\User;

class Fixtures
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function aUser(): User
    {
        $user = new User();
        $user->setUsername('tommy123');
        $user->setEmail('tommy123@gmail.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function anAdmin(): User
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@gmail.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function aCustomUser(string $username, string $email): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function aTeam(): Team
    {
        $team = Team::Create("Mercedes", "mercedes.png");

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }

    public function aTeamWithName(string $name): Team
    {
        $team = Team::create($name, "$name.png");

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }

    public function aTeamWithDrivers(): Team
    {
        $team = Team::Create("Mercedes", "mercedes.png");

        $driverOne = $this->aDriver("Lewis", "Hamilton", $team, 33, false);
        $driverTwo = $this->aDriver("Valteri", "Bottas", $team, 5, false);

        $team->addDriver($driverOne);
        $team->addDriver($driverTwo);

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }

    public function aDriver(string $name, string $surname, Team $team, int $carNumber, ?bool $useFlash = true): Driver
    {
        $driver = Driver::create($name, $surname, $team, $carNumber);

        $team->addDriver($driver);

        $this->entityManager->persist($driver);

        if ($useFlash) {
            $this->entityManager->flush();
        }

        return $driver;
    }

    public function aSeason(User $user, Driver $driver): Season
    {
        $season = Season::Create($user, $driver->getId());

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $season;
    }

    public function aUserSeason(
        string $secret,
        int $maxPlayers,
        User $owner,
        string $name,
        bool $completed,
        bool $started,
    ): UserSeason {
        $userSeason = UserSeason::create(
            $secret,
            $maxPlayers,
            $owner,
            $name,
            $completed,
            $started,
        );

        $this->entityManager->persist($userSeason);
        $this->entityManager->flush();

        return $userSeason;
    }

    public function aUserSeasonPlayer(UserSeason $userSeason, User $user, Driver $driver): UserSeasonPlayer
    {
        $userSeasonPlayer = UserSeasonPlayer::create(
            $userSeason,
            $user,
            $driver->getId(),
        );

        $userSeason->addPlayer($userSeasonPlayer);

        $this->entityManager->persist($userSeasonPlayer);
        $this->entityManager->flush();

        return $userSeasonPlayer;
    }

    public function aUserSeasonRace(int $trackId, UserSeason $userSeason): UserSeasonRace
    {
        $userSeasonRace = UserSeasonRace::create($trackId, $userSeason);

        $this->entityManager->persist($userSeasonRace);
        $this->entityManager->flush();

        return $userSeasonRace;
    }

    public function aUserSeasonRaceResult(
        int $position,
        int $points,
        UserSeasonRace $race,
        UserSeasonPlayer $player,
    ): UserSeasonRaceResult {
        $userSeasonRaceResult = UserSeasonRaceResult::create($position, $points, $race, $player);

        $race->addRaceResult($userSeasonRaceResult);

        $this->entityManager->persist($userSeasonRaceResult);
        $this->entityManager->flush();

        return $userSeasonRaceResult;
    }

    public function aUserSeasonQualification(
        UserSeasonPlayer $player,
        UserSeasonRace $race,
        int $position,
    ): UserSeasonQualification {
        $userSeasonQualification = UserSeasonQualification::create($player, $race, $position);

        $race->addQualification($userSeasonQualification);

        $this->entityManager->persist($userSeasonQualification);
        $this->entityManager->flush();

        return $userSeasonQualification;
    }

    public function aTrack(string $name, string $picture): Track
    {
        $track = Track::create($name, $picture);

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return $track;
    }

    public function aRace(Track $track, Season $season): Race
    {
        $race = Race::Create($track->getId(), $season);

        $season->addRace($race);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        return $race;
    }

    public function aRaceResult(int $position, Race $race, Driver $driver): RaceResult
    {
        $raceResult = RaceResult::create($position, $race, $driver->getId());

        $race->addRaceResult($raceResult);

        $this->entityManager->persist($raceResult);
        $this->entityManager->flush();

        return $raceResult;
    }

    public function aQualification(Driver $driver, Race $race, int $position): Qualification
    {
        $qualification = Qualification::Create($driver->getId(), $race, $position);

        $race->addQualification($qualification);

        $this->entityManager->persist($qualification);
        $this->entityManager->flush();

        return $qualification;
    }
}

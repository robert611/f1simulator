<?php

declare(strict_types=1);

namespace App\Tests\Common;

use App\Entity\Driver;
use App\Entity\Race;
use App\Entity\Season;
use App\Entity\Team;
use App\Entity\Track;
use App\Entity\User;
use App\Entity\UserSeason;
use Doctrine\ORM\EntityManagerInterface;

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

    public function aDriver(string $name, string $surname, Team $team, int $carId, ?bool $useFlash = true): Driver
    {
        $driver = Driver::create($name, $surname, $team, $carId);

        $this->entityManager->persist($driver);

        if ($useFlash) {
            $this->entityManager->flush();
        }

        return $driver;
    }

    public function aSeason(User $user, Driver $driver): Season
    {
        $season = Season::Create($user, $driver);

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

    public function aTrack(string $name, string $picture): Track
    {
        $track = Track::create($name, $picture);

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return $track;
    }

    public function aRace(Track $track, Season $season): Race
    {
        $race = Race::Create($track, $season);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        return $race;
    }
}

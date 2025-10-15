<?php

declare(strict_types=1);

namespace App\Service\Classification;

use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Repository\UserSeasonRaceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class LeagueClassifications
{
    public function __construct(
        private readonly UserSeasonRaceRepository $userSeasonRaceRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function recalculatePlayersPositions(UserSeason $league): void
    {
        /** @var UserSeasonPlayer[] $leaguePlayers */
        $leaguePlayers = $league->getPlayers()->toArray();

        usort($leaguePlayers, function (UserSeasonPlayer $a, UserSeasonPlayer $b) {
            return $b->getPoints() <=> $a->getPoints();
        });

        // Reindex table
        $leaguePlayers = array_values($leaguePlayers);

        foreach ($leaguePlayers as $index => $player) {
            $position = $index + 1;

            $player->updatePosition($position);
        }

        $this->entityManager->flush();
    }

    public function getClassificationBasedOnType(
        UserSeason $league,
        ClassificationType $type,
        ?int $raceId,
    ): array|Collection {
        return match ($type) {
            ClassificationType::RACE => $this->getRaceClassification($league, $raceId),
            ClassificationType::QUALIFICATIONS => $this->getQualificationsClassification($league, $raceId),
            default => $this->getPlayersClassification($league),
        };
    }

    private function getRaceClassification(UserSeason $league, int $raceId): Collection
    {
        $userSeasonRace = $this->userSeasonRaceRepository->findOneBy(['id' =>  $raceId, 'season' => $league]);

        return $userSeasonRace->getRaceResults();
    }

    /**
     * @return UserSeasonPlayer[]
     */
    private function getPlayersClassification(UserSeason $league): array
    {
        $players = $league->getPlayers()->toArray();

        /* Sort drivers according to possessed points */
        usort($players, function (UserSeasonPlayer $a, UserSeasonPlayer $b) {
            return $b->getPoints() <=> $a->getPoints();
        });

        return $players;
    }

    private function getQualificationsClassification(UserSeason $league, int $raceId): Collection
    {
        $userSeasonRace = $this->userSeasonRaceRepository->findOneBy(['id' =>  $raceId, 'season' => $league]);

        return $userSeasonRace->getQualifications();
    }
}

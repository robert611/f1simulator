<?php

declare(strict_types=1);

namespace Domain;

use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\DTO\TeamDTO;
use Domain\Contract\DTO\TrackDTO;

interface DomainFacadeInterface
{
    /**
     * Returns a collection of team entity dto based on given drivers ids
     *
     * @param int[] $driversIds
     *
     * @return TeamDTO[]
     */
    public function getTeamsByDriversIds(array $driversIds): array;


    /**
     * Returns a collection of driver entity dto based on given drivers ids
     *
     * @param int[] $driversIds
     *
     * @return DriverDTO[]
     */
    public function getDriversByIds(array $driversIds): array;

    /**
     * Returns all drivers from database with eager loaded teams
     *
     * @return DriverDTO[]
     */
    public function getAllDrivers(): array;

    /**
     * Returns count of all tracks
     */
    public function getTracksCount(): int;

    /**
     * Returns all tracks
     *
     * @return TrackDTO[]
     */
    public function getAllTracks(): array;

    /**
     * Returns first track
     */
    public function getFirstTrack(): ?TrackDTO;

    /**
     * Returns next track in ascending order sorted by tracks ids
     *
     * @param int $previousTrackId
     */
    public function getNextTrack(int $previousTrackId): ?TrackDTO;

    /**
     * Returns track by given id
     *
     * @param int $trackId
     */
    public function getTrackById(int $trackId): ?TrackDTO;
}

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
     * Returns team by given id
     *
     * @param int $teamId
     *
     * @return TeamDTO|null
     */
    public function getTeamById(int $teamId): ?TeamDTO;

    /**
     * Returns all teams
     *
     * @return TeamDTO[]
     */
    public function getAllTeams(): array;

    /**
     * Returns driver by given id
     *
     * @param int $driverId
     *
     * @return DriverDTO|null
     */
    public function getDriverById(int $driverId): ?DriverDTO;

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
     *
     * @return TrackDTO|null
     */
    public function getNextTrack(int $previousTrackId): ?TrackDTO;

    /**
     * Returns track by given id
     *
     * @param int $trackId
     *
     * @return TrackDTO|null
     */
    public function getTrackById(int $trackId): ?TrackDTO;
}

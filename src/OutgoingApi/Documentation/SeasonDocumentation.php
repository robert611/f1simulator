<?php

declare(strict_types=1);

namespace OutgoingApi\Documentation;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SeasonDocumentation',
    title: 'Season',
    description: 'Offline season representation.',
    type: 'object',
    xml: new OA\Xml(name: 'season'),
)]
final class SeasonDocumentation
{
    #[OA\Property(
        type: 'integer',
        example: 1000,
        nullable: false,
    )]
    public int $id;

    #[OA\Property(
        type: 'integer',
        example: 1000,
        nullable: false,
    )]
    public int $driverId;

    #[OA\Property(
        type: 'integer',
        example: 1000,
        nullable: false,
    )]
    public int $userId;

    #[OA\Property(
        type: 'integer',
        example: 8,
        nullable: false,
    )]
    public int $position;

    #[OA\Property(
        type: 'boolean',
        example: false,
        nullable: false,
    )]
    public bool $completed;

    #[OA\Property(
        type: 'string',
        format: 'date-time',
        example: '2025-07-23T15:43:46Z',
        nullable: true,
    )]
    public ?string $completedAt;

    #[OA\Property(
        type: 'string',
        format: 'date-time',
        example: '2025-07-22T10:00:15Z',
        nullable: false,
    )]
    public string $createdAt;

    #[OA\Property(
        type: 'string',
        format: 'date-time',
        example: '2025-07-23T17:12:05Z',
        nullable: false,
    )]
    public string $updatedAt;
}

<?php

declare(strict_types=1);

namespace OutgoingApi\Documentation;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FormatNotAcceptableDocumentation',
    title: 'FormatNotAcceptable',
    description: 'Format not acceptable representation.',
    type: 'object',
)]
final class FormatNotAcceptableDocumentation
{
    #[OA\Property(
        type: 'title',
        example: 'Not Acceptable',
        nullable: false,
    )]
    public string $title;

    #[OA\Property(
        type: 'integer',
        example: 406,
        nullable: false,
    )]
    public int $status;

    #[OA\Property(
        type: 'string',
        example: 'Supported media types: application\json, application\xml, text\xml.',
        nullable: false,
    )]
    public string $detail;
}

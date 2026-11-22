<?php

declare(strict_types=1);

namespace OutgoingApi\ContentNegotiation;

/**
 * The order of cases is the server preference, used when the client accepts several formats with equal quality.
 * The backing value is the Symfony request format name (see Request::getMimeType()).
 */
enum ApiFormat: string
{
    case Json = 'json';

    public static function default(): self
    {
        return self::Json;
    }

    /**
     * @return non-empty-list<string>
     */
    public function mimeTypes(): array
    {
        return match ($this) {
            self::Json => ['application/json'],
        };
    }
}

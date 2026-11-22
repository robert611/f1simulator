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
    case Xml = 'xml';

    public static function default(): self
    {
        return self::Json;
    }

    /**
     * The first media type is the one sent in the "Content-Type" response header.
     *
     * @return non-empty-list<string>
     */
    public function mimeTypes(): array
    {
        return match ($this) {
            self::Json => ['application/json'],
            self::Xml => ['application/xml', 'text/xml'],
        };
    }

    /**
     * Media type of error responses (RFC 9457 "Problem Details for HTTP APIs").
     */
    public function problemMimeType(): string
    {
        return match ($this) {
            self::Json => 'application/problem+json',
            self::Xml => 'application/problem+xml',
        };
    }
}

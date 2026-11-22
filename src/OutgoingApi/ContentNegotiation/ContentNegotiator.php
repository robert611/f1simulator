<?php

declare(strict_types=1);

namespace OutgoingApi\ContentNegotiation;

use Symfony\Component\HttpFoundation\AcceptHeader;

/**
 * Picks the response format based on the "Accept" request header (RFC 9110, section 12.5.1).
 */
final readonly class ContentNegotiator
{
    /**
     * @return ApiFormat|null null when none of the supported formats is acceptable for the client
     */
    public function negotiate(?string $acceptHeader): ?ApiFormat
    {
        // No "Accept" header means the client accepts any media type
        if (null === $acceptHeader || '' === trim($acceptHeader)) {
            return ApiFormat::default();
        }

        $accept = AcceptHeader::fromString($acceptHeader);

        $bestFormat = null;
        $bestQuality = 0.0;

        foreach (ApiFormat::cases() as $format) {
            foreach ($format->mimeTypes() as $mimeType) {
                // get() resolves the most specific matching range:
                // "application/json" wins over "application/*", which wins over "*/*"
                $quality = $accept->get($mimeType)?->getQuality() ?? 0.0;

                // Strict comparison keeps the server preference (case order) on equal quality.
                // Quality 0 means "not acceptable", so such a format is never picked.
                if ($quality > $bestQuality) {
                    $bestFormat = $format;
                    $bestQuality = $quality;
                }
            }
        }

        return $bestFormat;
    }
}

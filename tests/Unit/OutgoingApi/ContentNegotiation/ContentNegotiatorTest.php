<?php

declare(strict_types=1);

namespace Tests\Unit\OutgoingApi\ContentNegotiation;

use OutgoingApi\ContentNegotiation\ApiFormat;
use OutgoingApi\ContentNegotiation\ContentNegotiator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ContentNegotiatorTest extends TestCase
{
    #[Test]
    #[DataProvider('acceptableHeaders')]
    public function it_negotiates_format(?string $acceptHeader, ApiFormat $expectedFormat): void
    {
        // given
        $negotiator = new ContentNegotiator();

        // when
        $format = $negotiator->negotiate($acceptHeader);

        // then
        self::assertSame($expectedFormat, $format);
    }

    #[Test]
    #[DataProvider('notAcceptableHeaders')]
    public function it_returns_null_when_no_supported_format_is_acceptable(string $acceptHeader): void
    {
        // given
        $negotiator = new ContentNegotiator();

        // when
        $format = $negotiator->negotiate($acceptHeader);

        // then
        self::assertNull($format);
    }

    public static function acceptableHeaders(): iterable
    {
        // JSON
        yield 'missing header' => [null, ApiFormat::Json];
        yield 'empty header' => ['', ApiFormat::Json];
        yield 'exact json media type' => ['application/json', ApiFormat::Json];
        yield 'case insensitive media type' => ['Application/JSON', ApiFormat::Json];
        yield 'any media type' => ['*/*', ApiFormat::Json];
        yield 'any application subtype' => ['application/*', ApiFormat::Json];
        yield 'unsupported type with json fallback' => ['text/html, application/json;q=0.5', ApiFormat::Json];
        yield 'excluded xml, wildcard allowed' => ['application/xml;q=0, text/xml;q=0, */*', ApiFormat::Json];
        yield 'equal quality uses server preference' => ['application/xml, application/json', ApiFormat::Json];
        yield 'json with higher quality' => ['application/xml;q=0.5, application/json', ApiFormat::Json];

        // XML
        yield 'exact xml media type' => ['application/xml', ApiFormat::Xml];
        yield 'text xml media type' => ['text/xml', ApiFormat::Xml];
        yield 'any text subtype' => ['text/*', ApiFormat::Xml];
        yield 'xml with higher quality' => ['application/json;q=0.5, application/xml', ApiFormat::Xml];
        yield 'excluded json, wildcard allowed' => ['application/json;q=0, */*', ApiFormat::Xml];
        yield 'browser default' => ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', ApiFormat::Xml];
    }

    public static function notAcceptableHeaders(): iterable
    {
        yield 'unsupported media type' => ['text/html'];
        yield 'unsupported type family' => ['image/*'];
        yield 'explicitly excluded formats' => ['application/json;q=0, application/xml;q=0, text/xml;q=0'];
        yield 'more specific exclusions win over wildcard' => [
            '*/*, application/json;q=0, application/xml;q=0, text/xml;q=0',
        ];
    }
}

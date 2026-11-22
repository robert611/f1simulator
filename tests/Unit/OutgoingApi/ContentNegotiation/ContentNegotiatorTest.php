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
    public function it_negotiates_json(?string $acceptHeader): void
    {
        // given
        $negotiator = new ContentNegotiator();

        // when
        $format = $negotiator->negotiate($acceptHeader);

        // then
        self::assertSame(ApiFormat::Json, $format);
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
        yield 'missing header' => [null];
        yield 'empty header' => [''];
        yield 'exact media type' => ['application/json'];
        yield 'case insensitive media type' => ['Application/JSON'];
        yield 'any media type' => ['*/*'];
        yield 'any application subtype' => ['application/*'];
        yield 'unsupported type with fallback' => ['text/html, application/json;q=0.5'];
        yield 'browser default' => ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'];
        yield 'excluded other type, wildcard allowed' => ['application/xml;q=0, */*'];
    }

    public static function notAcceptableHeaders(): iterable
    {
        yield 'unsupported media type' => ['application/xml'];
        yield 'unsupported type family' => ['text/*'];
        yield 'explicitly excluded json' => ['application/json;q=0'];
        yield 'more specific exclusion wins over wildcard' => ['*/*, application/json;q=0'];
    }
}

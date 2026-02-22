<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Service\TokenGenerator;

class TokenGeneratorTest extends TestCase
{
    #[Test]
    public function bin2hex_random_string_will_be_created(): void
    {
        // given
        $length = 24;

        // when
        $result = TokenGenerator::bin2hex($length);

        // then
        self::assertEquals(48, strlen($result));
    }
}

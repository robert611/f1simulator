<?php

declare(strict_types=1);

namespace Tests\Unit\Computer\Contract;

use Computer\Contract\SeasonDTO;
use Computer\Entity\Season;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\User;
use Tests\Common\PrivateProperty;

final class SeasonDTOTest extends TestCase
{
    #[Test]
    public function dto_can_be_serialized(): void
    {
        // given
        $user = new User();
        PrivateProperty::set($user, 'id', 100);

        // and given
        $season = Season::create($user, 15);
        PrivateProperty::set($season, 'id', 80);
        PrivateProperty::set($season, 'createdAt', new DateTimeImmutable('2025-10-10 17:00:00'));
        PrivateProperty::set($season, 'updatedAt', new DateTimeImmutable('2025-10-10 17:45:20'));

        // when
        $seasonDTO = SeasonDTO::fromEntity($season);

        // then
        self::assertEquals([
            'id' => 80,
            'driverId' => 15,
            'userId' => 100,
            'position' => null,
            'completed' => false,
            'completedAt' => null,
            'createdAt' => '2025-10-10T17:00:00Z',
            'updatedAt' => '2025-10-10T17:45:20Z',
        ], $seasonDTO->jsonSerialize());
    }
}

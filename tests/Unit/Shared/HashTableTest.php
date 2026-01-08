<?php

declare(strict_types=1);

namespace Tests\Unit\Shared;

use Domain\Entity\Driver;
use Domain\Entity\Team;
use Domain\Entity\Track;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\HashTable;

class HashTableTest extends TestCase
{
    #[Test]
    public function create_hash_table_from_object_array(): void
    {
        // given
        /** @var Track[] $array */
        $array = [
            Track::create('Australian', 'australian.png'),
            Track::create('Silverstone', 'silverstone.png'),
            Track::create('Belgium', 'belgium.png'),
            Track::create('Mexico', 'mexico.png'),
        ];

        // when
        $result = HashTable::fromObjectArray($array, 'getName');

        // then
        self::assertEquals($array[0], $result[$array[0]->getName()]);
        self::assertEquals($array[1], $result[$array[1]->getName()]);
        self::assertEquals($array[2], $result[$array[2]->getName()]);
        self::assertEquals($array[3], $result[$array[3]->getName()]);
    }

    #[Test]
    public function create_hash_table_from_nested_object_array(): void
    {
        // given
        $team1 = Team::create('ferrari', 'ferrari.png');
        $team2 = Team::create('mercedes', 'mercedes.png');
        $team3 = Team::create('toro_rosso', 'toro_rosso.png');

        // and given
        /** @var Driver[] $array */
        $array = [
            Driver::create('Lewis', 'Hamilton', $team1, 1),
            Driver::create('Valtteri', 'Bottas', $team2, 2),
            Driver::create('Sebastian', 'Vettel', $team3, 5),
        ];

        // when
        $result = HashTable::fromNestedObjectArray($array, 'getTeam', 'getName');

        // then
        self::assertEquals($array[0], $result['ferrari']);
        self::assertEquals($array[1], $result['mercedes']);
        self::assertEquals($array[2], $result['toro_rosso']);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

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
}

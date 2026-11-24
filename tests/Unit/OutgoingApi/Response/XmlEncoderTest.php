<?php

declare(strict_types=1);

namespace Tests\Unit\OutgoingApi\Response;

use InvalidArgumentException;
use JsonSerializable;
use OutgoingApi\Response\XmlEncoder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

final class XmlEncoderTest extends TestCase
{
    #[Test]
    public function it_encodes_scalars_and_nulls(): void
    {
        // given
        $data = [
            'id' => 7,
            'ratio' => 1.5,
            'name' => 'Ferrari',
            'completed' => false,
            'active' => true,
            'completedAt' => null,
        ];

        // when
        $xml = new XmlEncoder()->encode($data, 'season');

        // then
        self::assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<season><id>7</id><ratio>1.5</ratio><name>Ferrari</name><completed>false</completed>'
            . '<active>true</active><completedAt/></season>' . "\n",
            $xml,
        );
    }

    #[Test]
    public function it_encodes_list_with_item_name(): void
    {
        // when
        $xml = new XmlEncoder()->encode([['id' => 1], ['id' => 2]], 'seasons', 'season');

        // then
        self::assertXmlStringEqualsXmlString(
            '<seasons><season><id>1</id></season><season><id>2</id></season></seasons>',
            $xml,
        );
    }

    #[Test]
    public function it_encodes_empty_list_as_empty_root(): void
    {
        // when
        $xml = new XmlEncoder()->encode([], 'seasons', 'season');

        // then
        self::assertXmlStringEqualsXmlString('<seasons/>', $xml);
    }

    #[Test]
    public function it_encodes_json_serializable_objects(): void
    {
        // given
        $object = new class () implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['id' => 1, 'tags' => ['fast', 'red'], 'team' => ['name' => 'Ferrari']];
            }
        };

        // when
        $xml = new XmlEncoder()->encode([$object], 'seasons', 'season');

        // then
        self::assertXmlStringEqualsXmlString(
            '<seasons><season><id>1</id><tags><item>fast</item><item>red</item></tags>'
            . '<team><name>Ferrari</name></team></season></seasons>',
            $xml,
        );
    }

    #[Test]
    public function it_escapes_special_characters(): void
    {
        // when
        $xml = new XmlEncoder()->encode(['name' => 'Tom & Jerry <3'], 'driver');

        // then
        self::assertStringContainsString('<name>Tom &amp; Jerry &lt;3</name>', $xml);
    }

    #[Test]
    public function it_puts_all_elements_in_given_namespace(): void
    {
        // when
        $xml = new XmlEncoder()->encode(['title' => 'Not found'], 'problem', namespace: 'urn:ietf:rfc:7807');

        // then
        self::assertXmlStringEqualsXmlString(
            '<problem xmlns="urn:ietf:rfc:7807"><title>Not found</title></problem>',
            $xml,
        );
    }

    #[Test]
    public function it_rejects_objects_that_are_not_json_serializable(): void
    {
        // expect
        $this->expectException(InvalidArgumentException::class);

        // when
        new XmlEncoder()->encode(['team' => new stdClass()], 'season');
    }
}

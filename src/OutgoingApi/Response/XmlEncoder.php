<?php

declare(strict_types=1);

namespace OutgoingApi\Response;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Encodes outgoing API data as XML, using the same data as the JSON representation (JsonSerializable).
 *
 * Mapping rules:
 *  - associative array key  -> child element named after the key
 *  - list                   -> repeated child elements named $listItemName (nested lists use "item")
 *  - null                   -> empty element, e.g. <completedAt/>
 *  - bool                   -> "true" / "false"
 *  - int, float, string     -> text content (special characters are escaped)
 */
final readonly class XmlEncoder
{
    private const string NESTED_LIST_ITEM_NAME = 'item';

    public function encode(
        mixed $data,
        string $rootName,
        string $listItemName = self::NESTED_LIST_ITEM_NAME,
        ?string $namespace = null,
    ): string {
        $document = new DOMDocument('1.0', 'UTF-8');

        $root = $this->createElement($document, $rootName, $namespace);
        $document->appendChild($root);

        $this->appendValue($document, $root, $data, $listItemName, $namespace);

        return $document->saveXML();
    }

    private function appendValue(
        DOMDocument $document,
        DOMElement $element,
        mixed $value,
        string $listItemName,
        ?string $namespace,
    ): void {
        if ($value instanceof JsonSerializable) {
            $value = $value->jsonSerialize();
        }

        if (is_array($value)) {
            $isList = array_is_list($value);

            foreach ($value as $key => $childValue) {
                $child = $this->createElement($document, $isList ? $listItemName : (string) $key, $namespace);
                $element->appendChild($child);

                $this->appendValue($document, $child, $childValue, self::NESTED_LIST_ITEM_NAME, $namespace);
            }

            return;
        }

        if (null === $value) {
            return;
        }

        $element->appendChild($document->createTextNode($this->scalarToString($value)));
    }

    private function scalarToString(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? 'true' : 'false',
            is_int($value), is_float($value), is_string($value) => (string) $value,
            default => throw new InvalidArgumentException(
                sprintf('Value of type "%s" cannot be encoded as XML.', get_debug_type($value)),
            ),
        };
    }

    private function createElement(DOMDocument $document, string $name, ?string $namespace): DOMElement
    {
        return null === $namespace
            ? $document->createElement($name)
            : $document->createElementNS($namespace, $name);
    }
}

<?php

declare(strict_types=1);

namespace Shared;

class HashTable
{
    public static function fromObjectArray(array $array, string $method): array
    {
        $result = [];

        foreach ($array as $element) {
            $result[$element->$method()] = $element;
        }

        return $result;
    }

    public static function fromNestedObjectArray(array $array, string $method, string $secondMethod): array
    {
        $result = [];

        foreach ($array as $element) {
            $result[$element->$method()->$secondMethod()] = $element;
        }

        return $result;
    }
}

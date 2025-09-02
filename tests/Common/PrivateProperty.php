<?php

declare(strict_types=1);

namespace App\Tests\Common;

use Error;
use Exception;
use ReflectionClass;

class PrivateProperty
{
    public static function set(object $object, string $propertyName, mixed $value): void
    {
        $reflection = new ReflectionClass($object);

        try {
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        } catch (Exception | Error) {
            // Do nothing
        }
    }
}

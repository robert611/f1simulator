<?php

declare(strict_types=1);

use Behat\Config\Config;

return new Config([
    'default' => [
        'suites' => [
            'default' => [
                'paths' => [
                    'tests/Behat/Features',
                ],
                'contexts' => [
                    Tests\Behat\FeatureContext::class,
                ],
            ],
        ],
    ],
]);

<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'admin' => [
        'path' => './assets/admin.js',
        'entrypoint' => true,
    ],
    'auth' => [
        'path' => './assets/auth.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free' => [
        'version' => '7.2.0',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '7.2.0',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/all.min.css' => [
        'version' => '7.2.0',
        'type' => 'css',
    ],
    'bootstrap-icons/font/bootstrap-icons.css' => [
        'version' => '1.13.1',
        'type' => 'css',
    ],
    'react' => [
        'version' => '19.2.4',
    ],
    'scheduler' => [
        'version' => '0.27.0',
    ],
    '@symfony/ux-react' => [
        'path' => './vendor/symfony/ux-react/assets/dist/loader.js',
    ],
    '@symfony/stimulus-bridge' => [
        'version' => '4.0.1',
    ],
    'i18next' => [
        'version' => '25.8.14',
    ],
    'react-i18next' => [
        'version' => '16.5.6',
    ],
    'html-parse-stringify' => [
        'version' => '3.0.1',
    ],
    'use-sync-external-store/shim' => [
        'version' => '1.6.0',
    ],
    'void-elements' => [
        'version' => '3.1.0',
    ],
    'react-dom' => [
        'version' => '19.2.4',
    ],
    'react-dom/client' => [
        'version' => '19.2.4',
    ],
    'svgmap' => [
        'version' => '2.19.2',
    ],
    'svgmap/dist/svg-map.min.css' => [
        'version' => '2.19.2',
        'type' => 'css',
    ],
    'chart.js' => [
        'version' => '4.5.1',
    ],
    '@kurkle/color' => [
        'version' => '0.3.4',
    ],
];

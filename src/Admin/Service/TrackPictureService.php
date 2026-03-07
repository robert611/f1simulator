<?php

declare(strict_types=1);

namespace Admin\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Path;

final readonly class TrackPictureService
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function isFilenameTaken(string $filename): bool
    {
        $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');

        $fullPath = Path::join($trackPicturesDirectory, $filename);

        return is_file($fullPath);
    }

    public function remove(string $filename): void
    {
        $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');

        $fullPath = Path::join($trackPicturesDirectory, $filename);

        unlink($fullPath);
    }
}

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

    public function temporaryRename(string $filename): string
    {
        $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');

        $temporaryFilename = $this->getTemporaryFilename($filename);

        $currentPath = Path::join($trackPicturesDirectory, $filename);
        $temporaryPath = Path::join($trackPicturesDirectory, $temporaryFilename);

        if (!is_file($currentPath)) {
            return '';
        }

        rename($currentPath, $temporaryPath);

        return $temporaryFilename;
    }

    public function revertTemporaryRename(string $filename): void
    {
        $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');

        $temporaryFilename = $this->getTemporaryFilename($filename);

        $temporaryPath = Path::join($trackPicturesDirectory, $temporaryFilename);
        $originalPath = Path::join($trackPicturesDirectory, $filename);

        if (!is_file($temporaryPath)) {
            return;
        }

        rename($temporaryPath, $originalPath);
    }

    public function remove(string $filename): void
    {
        $trackPicturesDirectory = $this->parameterBag->get('track_pictures_directory');

        $fullPath = Path::join($trackPicturesDirectory, $filename);

        if (!is_file($fullPath)) {
            return;
        }

        unlink($fullPath);
    }

    private function getTemporaryFilename(string $filename): string
    {
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return sprintf('%s_temporary.%s', $basename, $extension);
    }
}

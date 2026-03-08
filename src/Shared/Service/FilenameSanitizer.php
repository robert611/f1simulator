<?php

declare(strict_types=1);

namespace Shared\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FilenameSanitizer
{
    public static function sanitize(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);

        $safeName = substr($safeName, 0, 255);

        $safeName = preg_replace('/_+/', '_', $safeName);

        $extension = $file->guessExtension();

        return $safeName . '.' . $extension;
    }
}

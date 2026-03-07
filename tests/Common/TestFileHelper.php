<?php

declare(strict_types=1);

namespace Tests\Common;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class TestFileHelper
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function anImageFile(): UploadedFile
    {
        $kernelDir = $this->parameterBag->get('kernel.project_dir');

        return new UploadedFile(
            $kernelDir . '/tests/Common/Test.jpg',
            'Test.jpg',
            'image/jpeg',
            null,
            true,
        );
    }
}

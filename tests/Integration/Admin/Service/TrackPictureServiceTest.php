<?php

declare(strict_types=1);

namespace Tests\Integration\Admin\Service;

use Admin\Service\TrackPictureService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Path;

final class TrackPictureServiceTest extends KernelTestCase
{
    private TrackPictureService $trackPictureService;
    private ParameterBagInterface $parameterBag;

    public function setUp(): void
    {
        $this->trackPictureService = self::getContainer()->get(TrackPictureService::class);
        $this->parameterBag = self::getContainer()->get(ParameterBagInterface::class);
    }

    #[Test]
    public function will_verify_that_filename_is_taken(): void
    {
        // given
        $directory = $this->parameterBag->get('kernel.project_dir') . '/assets/images/tracks';
        $filename = sprintf('track-picture-%s.png', uniqid());
        $fullPath = Path::join($directory, $filename);

        try {
            touch($fullPath);

            self::assertTrue($this->trackPictureService->isFilenameTaken($filename));
        } finally {
            if (is_file($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    #[Test]
    public function will_temporarily_rename_existing_file(): void
    {
        // given
        $directory = $this->parameterBag->get('kernel.project_dir') . '/assets/images/tracks';
        $filename = 'track-picture-test.png';

        $originalPath = Path::join($directory, $filename);
        $temporaryPath = Path::join($directory, 'track-picture-test_temporary.png');

        try {
            touch($originalPath);

            // when
            $result = $this->trackPictureService->temporaryRename($filename);

            // then
            self::assertSame('track-picture-test_temporary.png', $result);
            self::assertFileDoesNotExist($originalPath);
            self::assertFileExists($temporaryPath);
        } finally {
            if (is_file($originalPath)) {
                unlink($originalPath);
            }

            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    #[Test]
    public function will_revert_temporary_rename(): void
    {
        // given
        $directory = $this->parameterBag->get('kernel.project_dir') . '/assets/images/tracks';
        $filename = 'track-picture-test.png';

        $originalPath = Path::join($directory, $filename);
        $temporaryPath = Path::join($directory, 'track-picture-test_temporary.png');

        try {
            touch($temporaryPath);

            // when
            $this->trackPictureService->revertTemporaryRename($filename);

            // then
            self::assertFileDoesNotExist($temporaryPath);
            self::assertFileExists($originalPath);
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }

            if (is_file($originalPath)) {
                unlink($originalPath);
            }
        }
    }

    #[Test]
    public function will_verify_that_filename_is_not_taken(): void
    {
        // given
        $filename = sprintf('missing-track-picture-%s.png', uniqid());

        // when
        $result = $this->trackPictureService->isFilenameTaken($filename);

        // then
        self::assertFalse($result);
    }
}

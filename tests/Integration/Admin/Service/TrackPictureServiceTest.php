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

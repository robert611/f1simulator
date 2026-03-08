<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shared\Service\FilenameSanitizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FilenameSanitizerTest extends TestCase
{
    #[Test]
    public function testKeepsValidFilename(): void
    {
        $file = $this->createUploadedFile('track.jpg');

        $result = FilenameSanitizer::sanitize($file);

        self::assertSame('track.jpg', $result);
    }

    #[Test]
    public function it_replaces_spaces_and_special_characters(): void
    {
        // given
        $file = $this->createUploadedFile('Tor wyścigowy!!!.jpg');

        // when
        $result = FilenameSanitizer::sanitize($file);

        // then
        self::assertSame('Tor_wy_cigowy_.jpg', $result);
    }

    #[Test]
    public function it_collapses_multiple_underscores(): void
    {
        // given
        $file = $this->createUploadedFile('tor!!!super###tor.jpg');

        // when
        $result = FilenameSanitizer::sanitize($file);

        // then
        self::assertSame('tor_super_tor.jpg', $result);
    }

    #[Test]
    public function it_removes_path_traversal_characters(): void
    {
        // given
        $file = $this->createUploadedFile('../../hack.jpg');

        // when
        $result = FilenameSanitizer::sanitize($file);

        // then
        self::assertSame('hack.jpg', $result);
    }

    #[Test]
    public function it_uses_guessed_extension(): void
    {
        // given
        $file = $this->createUploadedFile('file.jpeg');

        // when
        $result = FilenameSanitizer::sanitize($file);

        // then
        self::assertSame('file.jpg', $result);
    }

    #[Test]
    public function it_does_not_allow_doubled_extensions(): void
    {
        // given
        $file = $this->createUploadedFile('file.jpeg.php');

        // when
        $result = FilenameSanitizer::sanitize($file);

        // then
        self::assertSame('file_jpeg.jpg', $result);
    }

    private function createUploadedFile(string $originalName): UploadedFile
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'upload_test');
        file_put_contents(
            $tmpFile,
            hex2bin('FFD8FFE000104A46494600010100000100010000FFDB004300'),
        );

        return new UploadedFile(
            $tmpFile,
            $originalName,
            'image/jpeg',
            null,
            true,
        );
    }
}

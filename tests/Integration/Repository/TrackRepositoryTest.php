<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Repository\TrackRepository;
use App\Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TrackRepositoryTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private TrackRepository $trackRepository;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->trackRepository = self::getContainer()->get(TrackRepository::class);
    }

    #[Test]
    public function it_checks_if_next_track_will_be_returned(): void
    {
        // when
        $this->fixtures->aTrack('Australian Grand Prix', 'australia.png');
        $bahrainTrack = $this->fixtures->aTrack('Bahrain Grand Prix', 'bahrain.png');
        $chinaTrack = $this->fixtures->aTrack('China Grand Prix', 'china.png');

        // when
        $nextTrack = $this->trackRepository->getNextTrack($bahrainTrack->getId());

        // then
        self::assertEquals($chinaTrack, $nextTrack);
    }

    #[Test]
    public function it_checks_if_not_existing_next_track_can_be_handled(): void
    {
        // when
        $this->fixtures->aTrack('Australian Grand Prix', 'australia.png');
        $this->fixtures->aTrack('Bahrain Grand Prix', 'bahrain.png');
        $chinaTrack = $this->fixtures->aTrack('China Grand Prix', 'china.png');

        // when
        $nextTrack = $this->trackRepository->getNextTrack($chinaTrack->getId());

        // then
        self::assertNull($nextTrack);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Repository;

use Domain\Repository\TrackRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

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
    public function it_checks_if_first_track_will_be_returned(): void
    {
        // given
        $australianTrack = $this->fixtures->aTrack('Australian Grand Prix', 'australia.png');
        $this->fixtures->aTrack('Bahrain Grand Prix', 'bahrain.png');
        $this->fixtures->aTrack('China Grand Prix', 'china.png');

        // when
        $firstTrack = $this->trackRepository->getFirstTrack();

        // then
        self::assertEquals($australianTrack, $firstTrack);
    }

    #[Test]
    public function it_checks_if_getting_first_track_when_there_are_none_will_return_null(): void
    {
        // when
        $firstTrack = $this->trackRepository->getFirstTrack();

        // then
        self::assertNull($firstTrack);
    }

    #[Test]
    public function it_checks_if_next_track_will_be_returned(): void
    {
        // given
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
        // given
        $this->fixtures->aTrack('Australian Grand Prix', 'australia.png');
        $this->fixtures->aTrack('Bahrain Grand Prix', 'bahrain.png');
        $chinaTrack = $this->fixtures->aTrack('China Grand Prix', 'china.png');

        // when
        $nextTrack = $this->trackRepository->getNextTrack($chinaTrack->getId());

        // then
        self::assertNull($nextTrack);
    }
}

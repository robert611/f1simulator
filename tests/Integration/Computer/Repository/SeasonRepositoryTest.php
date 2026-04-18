<?php

declare(strict_types=1);

namespace Tests\Integration\Computer\Repository;

use Computer\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\FixedClock;
use Tests\Common\Fixtures;

final class SeasonRepositoryTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private SeasonRepository $seasonRepository;
    private EntityManagerInterface $entityManager;
    private FixedClock $clock;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->seasonRepository = self::getContainer()->get(SeasonRepository::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->clock = self::getContainer()->get(FixedClock::class);
    }

    #[Test]
    public function last_12_months_seasons_played_will_be_returned(): void
    {
        // given
        $user1 = $this->fixtures->aCustomUser('frank789', 'frank789@gmail.com');
        $user2 = $this->fixtures->aCustomUser('ross9241', 'ross9241@gmail.com');
        $user3 = $this->fixtures->aCustomUser('chandler831156', 'chandler831156@gmail.com');

        // and given
        $teamForceIndia = $this->fixtures->aTeamWithName('Force India');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');

        // and given
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamForceIndia, 16);
        $driver2 = $this->fixtures->aDriver('Justin', 'Russo', $teamForceIndia, 55);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);
        $driver4 = $this->fixtures->aDriver('Sergio', 'Perez', $teamRedBull, 11);
        $driver5 = $this->fixtures->aDriver('Alex', 'Gomez', $teamMercedes, 12);

        // and given
        $season1 = $this->fixtures->aSeason($user1, $driver1);
        $season2 = $this->fixtures->aSeason($user2, $driver2);
        $season3 = $this->fixtures->aSeason($user3, $driver3);
        $season4 = $this->fixtures->aSeason($user3, $driver4);
        $season5 = $this->fixtures->aSeason($user3, $driver4);
        $season6 = $this->fixtures->aSeason($user3, $driver4);
        $this->fixtures->aSeason($user3, $driver5);

        // and given
        $season1->endSeason($this->clock->now('2025-07-10 10:00:00'));
        $season2->endSeason($this->clock->now('2025-02-10 12:30:00'));
        $season3->endSeason($this->clock->now('2025-01-09 17:50:00'));
        $season4->endSeason($this->clock->now('2024-11-09 17:50:00'));
        $season5->endSeason($this->clock->now('2024-11-01 08:00:00'));
        $season6->endSeason($this->clock->now('2024-10-09 17:50:00'));

        // and given
        $this->entityManager->flush();

        // and given
        $this->clock->setNow('2025-10-10 10:00:00');

        // when
        $result = $this->seasonRepository->getLast12MonthsSeasonPlayed();

        // then
        self::assertCount(4, $result);
        self::assertSame(['month' => 11, 'year' => 2024, 'seasonsPlayed' => 2], $result[0]);
        self::assertSame(['month' => 1, 'year' => 2025, 'seasonsPlayed' => 1], $result[1]);
        self::assertSame(['month' => 2, 'year' => 2025, 'seasonsPlayed' => 1], $result[2]);
        self::assertSame(['month' => 7, 'year' => 2025, 'seasonsPlayed' => 1], $result[3]);
    }
}

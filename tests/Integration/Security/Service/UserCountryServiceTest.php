<?php

declare(strict_types=1);

namespace Tests\Integration\Security\Service;

use PHPUnit\Framework\Attributes\Test;
use Security\Entity\UserCountry;
use Security\Service\UserCountryService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

final class UserCountryServiceTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private UserCountryService $userCountryService;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userCountryService = self::getContainer()->get(UserCountryService::class);
    }

    #[Test]
    public function it_returns_valid_user_country_map_data(): void
    {
        // given
        $this->fixtures->aCustomUser('user1', 'user1@gmail.com');
        $this->fixtures->aCustomUser('user2', 'user2@gmail.com');
        $this->fixtures->aCustomUser('user3', 'user3@gmail.com', UserCountry::GB);
        $this->fixtures->aCustomUser('user4', 'user4@gmail.com', UserCountry::US);

        // when
        $result = $this->userCountryService->getUserCountryMapData();

        // then
        self::assertCount(3, $result);
        self::assertEquals(['country' => 'GB', 'users' => 1, 'percentageOfAllUsers' => 25.0], $result['GB']);
        self::assertEquals(['country' => 'PL', 'users' => 2, 'percentageOfAllUsers' => 50.0], $result['PL']);
        self::assertEquals(['country' => 'US', 'users' => 1, 'percentageOfAllUsers' => 25.0], $result['US']);
    }
}

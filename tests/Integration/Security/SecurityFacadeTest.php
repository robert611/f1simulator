<?php

declare(strict_types=1);

namespace Tests\Integration\Security;

use PHPUnit\Framework\Attributes\Test;
use Security\Entity\UserCountry;
use Security\SecurityFacade;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

final class SecurityFacadeTest extends KernelTestCase
{
    private SecurityFacade $securityFacade;
    private Fixtures $fixtures;

    protected function setUp(): void
    {
        $this->securityFacade = self::getContainer()->get(SecurityFacade::class);
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_returns_all_users_dto(): void
    {
        // given
        $user1 = $this->fixtures->aCustomUser('mark17821', 'mark17821@gmail.com', UserCountry::GB);
        $user2 = $this->fixtures->aCustomUser('speeding_jack', 'speeding_jack@gmail.com', UserCountry::GB);
        $user3 = $this->fixtures->aCustomUser('jack_sparrow', 'jack_sparrow@gmail.com', UserCountry::US);

        // when
        $result = $this->securityFacade->getUsers();

        // then
        self::assertCount(3, $result);
        self::assertEquals($user1->getId(), $result[0]->getId());
        self::assertEquals($user2->getId(), $result[1]->getId());
        self::assertEquals($user3->getId(), $result[2]->getId());
    }
}

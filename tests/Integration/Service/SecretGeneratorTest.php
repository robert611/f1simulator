<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Common\Fixtures;
use Multiplayer\Service\SecretGenerator;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SecretGeneratorTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private SecretGenerator $secretGenerator;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->secretGenerator = self::getContainer()->get(SecretGenerator::class);
    }

    #[Test]
    public function it_checks_if_league_secret_will_be_unique(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");

        // and given
        $userSeason = $this->fixtures->aUserSeason(
            "J783NMS092C",
            10,
            $owner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // when
        $secret = $this->secretGenerator->getLeagueUniqueSecret();

        // then
        self::assertNotEquals($userSeason->getSecret(), $secret);
    }

    #[Test]
    public function it_checks_if_secret_is_generated_according_to_rules(): void
    {
        // when
        $secret = SecretGenerator::getSecret();

        // then
        self::assertEquals(12, strlen($secret));

        // and then (No special characters are in the string)
        self::assertEquals(0, preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $secret));
    }
}

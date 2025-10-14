<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Service\SecretGenerator;
use App\Tests\Common\Fixtures;
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

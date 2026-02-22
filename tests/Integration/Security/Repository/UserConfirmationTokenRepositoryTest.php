<?php

declare(strict_types=1);

namespace Tests\Integration\Security\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Security\Repository\UserConfirmationTokenRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

final class UserConfirmationTokenRepositoryTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private UserConfirmationTokenRepository $userConfirmationTokenRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userConfirmationTokenRepository = self::getContainer()->get(UserConfirmationTokenRepository::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    #[Test]
    public function it_invalidates_all_user_tokens(): void
    {
        // given
        $user1 = $this->fixtures->aUser();
        $user2 = $this->fixtures->aCustomUser('john_doe', 'john.doe@example.com');

        $expiryAt = new DateTimeImmutable('+1 hour');
        
        // and given - create multiple tokens for user1
        $token1 = $this->fixtures->aUserConfirmationToken($user1, 'token1', $expiryAt);
        $token2 = $this->fixtures->aUserConfirmationToken($user1, 'token2', $expiryAt);
        $token3 = $this->fixtures->aUserConfirmationToken($user1, 'token3', $expiryAt);
        
        // and given - create token for user2 (should not be affected)
        $token4 = $this->fixtures->aUserConfirmationToken($user2, 'token4', $expiryAt);

        // then - initially all tokens should be valid
        self::assertTrue($token1->isValid());
        self::assertTrue($token2->isValid());
        self::assertTrue($token3->isValid());
        self::assertTrue($token4->isValid());

        // when
        $this->userConfirmationTokenRepository->invalidateUserTokens($user1->getId());
        $this->entityManager->clear();

        // then - refresh entities from database
        $token1 = $this->userConfirmationTokenRepository->find($token1->getId());
        $token2 = $this->userConfirmationTokenRepository->find($token2->getId());
        $token3 = $this->userConfirmationTokenRepository->find($token3->getId());
        $token4 = $this->userConfirmationTokenRepository->find($token4->getId());

        // then - user1 tokens should be invalidated
        self::assertFalse($token1->isValid());
        self::assertFalse($token2->isValid());
        self::assertFalse($token3->isValid());
        
        // then - user2 token should remain valid
        self::assertTrue($token4->isValid());
    }

    #[Test]
    public function it_returns_last_created_token_date_for_user(): void
    {
        // given
        $user1 = $this->fixtures->aUser();
        $user2 = $this->fixtures->aCustomUser('jane_doe', 'jane.doe@example.com');

        $expiryAt = new DateTimeImmutable('+1 hour');

        // and given
        $this->fixtures->aUserConfirmationToken($user1, 'token1', $expiryAt);
        sleep(1);
        $secondToken = $this->fixtures->aUserConfirmationToken($user1, 'token2', $expiryAt);

        // and given - create token for user2 (should not affect user1 result)
        $this->fixtures->aUserConfirmationToken($user2, 'token3', $expiryAt);

        // when
        $result = $this->userConfirmationTokenRepository->findLastTokenForUser($user1->getId());

        // then
        self::assertEquals($secondToken, $result);
    }

    #[Test]
    public function it_returns_null_when_user_has_no_tokens(): void
    {
        // given
        $user1 = $this->fixtures->aUser();
        $user2 = $this->fixtures->aCustomUser('other_user', 'other@example.com');

        // and given
        $this->fixtures->aUserConfirmationToken($user2, 'token1', new DateTimeImmutable('+1 hour'));

        // when
        $result = $this->userConfirmationTokenRepository->findLastTokenForUser($user1->getId());

        // then
        self::assertNull($result);
    }
}

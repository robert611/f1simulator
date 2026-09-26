<?php

declare(strict_types=1);

namespace Tests\Unit\Security\Contract;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Contract\UserDTO;
use Security\Entity\User;
use Security\Entity\UserCountry;
use Tests\Common\PrivateProperty;

final class UserDTOTest extends TestCase
{
    #[Test]
    public function it_builds_user_dto_with_properly_mapped_fields(): void
    {
        // given
        $user = new User();
        $user->setUsername('mikey');
        $user->setEmail('mikey@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('password');
        $user->setIsVerified(true);
        $user->setCountry(UserCountry::PL);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setUpdatedAt(new DateTimeImmutable());

        // and given
        PrivateProperty::set($user, 'id', 1000);

        // when
        $result = UserDTO::fromEntity($user);

        // then
        self::assertSame(1000, $result->getId());
        self::assertSame('mikey', $result->getUsername());
        self::assertSame('mikey@gmail.com', $result->getEmail());
        self::assertSame(['ROLE_USER'], $result->getRoles());
        self::assertTrue($result->isVerified());
        self::assertSame(UserCountry::PL, $result->getCountry());
        self::assertSame($user->getCreatedAt(), $result->getCreatedAt());
        self::assertSame($user->getUpdatedAt(), $result->getUpdatedAt());
    }

    #[Test]
    public function it_builds_user_dto_collection(): void
    {
        // given
        $user1 = new User();
        $user1->setUsername('marley');
        $user1->setEmail('marley@gmail.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword('marley_password');
        $user1->setIsVerified(true);
        $user1->setCountry(UserCountry::PL);
        $user1->setCreatedAt(new DateTimeImmutable());
        $user1->setUpdatedAt(new DateTimeImmutable());

        // and given
        $user2 = new User();
        $user2->setUsername('quinn');
        $user2->setEmail('quinn@gmail.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword('quinn_password');
        $user2->setIsVerified(true);
        $user2->setCountry(UserCountry::US);
        $user2->setCreatedAt(new DateTimeImmutable());
        $user2->setUpdatedAt(new DateTimeImmutable());

        // and given
        PrivateProperty::set($user1, 'id', 1000);
        PrivateProperty::set($user2, 'id', 1001);

        // when
        $result = UserDTO::fromEntityCollection([$user1, $user2]);

        // and then
        self::assertCount(2, $result);
        self::assertInstanceOf(UserDTO::class, $result[0]);
        self::assertInstanceOf(UserDTO::class, $result[1]);
    }
}

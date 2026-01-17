<?php

declare(strict_types=1);

namespace DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Security\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';

    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->getUsers();

        foreach ($users as $key => $data) {
            $user = new User();

            $user->setUsername($data['username']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->encoder->hashPassword($user, $data['password']));
            $user->setEmail($data['email']);

            $manager->persist($user);
            $manager->flush();

            $this->addReference('user.' . ($key + 1), $user);
        }
    }

    public function getUsers(): array
    {
        return [
            ['username' => 'Johny', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'email@interia.pl'],
            ['username' => 'Tomy', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'email@wp.pl'],
            ['username' => 'Vicky', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'email@example.pl'],
            ['username' => 'Julia', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'test2@example.pl'],
            ['username' => 'Michael', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'box@example.pl'],
        ];
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->getUsers();

        foreach ($users as $key => $userData) {
            $user = new User();

            $user->setUsername($userData['username']);
            $user->setRoles($userData['roles']);
            $user->setPassword($this->encoder->encodePassword($user, $userData['password']));
            $user->setEmail($userData['email']);

            $manager->persist($user);
            $manager->flush();

            $this->addReference('user.' . ($key + 1), $user);
        }
    }

    public function getUsers()
    {
        return [
            ['username' => 'Johny', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'email@interia.pl'],
            ['username' => 'Tomy', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'email@wp.pl'],
            ['username' => 'Vicky', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'email@example.pl']
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class NuevoUsuario extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']); // rol básico
        $user->setToken(bin2hex(random_bytes(32)));

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            '123456'
        );

        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}

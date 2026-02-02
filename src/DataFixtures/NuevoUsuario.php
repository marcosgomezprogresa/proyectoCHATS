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
        $usersData = [
            ['email' => 'user@user.com',   'nombre' => 'Usuario Prueba', 'lat' => 40.4168, 'lng' => -3.7038],
            ['email' => 'maria@example.com','nombre' => 'María',          'lat' => 40.4200, 'lng' => -3.7020],
            ['email' => 'carlos@example.com','nombre' => 'Carlos',         'lat' => 40.4120, 'lng' => -3.7100],
            ['email' => 'laura@example.com', 'nombre' => 'Laura',          'lat' => 40.4180, 'lng' => -3.6950],
        ];

        foreach ($usersData as $uData) {
            $u = new User();
            $u->setEmail($uData['email']);
            $u->setNombre($uData['nombre']);
            $u->setRoles(['ROLE_USER']); // rol básico
            $u->setToken(bin2hex(random_bytes(32)));

            // Ubicación y visibilidad
            $u->setLatitud($uData['lat']);
            $u->setLongitud($uData['lng']);
            $u->setCompartirUbicacion(true);
            $u->setRadioVisibilidadKm(5.0);
            $u->setActivo(true);
            $u->setUltimaUbicacion(new \DateTime());

            $hashedPassword = $this->passwordHasher->hashPassword(
                $u,
                '123456'
            );

            $u->setPassword($hashedPassword);
            $manager->persist($u);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\User;
use App\Entity\UsuarioChat;
use App\Enum\EstadoUsuario;
use App\Enum\TipoChat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ============ CREAR USUARIOS PERMANENTES ============
        // 1 ADMIN y 3 usuarios normales
        $usuarios = [];
        
        // USUARIO ADMIN (Valencia, España - Centro)
        $admin = new User();
        $admin->setEmail('admin@chat.com');
        $admin->setNombre('Administrador');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setEstado(EstadoUsuario::ONLINE);
        $admin->setLatitud(39.4699);  // Valencia
        $admin->setLongitud(-0.3763);
        $admin->setUltimaUbicacion(new \DateTime());
        
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);
        $admin->setToken(bin2hex(random_bytes(32)));
        
        $manager->persist($admin);
        $usuarios[] = $admin;
        
        // USUARIOS NORMALES
        $usuariosData = [
            ['email' => 'moderador@chat.com', 'nombre' => 'Moderador', 'password' => 'mod123', 'lat' => 39.4720, 'lon' => -0.3750],
            ['email' => 'soporte@chat.com', 'nombre' => 'Soporte', 'password' => 'soporte123', 'lat' => 39.4680, 'lon' => -0.3780],
            ['email' => 'bot_general@chat.com', 'nombre' => 'Bot General', 'password' => 'bot123', 'lat' => 39.4700, 'lon' => -0.3750],
        ];

        foreach ($usuariosData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setNombre($data['nombre']);
            $user->setRoles(['ROLE_USER']); // Solo ROLE_USER
            $user->setEstado(EstadoUsuario::ONLINE);
            $user->setLatitud($data['lat']);
            $user->setLongitud($data['lon']);
            $user->setUltimaUbicacion(new \DateTime());
            
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            $user->setToken(bin2hex(random_bytes(32)));
            
            $manager->persist($user);
            $usuarios[] = $user;
        }

        $manager->flush();

        // ============ CREAR CHATS PÚBLICOS ============
        $chatsData = [
            ['nombre' => 'General', 'descripcion' => 'Chat general para todos'],
            ['nombre' => 'Random', 'descripcion' => 'Conversaciones al azar'],
            ['nombre' => 'Soporte', 'descripcion' => 'Ayuda y soporte técnico'],
            ['nombre' => 'Entretenimiento', 'descripcion' => 'Compartir memes y videos'],
            ['nombre' => 'Tecnología', 'descripcion' => 'Hablar de tech'],
        ];

        $chats = [];
        foreach ($chatsData as $data) {
            $chat = new Chat();
            $chat->setNombre($data['nombre']);
            $chat->setDescripcion($data['descripcion']);
            $chat->setTipo(TipoChat::GENERAL);
            $chat->setActivo(true);
            $chat->setFechaCreacion(new \DateTimeImmutable());
            $chat->setRadioKm(100.0);
            
            $manager->persist($chat);
            $chats[] = $chat;
        }

        $manager->flush();

        // ============ AGREGAR USUARIOS A LOS CHATS ============
        foreach ($chats as $chat) {
            foreach ($usuarios as $user) {
                $usuarioChat = new UsuarioChat();
                $usuarioChat->setChat($chat);
                $usuarioChat->setUsuario($user);
                $usuarioChat->setFechaUnion(new \DateTimeImmutable());
                $usuarioChat->setSilenciado(false);
                $usuarioChat->setFijado(false);
                $usuarioChat->setNotificaciones(true);
                $usuarioChat->setMensajesNoLeidos(0);
                $usuarioChat->setEsAdmin(true);
                $usuarioChat->setUltimoAcceso(new \DateTimeImmutable());
                $usuarioChat->setEstaActivo(true);
                
                $manager->persist($usuarioChat);
            }
        }

        $manager->flush();
    }
}

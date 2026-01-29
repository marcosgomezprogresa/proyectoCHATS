<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 64, unique: true)]
    private ?string $token = null;

    // ============ CAMPOS NUEVOS DEL UML ============
    
    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(length: 20)]
    private string $estado = 'offline'; // online, ausente, offline

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $latitud = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $longitud = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ultimaUbicacion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaRegistro = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ultimaActividad = null;

    // ============ CONSTRUCTOR ============
    
    public function __construct()
    {
        $this->fechaRegistro = new \DateTime();
        $this->ultimaActividad = new \DateTime();
    }

    // ============ GETTERS Y SETTERS NUEVOS ============
    
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function getLatitud(): ?float
    {
        return $this->latitud;
    }

    public function setLatitud(?float $latitud): static
    {
        $this->latitud = $latitud;
        return $this;
    }

    public function getLongitud(): ?float
    {
        return $this->longitud;
    }

    public function setLongitud(?float $longitud): static
    {
        $this->longitud = $longitud;
        return $this;
    }

    public function getUltimaUbicacion(): ?\DateTimeInterface
    {
        return $this->ultimaUbicacion;
    }

    public function setUltimaUbicacion(?\DateTimeInterface $ultimaUbicacion): static
    {
        $this->ultimaUbicacion = $ultimaUbicacion;
        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeInterface
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(\DateTimeInterface $fechaRegistro): static
    {
        $this->fechaRegistro = $fechaRegistro;
        return $this;
    }

    public function getUltimaActividad(): ?\DateTimeInterface
    {
        return $this->ultimaActividad;
    }

    public function setUltimaActividad(\DateTimeInterface $ultimaActividad): static
    {
        $this->ultimaActividad = $ultimaActividad;
        return $this;
    }

    // ============ MÉTODOS EXISTENTES (MANTENIDOS) ============
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Añade ROLE_USER por defecto y ROLE_ADMIN si está en el array
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Método helper para añadir rol de ADMIN
     */
    public function addAdminRole(): static
    {
        if (!in_array('ROLE_ADMIN', $this->roles, true)) {
            $this->roles[] = 'ROLE_ADMIN';
        }
        return $this;
    }

    /**
     * Método helper para quitar rol de ADMIN
     */
    public function removeAdminRole(): static
    {
        $key = array_search('ROLE_ADMIN', $this->roles, true);
        if ($key !== false) {
            unset($this->roles[$key]);
        }
        return $this;
    }

    /**
     * Verifica si el usuario tiene rol ADMIN
     */
    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles, true);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }
}
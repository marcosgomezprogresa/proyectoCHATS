<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Enum\EstadoUsuario;
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

    #[ORM\Column(type: 'string', enumType: EstadoUsuario::class)]
    private EstadoUsuario $estado = EstadoUsuario::OFFLINE;

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

    /**
     * @var Collection<int, Bloqueo>
     */
    #[ORM\OneToMany(targetEntity: Bloqueo::class, mappedBy: 'bloqueador')]
    private Collection $bloqueos;

    /**
     * @var Collection<int, UsuarioChat>
     */
    #[ORM\OneToMany(targetEntity: UsuarioChat::class, mappedBy: 'usuario')]
    private Collection $usuariosChat;

    /**
     * @var Collection<int, Mensaje>
     */
    #[ORM\OneToMany(targetEntity: Mensaje::class, mappedBy: 'remitente')]
    private Collection $mensajes;

    /**
     * @var Collection<int, Invitacion>
     */
    #[ORM\OneToMany(targetEntity: Invitacion::class, mappedBy: 'invitador')]
    private Collection $invitacions;

    // ============ CONSTRUCTOR ============
    
    public function __construct()
    {
        $this->fechaRegistro = new \DateTime();
        $this->ultimaActividad = new \DateTime();
        $this->bloqueos = new ArrayCollection();
        $this->usuariosChat = new ArrayCollection();
        $this->mensajes = new ArrayCollection();
        $this->invitacions = new ArrayCollection();
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

    public function getEstado(): EstadoUsuario
    {
        return $this->estado;
    }

    public function setEstado(EstadoUsuario $estado): static
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

    /**
     * @return Collection<int, Bloqueo>
     */
    public function getBloqueos(): Collection
    {
        return $this->bloqueos;
    }

    public function addBloqueo(Bloqueo $bloqueo): static
    {
        if (!$this->bloqueos->contains($bloqueo)) {
            $this->bloqueos->add($bloqueo);
            $bloqueo->setBloqueador($this);
        }

        return $this;
    }

    public function removeBloqueo(Bloqueo $bloqueo): static
    {
        if ($this->bloqueos->removeElement($bloqueo)) {
            // set the owning side to null (unless already changed)
            if ($bloqueo->getBloqueador() === $this) {
                $bloqueo->setBloqueador(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UsuarioChat>
     */
    public function getUsuariosChat(): Collection
    {
        return $this->usuariosChat;
    }

    public function addUsuarioChat(UsuarioChat $usuarioChat): static
    {
        if (!$this->usuariosChat->contains($usuarioChat)) {
            $this->usuariosChat->add($usuarioChat);
            $usuarioChat->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioChat(UsuarioChat $usuarioChat): static
    {
        if ($this->usuariosChat->removeElement($usuarioChat)) {
            // set the owning side to null (unless already changed)
            if ($usuarioChat->getUsuario() === $this) {
                $usuarioChat->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mensaje>
     */
    public function getMensajes(): Collection
    {
        return $this->mensajes;
    }

    public function addMensaje(Mensaje $mensaje): static
    {
        if (!$this->mensajes->contains($mensaje)) {
            $this->mensajes->add($mensaje);
            $mensaje->setRemitente($this);
        }

        return $this;
    }

    public function removeMensaje(Mensaje $mensaje): static
    {
        if ($this->mensajes->removeElement($mensaje)) {
            // set the owning side to null (unless already changed)
            if ($mensaje->getRemitente() === $this) {
                $mensaje->setRemitente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitacion>
     */
    public function getInvitacions(): Collection
    {
        return $this->invitacions;
    }

    public function addInvitacion(Invitacion $invitacion): static
    {
        if (!$this->invitacions->contains($invitacion)) {
            $this->invitacions->add($invitacion);
            $invitacion->setInvitador($this);
        }

        return $this;
    }

    public function removeInvitacion(Invitacion $invitacion): static
    {
        if ($this->invitacions->removeElement($invitacion)) {
            // set the owning side to null (unless already changed)
            if ($invitacion->getInvitador() === $this) {
                $invitacion->setInvitador(null);
            }
        }

        return $this;
    }
}
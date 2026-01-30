<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use App\Enum\TipoChat;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'string', enumType: TipoChat::class)]
    private ?TipoChat $tipo = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column]
    private ?bool $activo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaCreacion = null;

    #[ORM\Column]
    private ?float $radioKm = null;

    /**
     * @var Collection<int, Mensaje>
     */
    #[ORM\OneToMany(targetEntity: Mensaje::class, mappedBy: 'chat')]
    private Collection $mensajes;

    /**
     * @var Collection<int, Invitacion>
     */
    #[ORM\OneToMany(targetEntity: Invitacion::class, mappedBy: 'chat')]
    private Collection $invitacions;

    public function __construct()
    {
        $this->mensajes = new ArrayCollection();
        $this->invitacions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getTipo(): ?TipoChat
    {
        return $this->tipo;
    }

    public function setTipo(TipoChat $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeImmutable $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getRadioKm(): ?float
    {
        return $this->radioKm;
    }

    public function setRadioKm(float $radioKm): static
    {
        $this->radioKm = $radioKm;

        return $this;
    }

    // ============ MÉTODOS DEL UML ============
    
    /**
     * Obtiene cantidad de usuarios en el chat
     * Se implementará cuando tengamos la relación con usuarios
     */
    public function obtenerCantidadUsuarios(): int
    {
        // TODO: Implementar cuando tengamos la relación con UsuarioChat
        return 0;
    }

    /**
     * Elimina el chat (marca como inactivo)
     */
    public function eliminarChat(): void
    {
        $this->activo = false;
    }

    /**
     * Verifica si el chat está vacío y debe borrarse automáticamente
     * 
     * @return bool True si el chat fue borrado, False si sigue activo
     */
    public function verificarYBorrarSiVacio(): bool
    {
        // El chat general NUNCA se borra
        if ($this->esChatGeneral()) {
            return false;
        }
        
        // Si no hay usuarios, se elimina
        if ($this->obtenerCantidadUsuarios() === 0) {
            $this->eliminarChat();
            return true;
        }
        
        return false;
    }

    /**
     * Verifica si es el chat general
     */
    public function esChatGeneral(): bool
    {
        return $this->tipo === TipoChat::GENERAL && $this->nombre === 'Chat General';
    }

    /**
     * Verifica si un usuario puede unirse a este chat basado en la distancia
     * (Para implementar cuando tengamos la entidad Usuario)
     */
    public function puedeUnirse($usuario): bool
    {
        // TODO: Implementar lógica de distancia
        // return $this->calcularDistancia($usuario) <= $this->radioKm;
        return true;
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
            $mensaje->setChat($this);
        }

        return $this;
    }

    public function removeMensaje(Mensaje $mensaje): static
    {
        if ($this->mensajes->removeElement($mensaje)) {
            // set the owning side to null (unless already changed)
            if ($mensaje->getChat() === $this) {
                $mensaje->setChat(null);
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
            $invitacion->setChat($this);
        }

        return $this;
    }

    public function removeInvitacion(Invitacion $invitacion): static
    {
        if ($this->invitacions->removeElement($invitacion)) {
            // set the owning side to null (unless already changed)
            if ($invitacion->getChat() === $this) {
                $invitacion->setChat(null);
            }
        }

        return $this;
    }
}
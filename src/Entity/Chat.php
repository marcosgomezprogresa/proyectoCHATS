<?php

namespace App\Entity;

use App\Repository\ChatRepository;
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

    #[ORM\Column(length: 20)]
    private ?string $tipo = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column]
    private ?bool $activo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaCreacion = null;

    #[ORM\Column]
    private ?float $radioKm = null;

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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
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
        if ($this->tipo === 'general' && $this->nombre === 'Chat General') {
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
        return $this->tipo === 'general' && $this->nombre === 'Chat General';
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
}
<?php

namespace App\Entity;

use App\Repository\UsuarioChatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioChatRepository::class)]
class UsuarioChat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'usuariosChat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'usuariosChat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chat $chat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaUnion = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $fechaSalida = null;

    #[ORM\Column]
    private ?bool $silenciado = null;  // <-- AÑADIR ESTE CAMPO

    #[ORM\Column]
    private ?bool $fijado = null;      // <-- AÑADIR ESTE CAMPO

    #[ORM\Column]
    private ?bool $notificaciones = null;

    #[ORM\Column]
    private ?int $mensajesNoLeidos = null;

    #[ORM\Column]
    private ?bool $esAdmin = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $ultimoAcceso = null;  // <-- CORREGIR: datetime_immutable

    #[ORM\Column]
    private ?bool $estaActivo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaUnion(): ?\DateTimeImmutable
    {
        return $this->fechaUnion;
    }

    public function setFechaUnion(\DateTimeImmutable $fechaUnion): static
    {
        $this->fechaUnion = $fechaUnion;

        return $this;
    }

    public function getFechaSalida(): ?\DateTimeImmutable
    {
        return $this->fechaSalida;
    }

    public function setFechaSalida(?\DateTimeImmutable $fechaSalida): static
    {
        $this->fechaSalida = $fechaSalida;

        return $this;
    }

    // ============ CAMPOS NUEVOS ============
    
    public function isSilenciado(): ?bool
    {
        return $this->silenciado;
    }

    public function setSilenciado(bool $silenciado): static
    {
        $this->silenciado = $silenciado;
        return $this;
    }

    public function isFijado(): ?bool
    {
        return $this->fijado;
    }

    public function setFijado(bool $fijado): static
    {
        $this->fijado = $fijado;
        return $this;
    }

    // ============ CAMPOS EXISTENTES ============
    
    public function isNotificaciones(): ?bool
    {
        return $this->notificaciones;
    }

    public function setNotificaciones(bool $notificaciones): static
    {
        $this->notificaciones = $notificaciones;
        return $this;
    }

    public function getMensajesNoLeidos(): ?int
    {
        return $this->mensajesNoLeidos;
    }

    public function setMensajesNoLeidos(int $mensajesNoLeidos): static
    {
        $this->mensajesNoLeidos = $mensajesNoLeidos;
        return $this;
    }

    public function isEsAdmin(): ?bool
    {
        return $this->esAdmin;
    }

    public function setEsAdmin(bool $esAdmin): static
    {
        $this->esAdmin = $esAdmin;
        return $this;
    }

    // ============ CAMPO CORREGIDO ============
    
    public function getUltimoAcceso(): ?\DateTimeImmutable  // <-- Cambiado a DateTimeImmutable
    {
        return $this->ultimoAcceso;
    }

    public function setUltimoAcceso(?\DateTimeImmutable $ultimoAcceso): static  // <-- Cambiado
    {
        $this->ultimoAcceso = $ultimoAcceso;
        return $this;
    }

    public function isEstaActivo(): ?bool
    {
        return $this->estaActivo;
    }

    public function setEstaActivo(bool $estaActivo): static
    {
        $this->estaActivo = $estaActivo;
        return $this;
    }

    // ============ MÉTODOS DEL UML ============
    
    /**
     * Abandona el chat - MÉTODO CLAVE PARA EL AUTO-BORRADO
     */
    public function abandonarChat(): bool
    {
        $this->estaActivo = false;
        $this->fechaSalida = new \DateTimeImmutable();
        
        // La lógica completa se implementará cuando tengamos las relaciones
        // Retorna true si el chat fue borrado, false si sigue activo
        return false;
    }

    /**
     * Silencia las notificaciones del chat
     */
    public function silenciar(): void
    {
        $this->silenciado = true;
        $this->notificaciones = false;
    }

    /**
     * Desilencia las notificaciones
     */
    public function desilenciar(): void
    {
        $this->silenciado = false;
        $this->notificaciones = true;
    }

    /**
     * Marca todos los mensajes como leídos
     */
    public function marcarComoLeido(): void
    {
        $this->mensajesNoLeidos = 0;
        $this->ultimoAcceso = new \DateTimeImmutable();
    }

    /**
     * Incrementa el contador de mensajes no leídos
     */
    public function incrementarNoLeidos(): void
    {
        $this->mensajesNoLeidos++;
    }

    /**
     * Activa las notificaciones
     */
    public function activarNotificaciones(): void
    {
        $this->notificaciones = true;
        $this->silenciado = false;
    }

    /**
     * Desactiva las notificaciones
     */
    public function desactivarNotificaciones(): void
    {
        $this->notificaciones = false;
        $this->silenciado = true;
    }

    // ============ RELACIONES NUEVAS ============

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    public function setChat(?Chat $chat): static
    {
        $this->chat = $chat;

        return $this;
    }
}
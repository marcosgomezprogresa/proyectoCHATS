<?php

namespace App\Entity;

use App\Repository\InvitacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitacionRepository::class)]
class Invitacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\Column]
    private ?\DateTime $fechaEnvio = null;

    #[ORM\Column]
    private ?\DateTime $fechaExpiracion = null;

    #[ORM\Column(length: 255)]
    private ?string $mensaje = null;

    #[ORM\ManyToOne(inversedBy: 'invitacions')]
    private ?Chat $chat = null;

    #[ORM\ManyToOne(inversedBy: 'invitacions')]
    private ?User $invitador = null;

    #[ORM\ManyToOne(inversedBy: 'invitacions')]
    private ?User $invitado = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFechaEnvio(): ?\DateTime
    {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(\DateTime $fechaEnvio): static
    {
        $this->fechaEnvio = $fechaEnvio;

        return $this;
    }

    public function getFechaExpiracion(): ?\DateTime
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(\DateTime $fechaExpiracion): static
    {
        $this->fechaExpiracion = $fechaExpiracion;

        return $this;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): static
    {
        $this->mensaje = $mensaje;

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

    public function getInvitador(): ?User
    {
        return $this->invitador;
    }

    public function setInvitador(?User $invitador): static
    {
        $this->invitador = $invitador;

        return $this;
    }

    public function getInvitado(): ?User
    {
        return $this->invitado;
    }

    public function setInvitado(?User $invitado): static
    {
        $this->invitado = $invitado;

        return $this;
    }
}

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
}

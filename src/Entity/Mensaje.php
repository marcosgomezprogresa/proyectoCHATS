<?php

namespace App\Entity;

use App\Repository\MensajeRepository;
use App\Enum\TipoMensaje;
use App\Enum\EstadoMensaje;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MensajeRepository::class)]
class Mensaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contenido = null;

    #[ORM\Column(type: 'string', enumType: TipoMensaje::class)]
    private ?TipoMensaje $tipo = null;

    #[ORM\Column(type: 'string', enumType: EstadoMensaje::class)]
    private ?EstadoMensaje $estado = null;

    #[ORM\Column]
    private ?\DateTime $fechaHora = null;

    #[ORM\ManyToOne(inversedBy: 'mensajes')]
    private ?Chat $chat = null;

    #[ORM\ManyToOne(inversedBy: 'mensajes')]
    private ?User $remitente = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getTipo(): ?TipoMensaje
    {
        return $this->tipo;
    }

    public function setTipo(TipoMensaje $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getEstado(): ?EstadoMensaje
    {
        return $this->estado;
    }

    public function setEstado(EstadoMensaje $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFechaHora(): ?\DateTime
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTime $fechaHora): static
    {
        $this->fechaHora = $fechaHora;

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

    public function getRemitente(): ?User
    {
        return $this->remitente;
    }

    public function setRemitente(?User $remitente): static
    {
        $this->remitente = $remitente;

        return $this;
    }
}

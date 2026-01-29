<?php

namespace App\Entity;

use App\Repository\MensajeRepository;
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

    #[ORM\Column(length: 255)]
    private ?string $tipo = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\Column]
    private ?\DateTime $fechaHora = null;

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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
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

    public function getFechaHora(): ?\DateTime
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTime $fechaHora): static
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }
}

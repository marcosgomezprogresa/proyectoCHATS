<?php

namespace App\Entity;

use App\Repository\BloqueoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BloqueoRepository::class)]
class Bloqueo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $fechaBloqueo = null;

    #[ORM\Column(length: 255)]
    private ?string $motivo = null;

    #[ORM\Column]
    private ?bool $activo = null;

    #[ORM\ManyToOne(inversedBy: 'bloqueos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $bloqueador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaBloqueo(): ?\DateTime
    {
        return $this->fechaBloqueo;
    }

    public function setFechaBloqueo(\DateTime $fechaBloqueo): static
    {
        $this->fechaBloqueo = $fechaBloqueo;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): static
    {
        $this->motivo = $motivo;

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

    public function getBloqueador(): ?User
    {
        return $this->bloqueador;
    }

    public function setBloqueador(?User $bloqueador): static
    {
        $this->bloqueador = $bloqueador;

        return $this;
    }
}

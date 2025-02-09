<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\DetailCommandeStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeInterface;

#[ORM\Entity]
class DetailCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["detailCommande:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'detailsCommande')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(["detailCommande:read"])]
    private ?Commande $commande = null;
    
    #[ORM\ManyToOne(targetEntity: Plat::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(["detailCommande:read"])]
    private ?Plat $plat = null;

    #[ORM\Column(type: 'string', enumType: DetailCommandeStatus::class)]
    #[Groups(["detailCommande:read"])]
    private DetailCommandeStatus $status;
    
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["detailCommande:read"])]
    private ?DateTimeInterface $dateDeFinition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getPlat(): ?Plat
    {
        return $this->plat;
    }

    public function setPlat(?Plat $plat): self
    {
        $this->plat = $plat;
        return $this;
    }

    public function getStatus(): DetailCommandeStatus
    {
        return $this->status;
    }

    public function setStatus(DetailCommandeStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDateDeFinition(): ?DateTimeInterface
    {
        return $this->dateDeFinition;
    }

    public function setDateDeFinition(?DateTimeInterface $dateDeFinition): self
    {
        $this->dateDeFinition = $dateDeFinition;
        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\CommandeStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["commande:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(["commande:read"])]
    private ?User $client = null;

    #[ORM\Column(type: 'date')]
    #[Groups(["commande:read"])]
    private \DateTimeInterface $dateCommande;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(["commande:read"])]
    private float $montantTotal;

    #[ORM\Column(type: 'string', enumType: CommandeStatus::class)]
    #[Groups(["commande:read"])]
    private CommandeStatus $status;

    #[ORM\OneToMany(targetEntity: DetailCommande::class, mappedBy: 'commande', cascade: ['persist', 'remove'])]
    #[Groups(["commande:read"])]
    private Collection $detailsCommande;
    

    public function __construct()
    {
        $this->detailsCommande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getDateCommande(): \DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getMontantTotal(): float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): self
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getStatus(): CommandeStatus
    {
        return $this->status;
    }

    public function setStatus(CommandeStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDetailsCommande(): Collection
    {
        return $this->detailsCommande;
    }

    public function addDetailCommande(DetailCommande $detailCommande): self
    {
        if (!$this->detailsCommande->contains($detailCommande)) {
            $this->detailsCommande[] = $detailCommande;
            $detailCommande->setCommande($this);
        }
        return $this;
    }

    public function removeDetailCommande(DetailCommande $detailCommande): self
    {
        if ($this->detailsCommande->removeElement($detailCommande)) {
            if ($detailCommande->getCommande() === $this) {
                $detailCommande->setCommande(null);
            }
        }
        return $this;
    }
}

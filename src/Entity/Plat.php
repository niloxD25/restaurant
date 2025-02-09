<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["plat:read", "plat:write"])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["plat:read", "plat:write"])]
    private string $nomPlat;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(["plat:read", "plat:write"])]
    private string $prixUnitaire;

    #[ORM\Column(type: 'time')]
    #[Groups(["plat:read", "plat:write"])]
    private \DateTimeInterface $tempsCuisson;

    #[ORM\OneToMany(targetEntity: IngredientPlat::class, mappedBy: 'plat', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ingredientsPlats; // ❌ Supprimé des @Groups pour éviter la boucle infinie

    public function __construct()
    {
        $this->ingredientsPlats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPlat(): string
    {
        return $this->nomPlat;
    }

    public function setNomPlat(string $nomPlat): self
    {
        $this->nomPlat = $nomPlat;
        return $this;
    }

    public function getPrixUnitaire(): string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getTempsCuisson(): \DateTimeInterface
    {
        return $this->tempsCuisson;
    }

    public function setTempsCuisson(\DateTimeInterface $tempsCuisson): self
    {
        $this->tempsCuisson = $tempsCuisson;
        return $this;
    }

    public function getIngredientsPlats(): Collection
    {
        return $this->ingredientsPlats;
    }

    public function addIngredientPlat(IngredientPlat $ingredientPlat): self
    {
        if (!$this->ingredientsPlats->contains($ingredientPlat)) {
            $this->ingredientsPlats[] = $ingredientPlat;
            $ingredientPlat->setPlat($this);
        }
        return $this;
    }

    public function removeIngredientPlat(IngredientPlat $ingredientPlat): self
    {
        if ($this->ingredientsPlats->removeElement($ingredientPlat)) {
            if ($ingredientPlat->getPlat() === $this) {
                $ingredientPlat->setPlat(null);
            }
        }
        return $this;
    }
}

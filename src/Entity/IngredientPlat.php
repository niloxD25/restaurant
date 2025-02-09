<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class IngredientPlat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["ingredientPlat:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Plat::class, inversedBy: 'ingredientsPlats')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Plat $plat = null; // ❌ Supprimé de la sérialisation pour éviter la boucle infinie

    #[ORM\ManyToOne(targetEntity: Ingredient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(["ingredientPlat:read"])]
    private ?Ingredient $ingredient = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(["ingredientPlat:read"])]
    private int $quantite;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }
}

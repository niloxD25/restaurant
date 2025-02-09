<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nomIngredient;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nomImage;

    // #[ORM\OneToMany(targetEntity: IngredientPlat::class, mappedBy: 'ingredient', cascade: ['persist', 'remove'])]
    private Collection $ingredientsPlats;

    public function __construct()
    {
        $this->ingredientsPlats = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNomIngredient(): string
    {
        return $this->nomIngredient;
    }

    public function setNomIngredient(string $nomIngredient): self
    {
        $this->nomIngredient = $nomIngredient;
        return $this;
    }

    public function getNomImage(): string
    {
        return $this->nomImage;
    }

    public function setNomImage(string $nomImage): self
    {
        $this->nomImage = $nomImage;
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
            $ingredientPlat->setIngredient($this);
        }
        return $this;
    }

    public function removeIngredientPlat(IngredientPlat $ingredientPlat): self
    {
        if ($this->ingredientsPlats->removeElement($ingredientPlat)) {
            if ($ingredientPlat->getIngredient() === $this) {
                $ingredientPlat->setIngredient(null);
            }
        }
        return $this;
    }
}

<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\StockStatus;

#[ORM\Entity]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Ingredient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Ingredient $ingredient;

    #[ORM\Column(type: 'integer')]
    private int $quantite;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateStock;

    #[ORM\Column(type: 'string', enumType: StockStatus::class)]
    private StockStatus $status;

    public function getId(): int
    {
        return $this->id;
    }

    public function getIngredient(): Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(Ingredient $ingredient): self
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

    public function getDateStock(): \DateTimeInterface
    {
        return $this->dateStock;
    }

    public function setDateStock(\DateTimeInterface $dateStock): self
    {
        $this->dateStock = $dateStock;
        return $this;
    }

    public function getStatus(): StockStatus
    {
        return $this->status;
    }

    public function setStatus(StockStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}

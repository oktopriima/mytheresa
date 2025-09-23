<?php

namespace App\Entity;

use App\Enum\PriceRuleValueType;
use App\Repository\PriceRulesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRulesRepository::class)]
class PriceRules
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: PriceRuleValueType::class)]
    private ?PriceRuleValueType $value_type = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column]
    private ?int $amount = null;

    /**
     * @var Collection<int, PriceRuleConditions>
     */
    #[ORM\OneToMany(targetEntity: PriceRuleConditions::class, mappedBy: 'priceRules')]
    private Collection $conditions;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getValueType(): ?PriceRuleValueType
    {
        return $this->value_type;
    }

    public function setValueType(PriceRuleValueType $value_type): self
    {
        $this->value_type = $value_type;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Collection<int, PriceRuleConditions>
     */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    public function addCondition(PriceRuleConditions $condition): static
    {
        if (!$this->conditions->contains($condition)) {
            $this->conditions->add($condition);
            $condition->setPriceRules($this);
        }

        return $this;
    }

    public function removeCondition(PriceRuleConditions $condition): static
    {
        if ($this->conditions->removeElement($condition)) {
            // set the owning side to null (unless already changed)
            if ($condition->getPriceRules() === $this) {
                $condition->setPriceRules(null);
            }
        }

        return $this;
    }
}

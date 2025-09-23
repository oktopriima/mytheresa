<?php

namespace App\Entity;

use App\Repository\PriceRuleConditionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRuleConditionsRepository::class)]
class PriceRuleConditions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $condition_key = null;

    #[ORM\Column(length: 255)]
    private ?string $condition_value = null;

    #[ORM\ManyToOne(inversedBy: 'conditions')]
    private ?PriceRules $priceRules = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getConditionKey(): ?string
    {
        return $this->condition_key;
    }

    public function setConditionKey(string $condition_key): static
    {
        $this->condition_key = $condition_key;

        return $this;
    }

    public function getConditionValue(): ?string
    {
        return $this->condition_value;
    }

    public function setConditionValue(string $condition_value): static
    {
        $this->condition_value = $condition_value;

        return $this;
    }

    public function getPriceRules(): ?PriceRules
    {
        return $this->priceRules;
    }

    public function setPriceRules(?PriceRules $priceRules): static
    {
        $this->priceRules = $priceRules;

        return $this;
    }
}

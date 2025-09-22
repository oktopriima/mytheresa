<?php

namespace App\Library;

use App\Entity\PriceRules;
use App\Entity\Product;
use App\Enum\PriceRuleValueType;

class ProductPrices
{
    protected int $finalPrice;
    protected ?string $discountRemark = null;

    /**
     * @param PriceRules[] $priceRules
     * @param Product $product
     */
    public function __construct(protected array $priceRules, protected Product $product)
    {
        $this->finalPrice = $this->product->gePrice();
        $this->calculate();
    }

    public function call(): array
    {
        return [
            'original' => $this->product->gePrice(),
            'final' => $this->finalPrice,
            'discount_percentage' => $this->discountRemark,
            'currency' => 'EUR',
        ];
    }

    private function calculate(): void
    {
        if (count($this->priceRules) === 0) {
            return;
        }

        $discountAmount = 0;
        $discountRemark = null;
        foreach ($this->priceRules as $priceRule) {
            foreach ($priceRule->getConditions() as $condition) {
                $amt = 0;
                switch ($condition->getConditionKey()) {
                    case "sku":
                        if ($this->product->getSku() == $condition->getConditionValue()) {
                            $amt = $this->discountAmount($priceRule->getValueType(), $priceRule->getAmount());
                        }
                        break;
                    case "category":
                        if ($this->product->getCategory()->getName() == $condition->getConditionValue()) {
                            $amt = $this->discountAmount($priceRule->getValueType(), $priceRule->getAmount());
                        }
                        break;
                }

                if ($amt > $discountAmount) {
                    $discountAmount = $amt;
                    if ($priceRule->getValueType() == PriceRuleValueType::PERCENTAGE) {
                        $discountRemark = $priceRule->getAmount() . "%";
                    } else if ($priceRule->getValueType() == PriceRuleValueType::AMOUNT) {
                        $discountRemark = "Discount EUR " . $priceRule->getAmount();
                    }
                }
            }
        }

        $this->discountRemark = $discountRemark;
        $this->finalPrice = $this->finalPrice - $discountRemark;
    }


    private function discountAmount(string $type, int $amount): int
    {
        if ($type == PriceRuleValueType::PERCENTAGE) return ($amount * $this->product->gePrice()) / 100;
        if ($type == PriceRuleValueType::AMOUNT) return $amount;
        return 0;
    }
}

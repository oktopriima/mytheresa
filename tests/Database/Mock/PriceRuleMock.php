<?php

namespace App\Tests\Database\Mock;

use App\DataFixtures\PriceRuleFixtures;
use App\Entity\PriceRuleConditions;
use App\Entity\PriceRules;

class PriceRuleMock
{
    /**
     * @return PriceRules[]
     */
    public static function multiple(): array
    {
        $priceRules = [];
        foreach (PriceRuleFixtures::PRICE_RULES_EXAMPLE as $item) {
            $priceRule = new PriceRules();
            $priceRule->setValueType($item['type']);
            $priceRule->setAmount($item['amount']);
            $priceRule->setIsActive($item['is_active']);

            foreach ($item['conditions'] as $condition) {
                $cond = new PriceRuleConditions();
                $cond->setConditionKey($condition['condition_key']);
                $cond->setConditionValue($condition['condition_value']);
                $cond->setPriceRules($priceRule);

                $priceRule->setCondition($cond);
            }

            $priceRules[] = $priceRule;
        }

        return $priceRules;
    }
}

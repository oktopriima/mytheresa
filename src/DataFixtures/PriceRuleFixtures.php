<?php

namespace App\DataFixtures;

use App\Entity\PriceRuleConditions;
use App\Entity\PriceRules;
use App\Enum\PriceRuleValueType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PriceRuleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'type' => PriceRuleValueType::PERCENTAGE,
                'is_active' => true,
                'amount' => 30,
                'conditions' => [
                    [
                        'condition_key' => 'sku',
                        'condition_value' => '000003',
                    ]
                ],
            ],
            [
                'type' => PriceRuleValueType::PERCENTAGE,
                'is_active' => true,
                'amount' => 15,
                'conditions' => [
                    [
                        'condition_key' => 'category',
                        'condition_value' => 'boots',
                    ],
                    [
                        'condition_key' => 'category',
                        'condition_value' => 'sandals',
                    ],
                    [
                        'condition_key' => 'category',
                        'condition_value' => 'sneakers',
                    ]
                ],
            ]
        ];

        foreach ($data as $item) {
            $p = new PriceRules();
            $p->setValueType($item['type']);
            $p->setIsActive($item['is_active']);
            $p->setAmount($item['amount']);
            $manager->persist($p);

            foreach ($item['conditions'] as $condition) {
                $c = new PriceRuleConditions();
                $c->setConditionKey($condition['condition_key']);
                $c->setConditionValue($condition['condition_value']);
                $c->setPriceRules($p);

                $manager->persist($c);
            }
        }

        $manager->flush();
    }
}

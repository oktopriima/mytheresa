<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            ["sku" => "000001", "name" => "BV Lean leather ankle boots", "category" => "boots", "price" => 89000],
            ["sku" => "000002", "name" => "BV Lean leather ankle boots", "category" => "boots", "price" => 99000],
            ["sku" => "000003", "name" => "Ashlington leather ankle boots", "category" => "boots", "price" => 71000],
            ["sku" => "000004", "name" => "Naima embellished suede sandals", "category" => "sandals", "price" => 79500],
            ["sku" => "000005", "name" => "Nathane leather sneakers", "category" => "sneakers", "price" => 59000],
            ["sku" => "000006", "name" => "Classic white running sneakers", "category" => "sneakers", "price" => 62000],
            ["sku" => "000007", "name" => "Premium leather crossbody bag", "category" => "bags", "price" => 125000],
            ["sku" => "000008", "name" => "Mini clutch evening bag", "category" => "bags", "price" => 87000],
            ["sku" => "000009", "name" => "Oversized wool blend coat", "category" => "jackets", "price" => 210000],
            ["sku" => "000010", "name" => "Denim cropped jacket", "category" => "jackets", "price" => 145000],
            ["sku" => "000011", "name" => "Slim-fit cotton shirt", "category" => "shirts", "price" => 48000],
            ["sku" => "000012", "name" => "Silk printed blouse", "category" => "shirts", "price" => 99000],
            ["sku" => "000013", "name" => "Linen summer shorts", "category" => "shorts", "price" => 53000],
            ["sku" => "000014", "name" => "Tailored wool trousers", "category" => "pants", "price" => 135000],
            ["sku" => "000015", "name" => "Casual cotton pants", "category" => "pants", "price" => 78000],
        ];

        // Keep categories in memory (avoid duplicates)
        $categoriesCache = [];

        foreach ($data as $item) {
            $product = new Product();
            $product->setSku($item['sku']);
            $product->setName($item['name']);
            $product->setPrice($item['price']);

            if (!isset($categoriesCache[$item['category']])) {
                $category = new Categories();
                $category->setName($item['category']);
                $manager->persist($category);

                $categoriesCache[$item['category']] = $category;
            }

            $product->setCategories($categoriesCache[$item['category']]);
            $manager->persist($product);
        }

        $manager->flush();
    }
}

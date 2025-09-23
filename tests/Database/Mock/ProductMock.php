<?php

namespace App\Tests\Database\Mock;

use App\DataFixtures\ProductFixtures;
use App\Entity\Categories;
use App\Entity\Product;

class ProductMock
{
    /**
     * @return Product[]
     */
    public static function multiple(): array
    {
        $products = [];
        $categoriesCache = [];
        foreach (ProductFixtures::PRODUCTS_EXAMPLE as $data) {
            $product = new Product();
            $product->setSku($data['sku']);
            $product->setName($data['name']);
            $product->setPrice($data['price']);

            if (!isset($categoriesCache[$data['category']])) {
                $category = new Categories();
                $category->setName($data['category']);
                $categoriesCache[$data['category']] = $category;
            }
            $product->setCategories($categoriesCache[$data['category']]);
            $products[] = $product;
        }
        return $products;
    }
}

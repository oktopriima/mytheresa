<?php

namespace App\Tests\Library;

use App\Entity\Categories;
use App\Library\ProductPrices;
use App\Tests\Database\Mock\PriceRuleMock;
use App\Tests\Database\Mock\ProductMock;
use PHPUnit\Framework\TestCase;

class ProductPriceTest extends TestCase
{
    /**
     * @description
     * Test one impact discount on a product.
     * The product will get a discount when sku is 0000003 or product category is boots, sandals, and sneakers.
     * sku 0000003 discount 30%.
     * category discount 15%.
     * Expected to get 15% Discount
     */
    public function testOneImpactDiscount(): void
    {
        $product = ProductMock::single();
        $product->setSku("0000001");

        $category = new Categories();
        $category->setName('boots');
        $product->setCategories($category);

        $priceRules = PriceRuleMock::multiple();

        $price = (new ProductPrices($priceRules, $product))->call();

        $this->assertNotEmpty($price);
        $this->assertIsArray($price);
        $this->assertTrue($price["original"] > $price["final"]);
        $this->assertEquals("EUR", $price["currency"]);
        $this->assertNotNull($price["discount_percentage"]);
        $this->assertEquals("15%", $price["discount_percentage"]);
    }

    /**
     * @description
     * Test on double impact discount on a product.
     * The product will get a discount when sku is 0000003 or product category is boots, sandals, and sneakers.
     * sku 0000003 discount 30%.
     * category discount 15%.
     * Expected to get a bigger discount number.
     * Expected to get 30% Discount.
     */
    public function testDoubleImpactDiscount(): void
    {
        $product = ProductMock::single();
        $product->setSku("0000003");

        $category = new Categories();
        $category->setName('boots');
        $product->setCategories($category);

        $priceRules = PriceRuleMock::multiple();

        $price = (new ProductPrices($priceRules, $product))->call();

        $this->assertNotEmpty($price);
        $this->assertIsArray($price);
        $this->assertTrue($price["original"] > $price["final"]);
        $this->assertEquals("EUR", $price["currency"]);
        $this->assertNotNull($price["discount_percentage"]);
        $this->assertEquals("30%", $price["discount_percentage"]);
    }

    /**
     * @description
     * Test without any discount on product.
     * Expected to get no discount
     */
    public function testWithoutDiscount(): void
    {
        $product = ProductMock::single();
        $price = (new ProductPrices([], $product))->call();
        $this->assertNotEmpty($price);
        $this->assertIsArray($price);
        $this->assertTrue($price["original"] == $price["final"]);
        $this->assertEquals("EUR", $price["currency"]);
        $this->assertNull($price["discount_percentage"]);
    }

    /**
     * @description
     * Test no impact discount on a product.
     * The product will get a discount when sku is 0000003 or product category is boots, sandals, and sneakers.
     * sku discount 30%.
     * category discount 15%.
     * Expected to get no discount.
     */
    public function testNoImpactOnDiscount(): void
    {
        $product = ProductMock::single();
        $product->setSku("0000001");

        $category = new Categories();
        $category->setName('pants');
        $product->setCategories($category);

        $priceRules = PriceRuleMock::multiple();

        $price = (new ProductPrices($priceRules, $product))->call();

        $this->assertNotEmpty($price);
        $this->assertIsArray($price);
        $this->assertTrue($price["original"] == $price["final"]);
        $this->assertEquals("EUR", $price["currency"]);
        $this->assertNull($price["discount_percentage"]);
    }
}

<?php

namespace App\Request\Product;

class ListResponse
{
    public function __construct(
        public string $sku,
        public string $name,
        public string $category,
        public array $price
    ) {}
}

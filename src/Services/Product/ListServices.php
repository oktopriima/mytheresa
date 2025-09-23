<?php

namespace App\Services\Product;

use App\Contract\AbstractServices;
use App\Contract\Interfaces\ServiceResponseInterface;
use App\Library\ProductPrices;
use App\Repository\PriceRulesRepository;
use App\Repository\ProductRepository;
use App\Request\Product\ListRequest;
use App\Request\Product\ListResponse;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ListServices extends AbstractServices
{
    public function __construct(
        private readonly ProductRepository    $productRepository,
        private readonly PriceRulesRepository $priceRulesRepository,
        public DenormalizerInterface          $denormalizer,
        public ValidatorInterface             $validator,
    )
    {
        parent::__construct($this->denormalizer, $this->validator);
    }

    public function call(array $params = []): ServiceResponseInterface
    {
        $errors = $this->validate($params, ListRequest::class);
        if (count($errors) > 0) {
            return self::error($errors, 'Error validate the request');
        }

        $priceRules = $this->priceRulesRepository->findByIsActive();
        $products = $this->productRepository->findByParams($this->dto);

        $result = array_map(fn($product) => new ListResponse(
            $product->getSku(),
            $product->getName(),
            $product->getCategories()->getName(),
            ((new ProductPrices($priceRules, $product))->call()),
        ), $products);

        return self::success($result);
    }
}
